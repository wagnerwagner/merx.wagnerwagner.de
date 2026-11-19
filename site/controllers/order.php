<?php

use Wagnerwagner\Merx\OrderPage;

return function (OrderPage $page) {
	return [
		'cart' => $page->cart(),
		'licenseCount' => $page->cart()->quantity(),
		'licenses' => $page->licenses()->split(','),
	];
};
