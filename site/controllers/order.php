<?php

/**
 * The invoice is shown on the order page and built as PDF from the same data
 */
return function (OrderPage $page) {
	$cart = $page->cart();
	$total = $cart->total();
	$countries = option('wagnerwagner.site.countryList');
	$seller = option('wagnerwagner.site.seller');

	$items = [];
	foreach ($cart as $item) {
		/** @var \Wagnerwagner\Merx\ListItem $item */
		$items[] = [
			(string)$item->title,
			(string)$item->quantity,
			$item->price?->toString('priceNet'),
			$item->total()?->toString('priceNet'),
		];
	}

	$totals = [];
	if ($cart->taxRates() !== []) {
		$totals[] = ['Subtotal', $total?->toString('priceNet')];
		foreach ($cart->taxRates() as $tax) {
			$totals[] = ['VAT ' . $tax->rate(), $tax->toString()];
		}
	}

	return [
		'licenseCount' => (int)$cart->quantity(),
		'licenses' => $page->licenses()->split(','),
		'invoice' => [
			'number' => $page->orderNumber()->value(),
			'filename' => 'merx-invoice-' . $page->orderNumber() . '.pdf',
			'buyer' => array_values(array_filter([
				$page->company()->value(),
				$page->name()->value(),
				$page->street()->value(),
				$page->postal_code() . ' ' . $page->city(),
				$page->state()->value(),
				$countries[(string)$page->country()] ?? null,
			])),
			'email' => $page->email()->value(),
			'details' => [
				['Invoice number', $page->orderNumber()->value()],
				['Date', $page->dateCreated()->toDate('F j, Y')],
				['Payment method', $page->paymentMethodName()],
			],
			'items' => $items,
			'totals' => $totals,
			'total' => $total?->toString('price'),
			'notice' => $page->vatNotice(),
			'seller' => [
				'address' => [
					$seller['name'],
					$seller['street'],
					$seller['postalCode'] . ' ' . $seller['city'],
					$countries[$seller['country']],
				],
				'email' => option('merx-email'),
				'tax' => [
					$seller['register'],
					'Tax number ' . $seller['taxNumber'],
					'VAT identification number ' . $seller['vatId'],
				],
			],
			'xml' => $page->eInvoiceXml(),
		],
	];
};
