<?php
/** @var \Wagnerwagner\Merx\Cart $cart */
/** @var ?string $region */
$stripePublishableKey = option('wagnerwagner.merx.production') === true
	? option('wagnerwagner.merx.stripe.live.publishable_key')
	: option('wagnerwagner.merx.stripe.test.publishable_key');

$total = $cart->total();
$license = $cart->findBy('key', 'merx-license');
?>
<?php snippet('head') ?>
<body>
	<?php snippet('o-header') ?>
	<main class="o-checkout" aria-labelledby="checkout-headline">
		<header class="o-checkout__header">
			<h1 class="a-display" id="checkout-headline">Buy a license</h1>
			<div class="m-text" data-size="xlarge">
				<?= $page->abstract()->kt() ?>
			</div>
		</header>

		<form class="o-checkout__form" method="post" action="<?= url('api/shop/checkout') ?>">
			<aside class="o-checkout__cart" aria-labelledby="cart-headline">
				<div class="m-cart" aria-live="polite">
					<h2 class="a-heading" data-size="xsmall" id="cart-headline">Your order</h2>

					<div class="m-cart__item">
						<div>
							<strong>Merx License</strong>
							<small>For one website, including all Merx 2 updates</small>
						</div>
						<div class="m-cart__quantity">
							<button type="button" data-action="decrease" aria-label="One license less" <?= $cart->quantity() <= 1 ? 'disabled' : '' ?>>−</button>
							<output data-cart="quantity" aria-label="Number of licenses"><?= $cart->quantity() ?></output>
							<button type="button" data-action="increase" aria-label="One license more">+</button>
						</div>
					</div>

					<dl class="m-cart__totals">
						<div>
							<dt>Price per license</dt>
							<dd data-cart="price"><?= $license?->price?->toString('priceNet') ?></dd>
						</div>
						<div>
							<dt>Subtotal</dt>
							<dd data-cart="subtotal"><?= $total->toString('priceNet') ?></dd>
						</div>
						<?php foreach ($cart->taxRates() as $tax): ?>
							<div data-cart="tax">
								<dt>VAT <?= $tax->rate() ?></dt>
								<dd><?= $tax ?></dd>
							</div>
						<?php endforeach ?>
						<div class="m-cart__total">
							<dt>Total</dt>
							<dd data-cart="total"><?= $total->toString('price') ?></dd>
						</div>
					</dl>

					<p class="m-cart__note">VAT depends on your billing country.</p>
				</div>
			</aside>

			<div class="m-form-checkout">
				<fieldset>
					<legend class="a-heading" data-size="small">Contact information</legend>
					<label class="m-field">
						<div>Email address</div>
						<input type="email" name="email" placeholder="contact@nicenewshoes.com" autocomplete="email" required>
						<small>We’ll send your license key to this address.</small>
					</label>
				</fieldset>

				<fieldset>
					<legend class="a-heading" data-size="small">Billing address</legend>
					<label class="m-field">
						<div>Company</div>
						<input type="text" name="company" placeholder="Nice New Shoes Inc" autocomplete="organization" required>
					</label>
					<label class="m-field">
						<div>Full name <small>(optional)</small></div>
						<input type="text" name="name" placeholder="Margret L Torreggiani" autocomplete="name">
					</label>
					<label class="m-field">
						<div>Street address</div>
						<input type="text" name="street" placeholder="50 Kintyre Street" autocomplete="billing address-line1" required>
					</label>
					<label class="m-field" data-span="2">
						<div>Postal code</div>
						<input type="text" name="postal_code" placeholder="4133" autocomplete="billing postal-code" required>
					</label>
					<label class="m-field" data-span="4">
						<div>City</div>
						<input type="text" name="city" placeholder="Waterford" autocomplete="billing address-level2" required>
					</label>
					<label class="m-field">
						<div>State, province or region <small>(optional)</small></div>
						<input type="text" name="state" placeholder="Queensland" autocomplete="billing address-level1">
					</label>
					<label class="m-field">
						<div>Country</div>
						<select name="country" autocomplete="billing country" required>
							<?php if ($region === null): ?>
								<option value="" selected disabled>Choose a country</option>
							<?php endif ?>
							<?php foreach (option('wagnerwagner.site.countryList') as $code => $name): ?>
								<option value="<?= $code ?>" <?= $region === $code ? 'selected' : '' ?>><?= $name ?></option>
							<?php endforeach ?>
						</select>
					</label>
				</fieldset>

				<fieldset>
					<legend class="a-heading" data-size="small">Payment</legend>
					<div class="m-form-checkout__payment-fields"></div>
				</fieldset>

				<input type="hidden" name="payment-gateway" value="stripe-elements">
				<input type="hidden" name="stripePublishableKey" value="<?= $stripePublishableKey ?>">

				<label class="m-field" data-type="checkbox">
					<input type="checkbox" name="legal" required>
					<div>I agree to the <a href="<?= page('privacy')->url() ?>" target="_blank">privacy policy</a>.</div>
				</label>

				<div class="m-form-checkout__submit">
					<button type="submit" class="a-button" data-size="large" data-kind="brand">
						Pay <span data-cart="total"><?= $total->toString('price') ?></span>
					</button>
				</div>
			</div>
		</form>
	</main>
	<?php snippet('o-footer') ?>
</body>
<?php snippet('foot') ?>
