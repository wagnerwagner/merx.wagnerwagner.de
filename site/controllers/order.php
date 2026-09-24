<?php

use Wagnerwagner\Merx\OrderPage;

return function (OrderPage $page) {
	return [
		'cart' => $page->cart(),
		'licenses' => $page->licenses()->split(','),
	];
};
