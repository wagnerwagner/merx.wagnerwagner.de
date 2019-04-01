<?php
$cart = $page->cart();
$licenses = explode(', ', $page->licenses());
?>
Thank’s for your purchase.


Billing Address:
<?= $page->company()->isNotEmpty() ? $page->company()->html() : '' ?>

<?= $page->name()->isNotEmpty() ? $page->name()->html() : '' ?>

<?= $page->street()->isNotEmpty() ? $page->street()->html() : '' ?>

<?= $page->postal_code()->isNotEmpty() ? $page->postal_code()->html() : '' ?> <?= $page->city()->isNotEmpty() ? $page->city()->html() : '' ?>

<?= $page->state()->isNotEmpty() ? $page->state()->html() : '' ?>

<?= option('wagnerwagner.site.countryList')[(string)$page->country()] ?>



Payment Method: <?= $page->paymentMethodName() ?>



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
