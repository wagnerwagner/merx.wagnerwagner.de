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

/**
 * Create a element with link to class documentation
 */
function classLink(string $class, bool $codeBlock = true): string
{
	$link = '';
	$slugs = array_map(fn ($item) => Str::kebab($item), Str::split($class, '\\'));
	$slug = implode('/', $slugs);
	if (Str::startsWith($class, 'Kirby\\')) {
		$slug = Str::replace($slug, 'kirby/', '');
		$link = 'https://getkirby.com/docs/reference/objects/' . $slug;
	} else {
		$slug = Str::replace($slug, 'wagnerwagner/merx/', '');
		$link = url('/reference/classes/' . $slug);
	}
	$content = $codeBlock ? ['<code>' . $class . '</code>'] : $class;
	return Html::tag('a', $content, [
		'href' => $link,
	]);
}

function niceExcerpt($string, $query)
{
	$start = stripos($string, $query) - 100;
	$start = $start < 0 ? 0 : $start;
	if ($start > 0) {
		$string = Str::substr($string, $start);
		$string = '… ' . Str::after($string, ' ');
	} else {
		$string = Str::substr($string, 0);
	}
	$excerpt = Str::excerpt($string, 200 + Str::length($query));
	return $excerpt;
}


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
	],
	'pageMethods' => [
		'seoTitle' => function (): string {
			if ($this->isHomePage()) {
				return $this->title();
			}
			return $this->title() . ' – ' . $this->site()->title();
		},
	],
	'siteMethods' => [
		'prettySearch' => function ($query = ''): Kirby\Cms\Pages {
			$sitesToSearch = $this->find('docs', 'cookbooks')->index()->listed()->filterBy('intendedTemplate', '!=', 'order');
			$result = $sitesToSearch->search($query);
			$result->map(function ($page) use ($query) {
				$contentCollection = new Kirby\Toolkit\Collection($page->content()->data());
				$pageResults = $contentCollection->filter(function ($page) use ($query) {
					return Str::contains(strtolower($page), strtolower($query));
				});
				$excerpt = $pageResults->first();
				if ($pageResults->findByKey('excerpt')) {
					$excerpt = $pageResults->findByKey('excerpt');
				} elseif ($pageResults->findByKey('text')) {
					$excerpt = $pageResults->findByKey('text');
				}
				$excerpt = niceExcerpt(HTML::decode(kirbytext($excerpt)), $query);
				if ($excerpt !== (string)$page->title()) {
					$page->content()->update([
						'excerpt' => preg_replace('/' . $query . '/i', '<b>${0}</b>', $excerpt),
					]);
				}
				return $page;
			});
			return $result;
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
		 * VAT follows the region in the session (see `taxRules` in the config),
		 * so the billing country has to be the one the cart was priced for.
		 */
		'wagnerwagner.merx.initializeOrder:before' => function (Cart $cart, array $data) {
			// Before the customer pays: Merx would not accept a key it does not know
			if (Licenses::available((int)$cart->get('merx-license')?->quantity) === false) {
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
