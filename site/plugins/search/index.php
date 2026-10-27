<?php

namespace Site\Search;

use Kirby\Cms\App;
use Kirby\Cms\Page;
use Kirby\Exception\Exception;
use Kirby\Toolkit\Collection;
use Kirby\Toolkit\Config;
use Kirby\Toolkit\I18n;
use Kirby\Toolkit\Str;

/**
 * A page found by the search
 *
 * The API resolves objects by type. Wrapping the page keeps the
 * `searchresult` model to search results, so any other page the
 * API returns still gets Kirby’s own `page` model.
 */
class SearchResult
{
	public function __construct(
		public readonly Page $page,
	) {
	}
}

class SearchResults extends Collection
{
}

App::plugin(
	name: 'site/search',
	info: [
		'authors' => [['name' => 'Tobias Wolf', 'email' => 'tobias.wolf@wagnerwagner.de']],
	],
	version: '2025-03',
	extends: [
		'api' => [
			'models' => [
				'searchresult' => [
					'fields' => [
						'url' => fn (SearchResult $result) => $result->page->url(),
						'title' => fn (SearchResult $result) => $result->page->metaTitle()->or($result->page->title())->toString(),
						'breadcrumbs' => fn (SearchResult $result) => $result->page->parents()->flip()->add($result->page)->values(fn (Page $page) => $page->title()->toString()),
						'type' => fn (SearchResult $result) => $result->page->template()->name(),
					],
					'type' => SearchResult::class,
					'views' => [
						'default' => [
							'url',
							'title',
							'breadcrumbs',
							'type',
						],
					],
				],
			],
			'collections' => [
				'searchresults' => [
					'model' => 'searchresult',
					'type' => SearchResults::class,
					'view' => 'compact',
				],
			],
			'routes' => function (App $kirby) {
				return [
					[
						'pattern' => 'search',
						'auth' => false,
						'action'  => function () use ($kirby) {
							$kirby->impersonate('nobody');

							$query = $this->requestQuery('q', '');
							$query = Str::lower(Str::trim($query));

							I18n::$locale = $this->language();

							if (Str::length($query) < 3) {
								throw new Exception(key: 'site.search.length', httpCode: 400);
							}

							$searchCache = $kirby->cache('site.search');
							$cachedPages = $searchCache->get($query);

							if (is_array($cachedPages)) {
								serverTimingStart('search.cache');
								$results = pages($cachedPages);
								serverTimingEnd('search.cache');
							} else {
								/** @var Pages $results */
								serverTimingStart('search.index');
								$results = $kirby->site()->index()->listed();
								serverTimingEnd('search.index');

								serverTimingStart('search.search');
								$results = $results->search($query);
								serverTimingEnd('search.search');

								$searchCache->set($query, $results->pluck('id'));
							}

							$results = new SearchResults($results->values(fn (Page $page) => new SearchResult($page)));

							$sums = Config::get('tobiaswolf.server-timing.data.sum');
							$strings = [];
							foreach ($sums as $name => $dur) {
								$strings[] = $name . ';dur=' . $dur * 1000;
							}
							header('Server-Timing: ' . implode(', ', $strings));

							if ($results->count() === 0) {
								return [
									'code' => 200,
									'status' => 'info',
									'message' => t('search.results.none'),
								];
							}

							return $results;
						},
					],
				];
			},
		],
		'translations' => [
			'en' => [
				'error.site.search.length' => 'Search query must be at least 3 characters long.',
			],
		],
		'hooks' => [
			'page.*:after' => function (Page|null $newPage) {
				if ($newPage ?? false) {
					$newPage->kirby()->cache('site.search')->flush();
				}
			},
		],
	],
);
