<?php

use Kirby\Cms\App;

return [
	'github-repositories' => [
		'merx' => 'https://github.com/wagnerwagner/merx',
		'kirby' => 'https://github.com/getkirby/kirby',
	],
	'merx-email' => 'merx@wagnerwagner.de',
	'wagnerwagner.merx.production' => true,
	'wagnerwagner.merx.pricingRules' => [
		'default' => [
			'currency' => 'EUR',
			'taxIncluded' => false,
		],
	],
	'wagnerwagner.merx.taxRules' => [
		'default' => [
			'rate' => function (App|null $kirby): float|null {
				$region = $kirby->session()->get('region');
				if (strtolower((string)$region) === 'de') {
					return 0.19;
				}
				return null;
			},
		],
	],
	'wagnerwagner.merx.stripe.paymentIntentParameters' => [
		'capture_method' => 'automatic_async',
		'payment_method_options' => [
			'card' => ['capture_method' => 'manual'],
		],
	],
	'cache.site.search' => true,
	'cache' => [
		'pages' => [
			'active' => true,
			'type' => 'static',
			'root' => __DIR__ . '/../../public/pages-cache',
			// Kirby neither writes the cache for a logged-in user
			'ignore' => function ($page) {
				return $page->template()->name() === 'checkout' || $page->kirby()->user() !== null;
			}
		],
	],
];
