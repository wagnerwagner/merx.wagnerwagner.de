<?php

use Kirby\Cms\App;

return function (App $kirby) {
	try {
		$results = $kirby->api()->call('search', 'GET', [
			'query' => [
				'q' => get('q'),
			],
		]);
	} catch (Throwable $ex) {
		$message = $ex->getMessage();
	}

	return [
		'results' => $results ?? null,
		'message' => $message ?? $results['message'] ?? null,
		'data' => $results['data'] ?? null,
	];

};
