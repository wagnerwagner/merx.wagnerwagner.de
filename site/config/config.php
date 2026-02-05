<?php

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
		]
	],
	'wagnerwagner.merx.stripe.paymentIntentParameters' => [
		'payment_method_types' => ['card'],
	],
	'cache.site.search' => true,
];
