<?php

/**
 * HTML version of the confirmation. Kirby sends it together with
 * confirmation.text.php, for mail clients that show text only.
 *
 * Mail clients ignore most CSS: the layout is tables, the styles are
 * inline, and the colors are those of o-order.css as hex values.
 *
 * @var \Wagnerwagner\Merx\OrderPage $page
 */

use Kirby\Cms\Url;

$cart = $page->cart();
$total = $cart->total();
$taxRates = $cart->taxRates();
$licenses = $page->licenses()->split(',');
$country = option('wagnerwagner.site.countryList')[(string)$page->country()] ?? null;

$font = 'font-family: Avenir, \'Helvetica Neue\', Helvetica, Arial, sans-serif;';
$label = 'font-size: 13px; line-height: 18px; color: #666666;';
$cell = 'padding: 8px 0; font-size: 15px; line-height: 20px; text-align: right; white-space: nowrap;';
?>
<!doctype html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Your Merx order</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f2f2f2; color: #212121; <?= $font ?>">
	<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color: #f2f2f2;">
		<tr>
			<td align="center" style="padding: 32px 16px;">
				<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width: 600px;">

					<?php /* o-order__status */ ?>
					<tr>
						<td style="padding: 24px; background-color: #f4f7ee; border: 2px solid #74af50; border-radius: 4px; <?= $font ?>">
							<h1 style="margin: 0; font-size: 20px; line-height: 26px; font-weight: 600;">Thank you for your order</h1>
							<p style="margin: 8px 0 0; font-size: 17px; line-height: 24px;">
								<?= count($licenses) === 1 ? 'Here is your license key' : 'Here are your license keys' ?>, together with your invoice.
							</p>
						</td>
					</tr>

					<tr><td style="height: 24px; line-height: 24px; font-size: 0;">&nbsp;</td></tr>

					<?php /* o-order__invoice */ ?>
					<tr>
						<td style="padding: 24px; background-color: #ffffff; border: 1px solid #d4d4d4; border-radius: 4px; <?= $font ?>">

							<?php if (count($licenses) > 0): ?>
								<p style="margin: 0 0 8px; <?= $label ?>"><?= count($licenses) === 1 ? 'Your license key' : 'Your license keys' ?></p>
								<?php foreach ($licenses as $license): ?>
									<p style="margin: 0 0 8px; font-family: monospace; font-size: 15px; line-height: 15px; letter-spacing: 0.02em;"><?= esc($license) ?></p>
								<?php endforeach ?>
								<div style="height: 24px; line-height: 24px; font-size: 0;">&nbsp;</div>
							<?php endif ?>

							<table role="presentation" width="100%" cellpadding="0" cellspacing="0">
								<tr>
									<td valign="top" style="padding-right: 16px; font-size: 15px; line-height: 22px;">
										<p style="margin: 0 0 4px; <?= $label ?>">Billed to</p>
										<?php foreach (['company', 'name', 'street'] as $key): ?>
											<?php if ($page->$key()->isNotEmpty()): ?>
												<?= $page->$key()->escape() ?><br>
											<?php endif ?>
										<?php endforeach ?>
										<?= $page->postal_code()->escape() ?> <?= $page->city()->escape() ?><br>
										<?php if ($page->state()->isNotEmpty()): ?>
											<?= $page->state()->escape() ?><br>
										<?php endif ?>
										<?= esc((string)$country) ?>
									</td>
									<td valign="top" width="40%" style="font-size: 15px; line-height: 22px;">
										<p style="margin: 0; <?= $label ?>">Invoice number</p>
										<p style="margin: 0 0 8px;"><?= $page->orderNumber()->escape() ?></p>
										<p style="margin: 0; <?= $label ?>">Date</p>
										<p style="margin: 0 0 8px;"><?= $page->dateCreated()->toDate('F j, Y') ?></p>
										<p style="margin: 0; <?= $label ?>">Payment method</p>
										<p style="margin: 0;"><?= esc($page->paymentMethodName()) ?></p>
									</td>
								</tr>
							</table>

							<div style="height: 24px; line-height: 24px; font-size: 0;">&nbsp;</div>

							<?php /* o-order__items */ ?>
							<table role="presentation" width="100%" cellpadding="0" cellspacing="0">
								<tr>
									<td style="padding: 8px 0; border-bottom: 1px solid #545454; <?= $label ?>">Product</td>
									<td style="padding: 8px 0 8px 16px; border-bottom: 1px solid #545454; text-align: right; <?= $label ?>">Quantity</td>
									<td style="padding: 8px 0 8px 16px; border-bottom: 1px solid #545454; text-align: right; <?= $label ?>">Amount</td>
								</tr>
								<?php foreach ($cart as $item): ?>
									<tr>
										<td style="padding: 8px 0; border-bottom: 1px solid #d4d4d4; font-size: 15px; line-height: 20px;"><?= esc((string)$item->title) ?></td>
										<td style="<?= $cell ?> padding-left: 16px; border-bottom: 1px solid #d4d4d4;"><?= $item->quantity ?></td>
										<td style="<?= $cell ?> padding-left: 16px; border-bottom: 1px solid #d4d4d4;"><?= $item->total()?->toString('priceNet') ?></td>
									</tr>
								<?php endforeach ?>
								<?php if (count($taxRates) > 0): ?>
									<tr>
										<td colspan="2" style="<?= $cell ?> padding-top: 16px; padding-bottom: 4px;">Subtotal</td>
										<td style="<?= $cell ?> padding-top: 16px; padding-bottom: 4px; padding-left: 16px;"><?= $total?->toString('priceNet') ?></td>
									</tr>
									<?php foreach ($taxRates as $tax): ?>
										<tr>
											<td colspan="2" style="<?= $cell ?> padding-top: 4px; padding-bottom: 4px;">VAT <?= $tax->rate() ?></td>
											<td style="<?= $cell ?> padding-top: 4px; padding-bottom: 4px; padding-left: 16px;"><?= $tax ?></td>
										</tr>
									<?php endforeach ?>
								<?php endif ?>
								<tr>
									<td colspan="2" style="<?= $cell ?> padding-top: 8px; font-size: 18px; font-weight: 600;">Total</td>
									<td style="<?= $cell ?> padding-top: 8px; padding-left: 16px; font-size: 18px; font-weight: 600;"><?= $total?->toString('price') ?></td>
								</tr>
							</table>

							<?php if ($notice = $page->vatNotice()): ?>
								<p style="margin: 24px 0 0; font-size: 14px; line-height: 20px;"><?= $notice ?></p>
							<?php endif ?>

							<p style="margin: 24px 0 0; font-size: 15px; line-height: 22px; text-align: center;">
								View or download your invoice:<br>
								<strong><a href="<?= $page->url() ?>" style="color: #285a14;"><?= Url::short($page->url()) ?></a></strong>
							</p>
						</td>
					</tr>

					<?php /* o-order__seller */ ?>
					<tr>
						<td style="padding: 24px 8px 0; font-size: 13px; line-height: 19px; color: #666666; <?= $font ?>">
							Wagnerwagner GmbH · Burkhardt+Weber-Straße 59 · 72760 Reutlingen · Germany<br>
							<a href="mailto:<?= option('merx-email') ?>" style="color: #666666;"><?= option('merx-email') ?></a> · VAT ID DE231029364
						</td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
</body>
</html>
