<?php
/**
 * Factur-X 1.0 / ZUGFeRD 2, profile EN 16931 (UN/CEFACT Cross Industry Invoice)
 *
 * For a customer in Germany, so the VAT has the standard category `S`. See
 * `OrderPage::eInvoiceXml()`. Amounts are in cents, so they add up without
 * floating point errors.
 *
 * @var OrderPage $order
 */

use Kirby\Toolkit\Str;

$x = fn (mixed $value): string => htmlspecialchars((string)$value, ENT_XML1 | ENT_QUOTES, 'UTF-8');
$amount = fn (int $cents): string => number_format($cents / 100, 2, '.', '');
$percent = fn (float $rate): string => rtrim(rtrim(number_format($rate * 100, 2, '.', ''), '0'), '.');
$date = fn (string $field): string => '<udt:DateTimeString format="102">' . $order->content()->get($field)->toDate('Ymd') . '</udt:DateTimeString>';

$seller = option('wagnerwagner.site.seller');
$cart = $order->cart();
$currency = $cart->total()?->currency ?? 'EUR';

$lines = [];
foreach ($cart as $item) {
	/** @var \Wagnerwagner\Merx\ListItem $item */
	$lines[] = [
		'id' => $item->key,
		'name' => (string)$item->title,
		'quantity' => $item->quantity,
		'price' => (float)$item->price?->priceNet,
		'net' => (int)round((float)$item->total()?->priceNet * 100),
		'rate' => (float)$item->price?->tax?->rate,
	];
}

// One VAT breakdown per rate. Its VAT is calculated from its net amount, as
// EN 16931 requires.
$taxes = [];
foreach ($lines as $line) {
	$key = (string)$line['rate'];
	$taxes[$key] ??= ['rate' => $line['rate'], 'net' => 0];
	$taxes[$key]['net'] += $line['net'];
}
$taxes = array_map(fn (array $tax): array => $tax + ['tax' => (int)round($tax['net'] * $tax['rate'])], $taxes);

$lineTotal = array_sum(array_column($lines, 'net'));
$taxTotal = array_sum(array_column($taxes, 'tax'));
$grandTotal = $lineTotal + $taxTotal;
// What the customer pays, as the order page shows it. Merx adds up the VAT of
// each item, which can differ by a cent: EN 16931 calls that rounding.
$payable = (int)round((float)$cart->total()?->price * 100);
$prepaid = $order->isPaid() ? $payable : 0;

$paymentMethod = $order->paymentDetails()['payment_method'] ?? ($order->paymentProvider() === 'paypal' ? 'paypal' : null);
// UNTDID 4461
$paymentMeansCode = match ($paymentMethod) {
	'card' => '48', // bank card
	'sepa_debit' => '59', // SEPA direct debit
	default => '68', // online payment service, e.g. PayPal
};
?>
<?= '<?xml version="1.0" encoding="UTF-8"?>' ?>

<rsm:CrossIndustryInvoice
	xmlns:rsm="urn:un:unece:uncefact:data:standard:CrossIndustryInvoice:100"
	xmlns:ram="urn:un:unece:uncefact:data:standard:ReusableAggregateBusinessInformationEntity:100"
	xmlns:qdt="urn:un:unece:uncefact:data:standard:QualifiedDataType:100"
	xmlns:udt="urn:un:unece:uncefact:data:standard:UnqualifiedDataType:100">
	<rsm:ExchangedDocumentContext>
		<ram:GuidelineSpecifiedDocumentContextParameter>
			<ram:ID>urn:cen.eu:en16931:2017</ram:ID>
		</ram:GuidelineSpecifiedDocumentContextParameter>
	</rsm:ExchangedDocumentContext>
	<rsm:ExchangedDocument>
		<ram:ID><?= $x($order->orderNumber()) ?></ram:ID>
		<ram:TypeCode>380</ram:TypeCode>
		<ram:IssueDateTime><?= $date('dateCreated') ?></ram:IssueDateTime>
	</rsm:ExchangedDocument>
	<rsm:SupplyChainTradeTransaction>
<?php foreach ($lines as $index => $line): ?>
		<ram:IncludedSupplyChainTradeLineItem>
			<ram:AssociatedDocumentLineDocument>
				<ram:LineID><?= $index + 1 ?></ram:LineID>
			</ram:AssociatedDocumentLineDocument>
			<ram:SpecifiedTradeProduct>
				<ram:SellerAssignedID><?= $x($line['id']) ?></ram:SellerAssignedID>
				<ram:Name><?= $x($line['name']) ?></ram:Name>
			</ram:SpecifiedTradeProduct>
			<ram:SpecifiedLineTradeAgreement>
				<ram:NetPriceProductTradePrice>
					<ram:ChargeAmount><?= number_format($line['price'], 2, '.', '') ?></ram:ChargeAmount>
				</ram:NetPriceProductTradePrice>
			</ram:SpecifiedLineTradeAgreement>
			<ram:SpecifiedLineTradeDelivery>
				<ram:BilledQuantity unitCode="C62"><?= $line['quantity'] ?></ram:BilledQuantity>
			</ram:SpecifiedLineTradeDelivery>
			<ram:SpecifiedLineTradeSettlement>
				<ram:ApplicableTradeTax>
					<ram:TypeCode>VAT</ram:TypeCode>
					<ram:CategoryCode>S</ram:CategoryCode>
					<ram:RateApplicablePercent><?= $percent($line['rate']) ?></ram:RateApplicablePercent>
				</ram:ApplicableTradeTax>
				<ram:SpecifiedTradeSettlementLineMonetarySummation>
					<ram:LineTotalAmount><?= $amount($line['net']) ?></ram:LineTotalAmount>
				</ram:SpecifiedTradeSettlementLineMonetarySummation>
			</ram:SpecifiedLineTradeSettlement>
		</ram:IncludedSupplyChainTradeLineItem>
<?php endforeach ?>
		<ram:ApplicableHeaderTradeAgreement>
			<ram:SellerTradeParty>
				<ram:Name><?= $x($seller['name']) ?></ram:Name>
				<ram:PostalTradeAddress>
					<ram:PostcodeCode><?= $x($seller['postalCode']) ?></ram:PostcodeCode>
					<ram:LineOne><?= $x($seller['street']) ?></ram:LineOne>
					<ram:CityName><?= $x($seller['city']) ?></ram:CityName>
					<ram:CountryID><?= $x($seller['country']) ?></ram:CountryID>
				</ram:PostalTradeAddress>
				<ram:URIUniversalCommunication>
					<ram:URIID schemeID="EM"><?= $x(option('merx-email')) ?></ram:URIID>
				</ram:URIUniversalCommunication>
				<ram:SpecifiedTaxRegistration>
					<ram:ID schemeID="VA"><?= $x($seller['vatId']) ?></ram:ID>
				</ram:SpecifiedTaxRegistration>
				<ram:SpecifiedTaxRegistration>
					<ram:ID schemeID="FC"><?= $x($seller['taxNumber']) ?></ram:ID>
				</ram:SpecifiedTaxRegistration>
			</ram:SellerTradeParty>
			<ram:BuyerTradeParty>
				<ram:Name><?= $x($order->company()->or($order->name())) ?></ram:Name>
<?php if ($order->name()->isNotEmpty()): ?>
				<ram:DefinedTradeContact>
					<ram:PersonName><?= $x($order->name()) ?></ram:PersonName>
				</ram:DefinedTradeContact>
<?php endif ?>
				<ram:PostalTradeAddress>
					<ram:PostcodeCode><?= $x($order->postal_code()) ?></ram:PostcodeCode>
					<ram:LineOne><?= $x($order->street()) ?></ram:LineOne>
					<ram:CityName><?= $x($order->city()) ?></ram:CityName>
					<ram:CountryID><?= $x(Str::upper((string)$order->country())) ?></ram:CountryID>
<?php if ($order->state()->isNotEmpty()): ?>
					<ram:CountrySubDivisionName><?= $x($order->state()) ?></ram:CountrySubDivisionName>
<?php endif ?>
				</ram:PostalTradeAddress>
				<ram:URIUniversalCommunication>
					<ram:URIID schemeID="EM"><?= $x($order->email()) ?></ram:URIID>
				</ram:URIUniversalCommunication>
			</ram:BuyerTradeParty>
		</ram:ApplicableHeaderTradeAgreement>
		<ram:ApplicableHeaderTradeDelivery>
<?php if ($order->isPaid()): // The license keys are issued when the order is paid ?>
			<ram:ActualDeliverySupplyChainEvent>
				<ram:OccurrenceDateTime><?= $date('datePaid') ?></ram:OccurrenceDateTime>
			</ram:ActualDeliverySupplyChainEvent>
<?php endif ?>
		</ram:ApplicableHeaderTradeDelivery>
		<ram:ApplicableHeaderTradeSettlement>
			<ram:PaymentReference><?= $x($order->orderNumber()) ?></ram:PaymentReference>
			<ram:InvoiceCurrencyCode><?= $x($currency) ?></ram:InvoiceCurrencyCode>
			<ram:SpecifiedTradeSettlementPaymentMeans>
				<ram:TypeCode><?= $paymentMeansCode ?></ram:TypeCode>
				<ram:Information><?= $x($order->paymentMethodName()) ?></ram:Information>
			</ram:SpecifiedTradeSettlementPaymentMeans>
<?php foreach ($taxes as $tax): ?>
			<ram:ApplicableTradeTax>
				<ram:CalculatedAmount><?= $amount($tax['tax']) ?></ram:CalculatedAmount>
				<ram:TypeCode>VAT</ram:TypeCode>
				<ram:BasisAmount><?= $amount($tax['net']) ?></ram:BasisAmount>
				<ram:CategoryCode>S</ram:CategoryCode>
				<ram:RateApplicablePercent><?= $percent($tax['rate']) ?></ram:RateApplicablePercent>
			</ram:ApplicableTradeTax>
<?php endforeach ?>
			<ram:SpecifiedTradePaymentTerms>
				<ram:Description><?= $x(($order->isPaid() ? 'Paid with ' : 'Payment by ') . $order->paymentMethodName()) ?></ram:Description>
			</ram:SpecifiedTradePaymentTerms>
			<ram:SpecifiedTradeSettlementHeaderMonetarySummation>
				<ram:LineTotalAmount><?= $amount($lineTotal) ?></ram:LineTotalAmount>
				<ram:TaxBasisTotalAmount><?= $amount($lineTotal) ?></ram:TaxBasisTotalAmount>
				<ram:TaxTotalAmount currencyID="<?= $x($currency) ?>"><?= $amount($taxTotal) ?></ram:TaxTotalAmount>
<?php if ($payable !== $grandTotal): ?>
				<ram:RoundingAmount><?= $amount($payable - $grandTotal) ?></ram:RoundingAmount>
<?php endif ?>
				<ram:GrandTotalAmount><?= $amount($grandTotal) ?></ram:GrandTotalAmount>
				<ram:TotalPrepaidAmount><?= $amount($prepaid) ?></ram:TotalPrepaidAmount>
				<ram:DuePayableAmount><?= $amount($payable - $prepaid) ?></ram:DuePayableAmount>
			</ram:SpecifiedTradeSettlementHeaderMonetarySummation>
		</ram:ApplicableHeaderTradeSettlement>
	</rsm:SupplyChainTradeTransaction>
</rsm:CrossIndustryInvoice>
