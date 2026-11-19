<?php
/**
 * The e-invoice XML, the same as in the PDF of the order page
 *
 * @var OrderPage $page
 */

use Kirby\Exception\NotFoundException;
use Kirby\Http\Header;

$xml = $page->eInvoiceXml();

// Only a customer in Germany gets an e-invoice
if ($xml === null) {
	throw new NotFoundException();
}

Header::download([
	'name' => 'invoice-' . $page->orderNumber() . '.xml',
	'mime' => 'text/xml',
]);

echo $xml;
