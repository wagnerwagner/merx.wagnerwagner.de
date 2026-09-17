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
		]
	],
	'wagnerwagner.merx.taxRules' => [
		'default' => [
			'rule' => function (?App $kirby): ?float {
				$region = $kirby->session()->get('region');
				if (strtolower($region) === 'de') {
					return 0.19;
				}
				return null;
			},
		],
	],
	'wagnerwagner.merx.stripe.paymentIntentParameters' => [
		'automatic_payment_methods' => null,
		'capture_method' => 'automatic_async',
		'payment_method_options' => [
			'card' => ['capture_method' => 'manual'],
		],
	],
	'cache.site.search' => true,
];
