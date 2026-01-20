<?php
return function ($page) {
	$cart = $page->cart();

	$quantity = 0;
	try {
		$quantity = (int)$cart->findBy('id', 'merx-license')['quantity'];
	} catch (Exception) {}

	return [
		'cart' => $cart,
		'quantity' => $quantity,
		'licenses' => explode(', ', $page->licenses()),
	];
};
