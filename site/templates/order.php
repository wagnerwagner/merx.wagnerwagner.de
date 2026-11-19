<?php

/**
 * @var \Wagnerwagner\Merx\ProductList $cart
 * @var \Wagnerwagner\Merx\OrderPage|OrderPage $page
 * @var float $licenseCount
 * @var string[] $licenses
 */

$total = $cart->total();
$taxRates = $cart->taxRates();
$country = option('wagnerwagner.site.countryList')[(string)$page->country()] ?? null;
?>
<?php snippet('head') ?>
<body>
	<?php snippet('o-header') ?>
	<main class="o-order" aria-labelledby="invoice-headline">
		<div class="o-order__box">
			<img class="o-order__logo" src="<?= url('assets/images/wagnerwagner.svg') ?>" loading="lazy" alt="Logo: Wagnerwagner" height="16" width="169">

			<div class="o-order__status">
				<h1 class="a-heading" data-size="small">Thank you for your order</h1>
				<div class="m-text">
					<p>
						<?php if ($page->isPaid()): ?>
							We have send your license <?= $licenseCount === 1 ? 'key' : 'keys' ?> to <strong><?= $page->email()->escape() ?></strong>.
						<?php else: ?>
							As soon as we received the payment, we will send your license <?= $licenseCount === 1 ? 'key' : 'keys' ?> to <strong><?= $page->email()->escape() ?></strong>.
						<?php endif ?>
					</p>
				</div>
			</div>

			<div class="o-order__invoice">
				<h1 class="a-display" id="invoice-headline">Invoice</h1>
				<div class="o-order__details">
					<div>
						<h2>Billed to</h2>
						<p>
							<?php foreach (['company', 'name', 'street'] as $key): ?>
								<?php if ($page->$key()->isNotEmpty()): ?>
									<?= $page->$key()->escape() ?><br>
								<?php endif ?>
							<?php endforeach ?>
							<?= $page->postal_code()->escape() ?> <?= $page->city()->escape() ?><br>
							<?php if ($page->state()->isNotEmpty()): ?>
								<?= $page->state()->escape() ?><br>
							<?php endif ?>
							<?= $country ?>
						</p>
						<p><?= $page->email()->escape() ?></p>
					</div>
					<dl>
						<div>
							<dt>Invoice number</dt>
							<dd><?= $page->orderNumber()->escape() ?></dd>
						</div>
						<div>
							<dt>Date</dt>
							<dd><?= $page->dateCreated()->toDate('F j, Y') ?></dd>
						</div>
						<div>
							<dt>Payment method</dt>
							<dd><?= $page->paymentMethodName() ?></dd>
						</div>
					</dl>
				</div>

				<?php if (count($licenses) > 0): ?>
					<section class="o-order__licenses" aria-labelledby="licenses">
						<h2 class="a-heading" data-size="xsmall" id="licenses"><?= count($licenses) === 1 ? 'Your license key' : 'Your license keys' ?></h2>
						<ul>
							<?php foreach ($licenses as $license): ?>
								<li><code class="a-type" data-size="large"><?= Kirby\Toolkit\Html::encode($license) ?></code></li>
							<?php endforeach ?>
						</ul>
					</section>
				<?php endif ?>


				<table class="o-order__items">
					<thead>
						<tr>
							<th scope="col">Product</th>
							<th scope="col">Quantity</th>
							<th scope="col">Unit price</th>
							<th scope="col">Amount</th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ($cart as $item): ?>
							<?php /** @var \Wagnerwagner\Merx\ListItem $item */ ?>
							<tr>
								<td><?= Kirby\Toolkit\Html::encode($item->title) ?></td>
								<td><?= $item->quantity ?></td>
								<td><?= $item->price?->toString('priceNet') ?></td>
								<td><?= $item->total()?->toString('priceNet') ?></td>
							</tr>
						<?php endforeach ?>
					</tbody>
					<tfoot>
						<?php if (count($taxRates) > 0): ?>
							<tr>
								<th scope="row" colspan="3">Subtotal</th>
								<td><?= $total?->toString('priceNet') ?></td>
							</tr>
							<?php foreach ($taxRates as $tax): ?>
								<tr>
									<th scope="row" colspan="3">VAT <?= $tax->rate() ?></th>
									<td><?= $tax ?></td>
								</tr>
							<?php endforeach ?>
						<?php endif ?>
						<tr class="o-order__total">
							<th scope="row" colspan="3">Total</th>
							<td><?= $total?->toString('price') ?></td>
						</tr>
					</tfoot>
				</table>

				<?php if (count($taxRates) === 0 && Kirby\Toolkit\Str::lower($page->country()->toString()) !== 'de'): ?>
					<p class="o-order__notice">This invoice is issued under the <strong>reverse charge procedure</strong>. Therefore, <strong>no VAT is charged</strong>. The <strong>recipient of the supply is liable for the VAT</strong>. The VAT must be <strong>declared and paid by the recipient in accordance with the reverse charge mechanism</strong> (<a href="https://eur-lex.europa.eu/legal-content/EN/TXT/?uri=CELEX:32006L0112#art_196" target="_blank" rel="noopener">Art. 196 VAT Directive</a>).</p>
				<?php endif ?>

				<footer class="o-order__seller">
					<h2>From</h2>
					<div class="m-text" data-size="medium" data-gap="small">
						<p>
							Wagnerwagner GmbH<br>
							Burkhardt+Weber-Straße 59<br>
							72760 Reutlingen<br>
							Germany
						</p>
						<p><a href="mailto:merx@wagnerwagner.de">merx@wagnerwagner.de</a></p>
						<p>
							Tax number 78092 / 68991<br>
							VAT identification number DE231029364
						</p>
					</div>
				</footer>
			</div>

			<div class="o-order__actions">
				<button type="button" class="a-button" data-kind="secondary" onclick="window.print()">Print invoice</button>
			</div>
		</div>
	</main>
	<?php snippet('o-footer') ?>
</body>
<?php snippet('foot') ?>
