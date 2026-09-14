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
			'rule' => function (?App $kirby): float {
				$region = $kirby->session()->get('region');
				if (strtolower($region) === 'de') {
					return 0.19;
				}
				return 0;
			},
		],
	],
	'wagnerwagner.merx.stripe.paymentIntentParameters' => [
		'payment_method_types' => ['card', 'ideal'],
		'automatic_payment_methods' => ['enabled' => false],
		'capture_method' => 'automatic_async',
		'payment_method_options' => [
			'card' => ['capture_method' => 'manual'],
		],
	],
	'cache.site.search' => true,
];
