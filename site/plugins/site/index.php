<?php

use Kirby\Cms\App;
use Kirby\Cms\Collection;
use Kirby\Cms\Page;
use Kirby\Http\Response;
use Kirby\Toolkit\A;
use Kirby\Toolkit\Html;
use Kirby\Toolkit\Obj;
use Kirby\Toolkit\Str;
use Wagnerwagner\Merx\Cart;
use Wagnerwagner\Merx\Logger;
use Wagnerwagner\Site\Licenses;
use Wagnerwagner\Site\Llms;
use Wagnerwagner\Site\TypeScript;

@include_once __DIR__ . '/vendor/autoload.php';
require_once 'country-list.php';

function sendConfirmationMail(OrderPage $orderPage)
{
	kirby()->email([
		'from' => option('merx-email'),
		'to' => (string)$orderPage->email(),
		'subject' => 'Your Merx Order',
		'template' => 'confirmation',
		'data' => [
			'page' => $orderPage,
		],
	]);
}


App::plugin('wagnerwagner/site', [
	'options' => [
		'countryList' => $countryList,
		// Seller on every invoice: the order page, its PDF and its XML
		'seller' => [
			'name' => 'Wagnerwagner GmbH',
			'street' => 'Burkhardt+Weber-Straße 59',
			'postalCode' => '72760',
			'city' => 'Reutlingen',
			'country' => 'DE',
			'register' => 'HRB Stuttgart 354262',
			'taxNumber' => '78092 / 68991',
			'vatId' => 'DE231029364',
		],
	],
	'pageMethods' => [
		'seoTitle' => function (): string {
			if ($this->isHomePage()) {
				return $this->title();
			}
			return $this->title() . ' – ' . $this->site()->title();
		},
	],
	'hooks' => [
		/**
		 * The license keys are issued once the order is paid, which is when
		 * it is created for a card or PayPal, and later for a SEPA debit
		 */
		'wagnerwagner.merx.orderPaid' => function (OrderPage $orderPage): OrderPage {
			// Paid a second time, e.g. by a Panel user who set the date again
			if ($orderPage->licenses()->isNotEmpty()) {
				return $orderPage;
			}

			$orderPage = $orderPage->update([
				'licenses' => A::join(Licenses::issue((int)$orderPage->cart()->get('merx-license')?->quantity)),
			]);

			// The order is paid and shows the keys as well. An email which
			// cannot be sent must not send the customer back to the checkout.
			try {
				sendConfirmationMail($orderPage);
			} catch (Throwable $ex) {
				Logger::log('Confirmation email of order "' . $orderPage->uid() . '" was not sent: ' . $ex->getMessage(), 'error');
			}

			return $orderPage;
		},
		/**
		 * The invoice is issued with the order, so its e-invoice XML is written
		 * now and stays as it is
		 */
		'wagnerwagner.merx.createOrder:after' => function (OrderPage $orderPage): OrderPage {
			$orderPage->eInvoiceXml();
			return $orderPage;
		},
		/**
		 * VAT follows the region in the session (see `taxRules` in the config),
		 * so the billing country has to be the one the cart was priced for.
		 */
		'wagnerwagner.merx.initializeOrder:before' => function (Cart $cart, array $data) {
			// Before the customer pays: Merx would not accept a key it does not know.
			// A cart without a license, such as the one of the API reference
			// examples, needs no key.
			$quantity = (int)$cart->get('merx-license')?->quantity;
			if ($quantity > 0 && Licenses::available($quantity) === false) {
				throw new Kirby\Exception\Exception(
					key: 'site.checkout.licenses',
					fallback: 'We cannot issue license keys at the moment. Please write to ' . option('merx-email') . '.',
					httpCode: 503,
				);
			}

			$country = Str::upper((string)($data['country'] ?? ''));
			$region = Str::upper((string)kirby()->session()->get('region'));

			// A missing country is left to the blueprint, which reports it
			// together with the other required fields
			if ($country !== '' && $country !== $region) {
				// Merx passes on a `Kirby\Exception\Exception` as it is, other
				// exceptions become a generic error
				throw new Kirby\Exception\Exception(
					key: 'site.checkout.country',
					fallback: 'Your billing country changed. Please check the price and submit your order again.',
					httpCode: 400,
				);
			}
		},
	],
	'api' => [
		'routes' => [
			[
				/**
				 * Sets the billing country, which decides the VAT, and
				 * returns the cart priced for it
				 */
				'pattern' => 'checkout/region',
				'method' => 'POST',
				'auth' => false,
				'action' => function () {
					/** @var \Kirby\Api\Api $this */
					$country = Str::upper((string)$this->requestBody('country'));

					if (array_key_exists($country, option('wagnerwagner.site.countryList')) === false) {
						throw new Kirby\Exception\InvalidArgumentException(
							key: 'site.checkout.unknownCountry',
							fallback: 'Please choose a country from the list.',
						);
					}

					$this->kirby()->session()->set('region', $country);

					return $this->cart();
				},
			],
		],
	],
	'areas' => [
		// Kirby merges the buttons of every area, like those of Merx in `site`
		'site' => fn (): array => [
			'buttons' => [
				/**
				 * Downloads the e-invoice XML of an order which has one
				 */
				'eInvoice' => function (Page $page): array|null {
					if ($page instanceof OrderPage === false || $page->isDraft() === true || $page->hasEInvoice() === false) {
						return null;
					}

					return [
						'icon' => 'code',
						'text' => 'E-invoice (XML)',
						'link' => $page->url() . '.xml',
					];
				},
			],
		],
	],
	'components'	 => include __DIR__ . '/extensions/components.php',
	'routes' => [
		[
			'pattern' => 'sitemap.xml',
			'action' => function () {
				return new Page([
					'slug' => 'sitemap',
					'template' => 'sitemap',
				]);
			},
		],
		[
			/**
			 * Index of the whole documentation for LLMs, linking every
			 * page as Markdown.
			 *
			 * @see https://llmstxt.org
			 */
			'pattern' => 'llms.txt',
			'action' => function () {
				return new Response(Llms::index(), 'text/plain');
			},
		],
		[
			/**
			 * TypeScript interfaces of every API model
			 */
			'pattern' => 'reference/api/models/merx.d.ts',
			'action' => function () {
				return new Response(TypeScript::declarations(), 'text/plain');
			},
		],
		[
			/**
			 * Markdown representation of a documentation page, e.g.
			 * /guide/getting-started/installation.md
			 */
			'pattern' => '(:all).md',
			'action' => function (string $id) {
				$page = Llms::find($id);

				// Not a documentation page: let Kirby handle the
				// request, which ends in the error page.
				if ($page === null) {
					return null;
				}

				return new Response(Llms::page($page), 'text/markdown');
			},
		],
	],
	'fieldMethods' => [
		/**
		 * Extract headlines from the field value and return a collection of them
		 */
		'toToc' => function (\Kirby\Content\Field $field, string $headline = 'h2'): Collection {
			$value = $field->value() ?? '';

			// Make sure not to include sceencast boxes
			$value = preg_replace('$\(screencast:.*\)$', '', $value);

			preg_match_all(
				'!<' . $headline . '.*?>(.*?)</' . $headline . '>!s',
				$field->value($value)->kt()->value(),
				$matches,
			);

			$headlines = new Collection();

			foreach ($matches[1] as $text) {
				$headline = new Obj([
					'id'	 => '#' . Str::slug(Str::unhtml($text)),
					'text' => trim(strip_tags($text)),
				]);

				$headlines->append($headline->id(), $headline);
			}

			return $headlines;
		},
	],
]);
