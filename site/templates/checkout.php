<?php
/** @var \Wagnerwagner\Merx\Cart $cart */
$stripePublishableKey = option('wagnerwagner.merx.production') === true
	? option('wagnerwagner.merx.stripe.live.publishable_key')
	: option('wagnerwagner.merx.stripe.test.publishable_key');
?>
<?php snippet('head') ?>
<body>
	<?php snippet('o-header') ?>
	<main class="o-checkout">
		<div class="o-checkout__box">
			<strong>@TODO</strong>
			<form class="o-checkout__form" method="post" action="<?= url('api/shop/checkout') ?>" hidden>
				<div class="o-checkout__abstract">
					<?= $page->abstract()->kt() ?>
				</div>
				<div class="cart">
					<div class="cart__name">Merx License</div>
					<div class="cart__quantity">
						<span aria-label="quantity"><?= $cart->quantity() ?></span>
						<button type="button" data-action="decrease" aria-label="decrease quantity">−</button>
						<button type="button" data-action="increase" aria-label="increase quantity">+</button>
					</div>
					<div class="cart__sum"><?= $cart->total()->toString('priceNet') ?></div>
					<div class="cart__tax">+ Vat (<?= $cart->total()->tax->toString('rate') ?>) <?= $cart->tax() ?></div>
					<div class="cart__total"><?= $cart->total() ?></div>
				</div>
				<div class="form-checkout">
					<h3>Contact Infomation</h3>
					<label data-name="email">
						<div>Email Address</div>
						<input type="email" name="email" placeholder="contact@nicenewshoes.com" autocomplete="email" required>
						<small>We will send your license key to this email address.</small>
					</label>
					<h3>Billing Address</h3>
					<label data-name="company">
						<div>Company</div>
						<input type="text" name="company" placeholder="Nice New Shoes Inc" autocomplete="organization">
					</label>
					<label data-name="name">
						<div>Full Name</div>
						<input type="text" name="name" placeholder="Margret L Torreggiani" autocomplete="name">
					</label>
					<label data-name="street">
						<div>Street Address</div>
						<input type="text" name="street" required placeholder="50 Kintyre Street" autocomplete="billing street-address">
					</label>
					<label class="is-1-3" data-name="postal_code">
						<div>Postal Code / ZIP</div>
						<input type="text" name="postal_code" placeholder="4133" autocomplete="billing postal-code">
					</label>
					<label class="is-2-3" data-name="city">
						<div>City</div>
						<input type="text" name="city" required placeholder="Waterford" autocomplete="billing locality">
					</label>
					<label class="is-2-3" data-name="city">
						<div>State/Province/Region</div>
						<input type="text" name="state" placeholder="Queensland" autocomplete="billing region">
					</label>
					<label data-name="country">
						<div>Country</div>
						<div class="select-wrapper">
							<select name="country" required autocomplete="country">
								<?php foreach(option('wagnerwagner.site.countryList') as $code => $name): ?>
									<option value="<?= $code ?>" <?= $region === $code ? 'selected' : '' ?>><?= $name ?></option>
								<?php endforeach; ?>
							</select>
						</div>
					</label>
					<div class="form-checkout__payment-fields"></div>
					<input type="hidden" name="payment-gateway" value="stripe-elements">
					<input type="hidden" name="stripePublishableKey" value="<?= option('wagnerwagner.merx.production') === true ? option('wagnerwagner.merx.stripe.live.publishable_key') : option('wagnerwagner.merx.stripe.test.publishable_key') ?>">

					<label class="has-radio" data-name="legal">
						<input type="checkbox" name="legal" required>
						<div>I agree the <a href="<?= page('privacy')->url() ?>" target="_blank">Privacy Policy</a>.</div>
					</label>

					<div class="form-checkout__submit">
						<button type="submit">buy</button>
					</div>
				</div>
			</form>
		</div>
	</main>
	<?php snippet('o-footer') ?>
</body>
<?php snippet('foot') ?>
