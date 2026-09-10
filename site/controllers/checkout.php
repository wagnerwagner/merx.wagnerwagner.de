<?php

return function () {
	$cart = merx()->cart(['merx-license']);
	return [
		'cart' => $cart,
		'region' => kirby()->visitor()->acceptedLanguages()->first()?->region(),
	];
};
