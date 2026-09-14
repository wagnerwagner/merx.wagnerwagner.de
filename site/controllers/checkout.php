<?php

use Kirby\Cms\App;

return function (App $kirby) {
	$region = $kirby->visitor()->acceptedLanguages()->first()?->region();
	$kirby->session()->set('region', $region);
	$cart = cart([[
		'key' => 'merx-license',
	]]);
	return [
		'cart' => $cart,
		'region' => $region,
	];
};
