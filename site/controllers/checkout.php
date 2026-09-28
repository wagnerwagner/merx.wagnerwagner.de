<?php

use Kirby\Cms\App;

return function (App $kirby) {
	// The billing country decides the VAT (see `taxRules` in the config). The
	// browser’s region is only a first guess until the customer picks one.
	$region = $kirby->visitor()->acceptedLanguages()->first()?->region();
	if (array_key_exists((string)$region, option('wagnerwagner.site.countryList')) === false) {
		$region = null;
	}
	$kirby->session()->set('region', $region);

	// Keyed by the item’s key: a plain list would store it as `0`, and
	// `updateItem('merx-license')` would not find it
	$cart = cart([
		'merx-license' => ['key' => 'merx-license'],
	]);

	return [
		'cart' => $cart,
		'region' => $region,
	];
};
