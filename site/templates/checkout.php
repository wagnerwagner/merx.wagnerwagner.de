<?php
/** @var \Wagnerwagner\Merx\Cart $cart */
/** @var \Kirby\Cms\Page $page */
/** @var ?string $region */
$stripePublishableKey = option('wagnerwagner.merx.production') === true
	? option('wagnerwagner.merx.stripe.live.publishable_key')
	: option('wagnerwagner.merx.stripe.test.publishable_key');
$paypalClientId = option('wagnerwagner.merx.production') === true
	? option('wagnerwagner.merx.paypal.live.clientID')
	: option('wagnerwagner.merx.paypal.sandbox.clientID');

$total = $cart->total();
$license = $cart->findBy('key', 'merx-license');
?>
<?php snippet('head') ?>
<body>
	<?php snippet('o-header') ?>
	<main class="o-checkout" aria-labelledby="checkout-headline">
		<header class="o-checkout__header">
			<h1 class="a-display" id="checkout-headline"><?= $page->headline() ?></h1>
			<div class="m-text" data-size="xlarge">
				<?= $page->abstract()->kt() ?>
			</div>
		</header>

		<form class="o-checkout__form" method="post" action="<?= url('api/shop/checkout') ?>">
			<aside class="o-checkout__cart" aria-labelledby="cart-headline">
				<div class="m-cart" aria-live="polite">
					<div class="m-cart__item">
						<div>
							<strong>Merx License</strong>
							<small>Including security updates for 5 years</small>
						</div>
						<div class="m-cart__quantity">
							<button type="button" data-action="decrease" aria-label="One license less" <?= $cart->quantity() <= 1 ? 'disabled' : '' ?>>−</button>
							<output data-cart="quantity" aria-label="Number of licenses"><?= $cart->quantity() ?></output>
							<button type="button" data-action="increase" aria-label="One license more">+</button>
						</div>
					</div>

					<dl class="m-cart__totals">
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
				</div>
			</aside>

			<div class="m-form-checkout">
				<?php snippet('m-callout', [
					'theme' => 'warning',
					'html' => '<p>The shop isn’t live yet. You can try the checkout with test payments, but no real order will be placed. The price may still change.</p>',
				]) ?>

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

					<?php /*
						The value of a payment method of Stripe is its type at Stripe.
						Apple Pay and Google Pay show up where Stripe says the browser
						can pay with them, see /develop/js/components/o-checkout.js
					*/ ?>
					<div class="m-form-checkout__payment-methods">
						<label class="m-field" data-type="radio">
							<input type="radio" name="paymentMethod" value="card" data-gateway="stripe" required>
							<div>Credit card</div>
						</label>
						<label class="m-field" data-type="radio">
							<input type="radio" name="paymentMethod" value="paypal" data-gateway="paypal" data-submit-text="Continue to PayPal" required>
							<div>PayPal</div>
						</label>
						<label class="m-field" data-type="radio">
							<input type="radio" name="paymentMethod" value="ideal" data-gateway="stripe" data-submit-text="Continue to iDEAL" required>
							<div>iDEAL | Wero</div>
						</label>
						<label class="m-field" data-type="radio">
							<input type="radio" name="paymentMethod" value="sepa_debit" data-gateway="stripe" required>
							<div>SEPA Direct Debit</div>
						</label>
						<label class="m-field" data-type="radio">
							<input type="radio" name="paymentMethod" value="amazon_pay" data-gateway="stripe" data-submit-text="Continue to Amazon Pay" required>
							<div>Amazon Pay</div>
						</label>
						<label class="m-field" data-type="radio">
							<input type="radio" name="paymentMethod" value="klarna" data-gateway="stripe" data-submit-text="Continue to Klarna" required>
							<div>Klarna</div>
						</label>
						<label class="m-field" data-type="radio" hidden>
							<input type="radio" name="paymentMethod" value="apple_pay" data-gateway="stripe" data-wallet="applePay" autocomplete="off" required>
							<div>Apple Pay</div>
						</label>
						<label class="m-field" data-type="radio" hidden>
							<input type="radio" name="paymentMethod" value="google_pay" data-gateway="stripe" data-wallet="googlePay" autocomplete="off" required>
							<div>Google Pay</div>
						</label>
					</div>

					<div class="m-field" data-payment-method="card" hidden>
						<div>Card details</div>
						<div class="m-field__stripe" id="stripe-card"></div>
					</div>

					<div class="m-field" data-payment-method="sepa_debit" hidden>
						<div>IBAN</div>
						<div class="m-field__stripe" id="stripe-iban"></div>
						<small>By providing your IBAN and confirming this payment, you authorize Wagnerwagner GmbH and Stripe, our payment service provider, to send instructions to your bank to debit your account, and your bank to debit your account in accordance with those instructions. You are entitled to a refund from your bank under the terms and conditions of your agreement with your bank. A refund must be claimed within 8 weeks starting from the date on which your account was debited.</small>
					</div>
				</fieldset>

				<input type="hidden" name="stripePublishableKey" value="<?= $stripePublishableKey ?>">

				<?php if ($error = merx()->error()): ?>
					<?php /* E.g. the customer canceled at PayPal, which sends them back here */ ?>
					<p class="m-form-checkout__error" role="alert"><?= esc($error['message']) ?></p>
				<?php endif ?>

				<div class="m-form-checkout__submit">
					<button type="submit" class="a-button" data-size="large" data-kind="brand">
						<span data-submit="pay">Pay <span data-cart="total"><?= $total->toString('price') ?></span></span>
						<span data-submit="method" hidden></span>
					</button>
					<?php /* Only a click on its own button may open the wallet */ ?>
					<div class="m-form-checkout__wallet" id="express-checkout-apple_pay" data-payment-method="apple_pay" hidden></div>
					<div class="m-form-checkout__wallet" id="express-checkout-google_pay" data-payment-method="google_pay" hidden></div>
				</div>
			</div>
		</form>
	</main>
	<?php snippet('o-footer') ?>
</body>
<?php snippet('foot') ?>
