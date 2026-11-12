<?php

/**
 * @var OrderPage $page
 */

$cart = $page->cart();
$total = $cart->total();
$taxRates = $cart->taxRates();
$licenses = $page->licenses()->split(',');
?>
Thank’s for your purchase.


Billing Address:
<?= $page->company()->isNotEmpty() ? $page->company()->toString() : null ?>

<?= $page->name()->isNotEmpty() ? $page->name()->toString() : null ?>

<?= $page->street()->isNotEmpty() ? $page->street()->toString() : null ?>

<?= $page->postal_code()->isNotEmpty() ? $page->postal_code()->toString() : null ?> <?= $page->city()->isNotEmpty() ? $page->city()->toString() : null ?>

<?= $page->state()->isNotEmpty() ? $page->state()->toString() : null ?>

<?= option('wagnerwagner.site.countryList')[(string)$page->country()] ?>



Payment Method:
<?= $page->paymentMethodName() ?>



Purchase
<?php foreach ($cart as $item): ?>
<?= $item->title ?> | Quantity: <?= $item->quantity ?> | Price: <?= $item->price?->toString('priceNet') ?> | Sum <?= $item->total()?->toString('priceNet') ?>

<?php endforeach; ?>


<?php if (count($taxRates) > 0): ?>
Subtotal: <?= $total?->toString('priceNet') ?>

<?php foreach ($taxRates as $tax): ?>
+ VAT <?= $tax->rate() ?>: <?= $tax ?>

<?php endforeach; ?>
<?php endif ?>
Total: <?= $total?->toString('price') ?>




<?= count($licenses) === 1 ? 'License Key' : 'License Keys' ?>

<?php foreach ($licenses as $item) :?>
<?= $item ?>

<?php endforeach; ?>



<?= $page->url() ?>



Wagnerwagner GmbH, Burkhardt+Weber-Straße 59, 72760 Reutlingen, Germany
