<?php
$cart = $page->cart();
$licenses = explode(', ', $page->licenses());
?>
Thank’s for your purchase.


Billing Address:
<?= $page->company()->isNotEmpty() ? $page->company()->html() : null ?>

<?= $page->name()->isNotEmpty() ? $page->name()->html() : null ?>

<?= $page->street()->isNotEmpty() ? $page->street()->html() : null ?>

<?= $page->postal_code()->isNotEmpty() ? $page->postal_code()->html() : null ?> <?= $page->city()->isNotEmpty() ? $page->city()->html() : null ?>

<?= $page->state()->isNotEmpty() ? $page->state()->html() : null ?>

<?= option('wagnerwagner.site.countryList')[(string)$page->country()] ?>



Payment Method:
<?= $page->paymentMethodName() ?>



Purchase
<?php foreach($cart as $item): ?>
<?= $item['title'] ?> | Amount: <?= $item['quantity'] ?> | Price: <?= formatPrice($item['price'] - $item['tax']) ?> | Sum <?= formatPrice($item['sum'] - $item['sumTax']) ?>
<?php endforeach; ?>


Gross: <?= formatPrice($cart->getSum() - $cart->getTax()) ?>

+ Vat (19%): <?= formatPrice($cart->getTax()) ?>

Sum <?= formatPrice($cart->getSum()) ?>



<?= count($licenses) === 1 ? 'License Key' : 'License Keys' ?>

<?php foreach ($licenses as $item) :?>
<?= $item ?>

<?php endforeach; ?>



<?= $page->url() ?>



Wagnerwagner GmbH, Burkhardt+Weber-Straße 59, 72760 Reutlingen, Germany