<?php

use Kirby\Cms\App;
use Kirby\Cms\Pagination;
use Kirby\Toolkit\Str;

return function (App $kirby) {
	$page = param('page', 1);
	$query = Str::trim(get('q', ''));

	try {
		$results = $kirby->api()->call('search', 'GET', [
			'query' => [
				'q' => $query,
				'limit' => 50,
				'page' => $page,
			],
		]);

		$total =  $results['pagination']['total'] ?? null;
		if ($total) {
			$message = $total . ' results';
		}

	} catch (Throwable $ex) {
		$message = $ex->getMessage();
	}

	return [
		'query' => $query,
		'results' => $results ?? null,
		'message' => $message ?? $results['message'] ?? null,
		'data' => $results['data'] ?? null,
		'pagination' => new Pagination($results['pagination'] ?? []),
	];

};
