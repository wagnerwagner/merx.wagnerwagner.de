<?php

/**
 * @var \Wagnerwagner\Merx\OrderPage|OrderPage $page
 * @var int $licenseCount
 * @var string[] $licenses
 * @var array $invoice See the controller
 */
?>
<?php snippet('head') ?>
<body>
	<?php snippet('o-header') ?>
	<main class="o-order" aria-labelledby="invoice-headline">
		<div class="o-order__box">
			<?php if ($page->isCancelled()): ?>
				<div class="o-order__status" data-status="cancelled">
					<h1 class="a-heading" data-size="small">Your order has been cancelled</h1>
					<div class="m-text">
						<p>
							This invoice has been cancelled. The original license <?= $licenseCount === 1 ? 'key is' : 'keys are' ?> no longer valid.
							<?php if ($page->isPaid()): ?>
								Your payment has been refunded to your original payment method: <?= esc($page->paymentMethodName()) ?>.
							<?php endif ?>
						</p>
					</div>
				</div>
			<?php else: ?>
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
			<?php endif ?>

			<div class="o-order__invoice">
				<h1 class="a-display" id="invoice-headline">Invoice</h1>
				<div class="o-order__details">
					<div>
						<h2>Billed to</h2>
						<p><?= implode('<br>', array_map('esc', $invoice['buyer'])) ?></p>
						<p><?= esc($invoice['email']) ?></p>
					</div>
					<dl>
						<?php foreach ($invoice['details'] as [$label, $value]): ?>
							<div>
								<dt><?= $label ?></dt>
								<dd><?= esc($value) ?></dd>
							</div>
						<?php endforeach ?>
					</dl>
				</div>

				<?php if (count($licenses) > 0): ?>
					<section class="o-order__licenses" aria-labelledby="licenses">
						<h2 class="a-heading" data-size="xsmall" id="licenses"><?= count($licenses) === 1 ? 'Your license key' : 'Your license keys' ?></h2>
						<ul>
							<?php foreach ($licenses as $license): ?>
								<?php if ($page->isCancelled()): ?>
									<li><del><code class="a-type" data-size="large"><?= Kirby\Toolkit\Html::encode($license) ?></code></del></li>
								<?php else: ?>
									<li><code class="a-type" data-size="large"><?= Kirby\Toolkit\Html::encode($license) ?></code></li>
								<?php endif ?>
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
						<?php foreach ($invoice['items'] as $cells): ?>
							<tr>
								<?php foreach ($cells as $cell): ?>
									<td><?= esc($cell) ?></td>
								<?php endforeach ?>
							</tr>
						<?php endforeach ?>
					</tbody>
					<tfoot>
						<?php foreach ($invoice['totals'] as [$label, $value]): ?>
							<tr>
								<th scope="row" colspan="3"><?= $label ?></th>
								<td><?= $value ?></td>
							</tr>
						<?php endforeach ?>
						<tr class="o-order__total">
							<th scope="row" colspan="3">Total</th>
							<td><?= $invoice['total'] ?></td>
						</tr>
					</tfoot>
				</table>

				<?php if ($page->vatCategory() === 'AE'): ?>
					<p class="o-order__notice">This invoice is issued under the <strong>reverse charge procedure</strong>. Therefore, <strong>no VAT is charged</strong>. The <strong>recipient of the supply is liable for the VAT</strong>. The VAT must be <strong>declared and paid by the recipient in accordance with the reverse charge mechanism</strong> (<a href="https://eur-lex.europa.eu/legal-content/EN/TXT/?uri=CELEX:32006L0112#art_196" target="_blank" rel="noopener">Art. 196 VAT Directive</a>).</p>
				<?php elseif ($invoice['notice'] !== null): ?>
					<p class="o-order__notice"><?= esc($invoice['notice']) ?></p>
				<?php endif ?>

				<footer class="o-order__seller">
					<h2>From</h2>
					<div class="m-text" data-size="medium" data-gap="small">
						<p><?= implode('<br>', array_map('esc', $invoice['seller']['address'])) ?></p>
						<p><a href="mailto:<?= esc($invoice['seller']['email'], 'attr') ?>"><?= esc($invoice['seller']['email']) ?></a></p>
						<p><?= implode('<br>', array_map('esc', $invoice['seller']['tax'])) ?></p>
					</div>
				</footer>
			</div>

			<div class="o-order__actions">
				<button type="button" class="a-button" data-kind="secondary" data-action="download-invoice">Download invoice</button>
			</div>
		</div>
		<script type="application/json" id="invoice-data"><?= json_encode($invoice, JSON_HEX_TAG | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?></script>
	</main>
	<script type="module" src="<?= hashedUrl('assets/js/order.js') ?>"></script>
	<?php snippet('o-footer') ?>
</body>
<?php snippet('foot') ?>
