<?php

use Wagnerwagner\Merx\OrderPage;

return function (OrderPage $page) {
	return [
		'cart' => $page->cart(),
		'licenseCount' => (int)$page->cart()->quantity(),
		'licenses' => $page->licenses()->split(','),
	];
};
