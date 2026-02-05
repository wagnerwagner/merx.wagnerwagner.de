<?php

use Wagnerwagner\Merx\OrderPage;

return function (OrderPage $page) {
	$cart = $page->cart();

	$quantity = 0;
	try {
		$quantity = (int)$cart->findBy('id', 'merx-license')['quantity'];
	} catch (Exception) {}

	return [
		'cart' => $cart ?? null,
		'quantity' => $quantity,
		'licenses' => explode(', ', $page->licenses()),
	];
};
