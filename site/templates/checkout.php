<?php
/** @var \Wagnerwagner\Merx\Cart $cart */
/** @var ?string $region */
$stripePublishableKey = option('wagnerwagner.merx.production') === true
	? option('wagnerwagner.merx.stripe.live.publishable_key')
	: option('wagnerwagner.merx.stripe.test.publishable_key');
?>
<?php snippet('head') ?>
<body>
	<?php snippet('o-header') ?>
	<main class="o-checkout">
		<div class="o-checkout__box">
			<h1 class="a-visually-hidden"><?= $page->title() ?></h1>
			<form class="o-checkout__form" method="post" action="<?= url('api/shop/checkout') ?>">
				<div class="o-checkout__abstract">
					<?= $page->abstract()->kt() ?>
				</div>
				<div class="o-checkout__cart">
					<div class="m-cart">
						<div class="m-cart__name">Merx License</div>
						<div class="m-cart__quantity">
							<span aria-label="quantity"><?= $cart->quantity() ?></span>
							<button type="button" data-action="decrease" aria-label="decrease quantity">−</button>
							<button type="button" data-action="increase" aria-label="increase quantity">+</button>
						</div>
						<div class="m-cart__sum"><?= $cart->total()->toString() ?></div>
						<?php if ($cart->total()->tax): ?>
							<div class="m-cart__tax">+ Vat (<?= $cart->total()->tax->rate() ?>) <?= $cart->tax() ?></div>
						<?php endif ?>
						<div class="m-cart__total"><?= $cart->total()->toString('price') ?></div>
					</div>
				</div>
				<div class="m-form-checkout">
					<div>
						<h3 class="a-heading" data-size="small">Contact Infomation</h3>
						<label class="m-field">
							<div>Email Address</div>
							<input type="email" name="email" placeholder="contact@nicenewshoes.com" autocomplete="email" required>
							<small>We will send your license key to this email address.</small>
						</label>
					</div>
					<div>

						<h3 class="a-heading" data-size="small">Billing Address</h3>
						<label class="m-field">
							<div>Company</div>
							<input type="text" name="company" placeholder="Nice New Shoes Inc" autocomplete="organization" required>
						</label>
						<label class="m-field">
							<div>Full Name</div>
							<input type="text" name="name" placeholder="Margret L Torreggiani" autocomplete="name" required>
						</label>
						<label class="m-field">
							<div>Street Address</div>
							<input type="text" name="street" required placeholder="50 Kintyre Street" autocomplete="address-line1 billing" required>
						</label>
						<label class="m-field" data-span=2>
							<div>Postal Code / ZIP</div>
							<input type="text" name="postal_code" placeholder="4133" autocomplete="postal-code billing" required>
						</label>
						<label class="m-field" data-span=4>
							<div>City</div>
							<input type="text" name="city" required placeholder="Waterford" autocomplete="address-level2 billing" required>
						</label>
						<label class="m-field">
							<div>State/Province/Region <small>(optional)</small></div>
							<input type="text" name="state" placeholder="Queensland" autocomplete="address-level1 billing">
						</label>
						<label class="m-field">
							<div>Country</div>
							<select name="country" autocomplete="country" required>
								<?php foreach(option('wagnerwagner.site.countryList') as $code => $name): ?>
									<option value="<?= $code ?>" <?= $region === $code ? 'selected' : '' ?>><?= $name ?></option>
								<?php endforeach; ?>
							</select>
						</label>
					</div>
					<div class="m-form-checkout__payment-fields"></div>
					<input type="hidden" name="payment-gateway" value="stripe-elements">
					<input type="hidden" name="stripePublishableKey" value="<?= $stripePublishableKey ?>">

					<label class="m-field">
						<input type="checkbox" name="legal" required>
						<div>I agree the <a href="<?= page('privacy')->url() ?>" target="_blank">Privacy Policy</a>.</div>
					</label>

					<div class="m-form-checkout__submit">
						<button type="submit">buy</button>
					</div>
				</div>
			</form>
		</div>
	</main>
	<?php snippet('o-footer') ?>
</body>
<?php snippet('foot') ?>
