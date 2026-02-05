<?php

use Kirby\Cms\App;
use Kirby\Cms\Collection;
use Kirby\Cms\Page;
use Kirby\Toolkit\A;
use Kirby\Toolkit\Str;
use Kirby\Toolkit\Html;
use Kirby\Toolkit\Obj;

@include_once __DIR__ . '/vendor/autoload.php';
require_once 'country-list.php';
$licenseBase = option('wagnerwagner.site.licenseBase');

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

function niceExcerpt($string, $query) {
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


function sendConfirmationMail(OrderPage $orderPage) {
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
		'seoTitle' => function (): string
		{
			if ($this->isHomePage()) {
				return $this->title();
			}
			return $this->title() . ' – ' . $this->site()->title();
		}
	],
	'siteMethods' => [
		'prettySearch' => function ($query = ''): Kirby\Cms\Pages
		{
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
				} else if ($pageResults->findByKey('text')) {
					$excerpt = $pageResults->findByKey('text');
				}
				$excerpt = niceExcerpt(HTML::decode(kirbytext($excerpt)), $query);
				if ($excerpt !== (string)$page->title()) {
					$page->content()->update([
						'excerpt' => preg_replace('/'.$query.'/i', '<b>${0}</b>', $excerpt),
					]);
				}
				return $page;
			});
			return $result;
		}
	],
	'hooks' => [
		'wagnerwagner.merx.completePayment:after' => function($orderPage) use ($licenseBase) {
			$quantity = $orderPage->cart()->findBy('id', 'merx-license')['quantity'];
			$licenses = [];
			while (count($licenses) < $quantity) {
				$licenses[] = 'MERX-' . strtoupper(dechex(A::join(A::shuffle(str_split((int)$licenseBase)), '')) . '-' . dechex(A::join(A::shuffle(str_split((int)$licenseBase)), '')));
			}
			$orderPage = $orderPage->update([
				'licenses' => A::join($licenses),
			]);
			sendConfirmationMail($orderPage);
		},
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
	],
	'fieldMethods' => [
		/**
		 * Extract headlines from the field value and return a collection of them
		 */
		'toToc' => function (\Kirby\Content\Field $field, string $headline = 'h2'): Collection
		{
			$value = $field->value() ?? '';

			// Make sure not to include sceencast boxes
			$value = preg_replace('$\(screencast:.*\)$', '', $value);

			preg_match_all(
				'!<' . $headline . '.*?>(.*?)</' . $headline . '>!s',
				$field->value($value)->kt()->value(),
				$matches
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
