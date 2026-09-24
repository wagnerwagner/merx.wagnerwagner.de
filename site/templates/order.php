<?php

/**
 * @var \Wagnerwagner\Merx\ProductList $cart
 * @var \Wagnerwagner\Merx\OrderPage|OrderPage $page
 * @var string[] $licenses
 */

$total = $cart->total();
$taxRates = $cart->taxRates();
$isPaid = $page->paymentComplete()->toBool();
$isTestPayment = ($page->paymentDetails()['livemode'] ?? null) === false;
$country = option('wagnerwagner.site.countryList')[(string)$page->country()] ?? null;
?>
<?php snippet('head') ?>
<body>
	<?php snippet('o-header') ?>
	<main class="o-order" aria-labelledby="order-headline">
		<div class="o-order__box">
			<header class="o-order__header">
				<p class="a-label o-order__meta">
					Order <?= $page->orderNumber()->escape() ?> · <?= $page->dateCreated()->toDate('F j, Y') ?>
				</p>
				<h1 class="a-display" id="order-headline">Thank you for your order</h1>
				<?php if (count($licenses) === 0): ?>
					<div class="m-text" data-size="xlarge">
						<p>We’ll send your license key to <strong><?= $page->email()->escape() ?></strong>.</p>
					</div>
				<?php endif ?>
			</header>

			<div class="m-callout o-order__status" <?= attr(['data-theme' => $isPaid ? 'tip' : null]) ?>>
				<div class="m-text" data-variant="callout">
					<?php if ($isPaid): ?>
						<p>Paid with <?= $page->paymentMethodName() ?> on <?= $page->datePaid()->toDate('F j, Y') ?>.</p>
					<?php else: ?>
						<p>Your <?= $page->paymentMethodName() ?> payment is being processed. This can take a few business days. Once it has arrived, this page shows it as paid.</p>
					<?php endif ?>
					<?php if ($isTestPayment): ?>
						<p>This was a test payment. No money has been charged.</p>
					<?php endif ?>
				</div>
			</div>

			<?php if (count($licenses) > 0): ?>
				<section class="o-order__licenses" aria-labelledby="licenses">
					<h2 class="a-heading" data-size="small" id="licenses"><?= count($licenses) === 1 ? 'Your license key' : 'Your license keys' ?></h2>
					<ul>
						<?php foreach ($licenses as $license): ?>
							<li><code><?= Kirby\Toolkit\Html::encode($license) ?></code></li>
						<?php endforeach ?>
					</ul>
				</section>
			<?php endif ?>

			<article class="o-order__invoice" aria-labelledby="invoice">
				<header class="o-order__invoice-header">
					<h2 class="a-heading" data-size="large" id="invoice">Invoice</h2>
					<img src="<?= url('assets/images/wagnerwagner.svg') ?>" alt="Wagnerwagner" height="16" width="169">
				</header>

				<dl class="o-order__details">
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
					<div>
						<dt>Status</dt>
						<dd><?= $isPaid ? 'Paid' : 'Payment pending' ?></dd>
					</div>
				</dl>

				<div class="o-order__addresses">
					<div>
						<h3 class="a-label">Billed to</h3>
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
					<div>
						<h3 class="a-label">From</h3>
						<p>
							Wagnerwagner GmbH<br>
							Burkhardt+Weber-Straße 59<br>
							72760 Reutlingen<br>
							Germany
						</p>
						<p><a href="mailto:merx@wagnerwagner.de">merx@wagnerwagner.de</a></p>
					</div>
				</div>

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
					Wagnerwagner GmbH · Tax number 78092 / 68991 · VAT identification number DE231029364
				</footer>
			</article>

			<div class="o-order__actions">
				<button type="button" class="a-button" data-kind="secondary" onclick="window.print()">Print invoice</button>
			</div>
		</div>
	</main>
	<?php snippet('o-footer') ?>
</body>
<?php snippet('foot') ?>
