<?php
use Wagnerwagner\Merx\Merx;
?>
<?php snippet('head') ?>
<body>
  <?php snippet('header') ?>
  <?php if ($message = Merx::getMessage()): ?>
    <div class="message">
      <div><?= $message ?></div>
    </div>
  <?php endif; ?>
  <main class="buy">
    <form class="buy__form" method="post" action="merx-api/buy">
      <div class="buy__abstract">
        <?= $page->abstract()->kt() ?>
      </div>
      <div class="cart">
        <div class="cart__name">Merx License</div>
        <div class="cart__quantity">
          <span aria-label="quantity">1</span>
          <button type="button" data-action="decrease" aria-label="decrease quantity">−</button>
          <button type="button" data-action="increase" aria-label="increase quantity">+</button>
        </div>
        <div class="cart__sum"><?= formatPrice($cart->getSum() - $cart->getTax()) ?></div>
        <div class="cart__tax">+ Vat (19%) <?= formatPrice($cart->getTax()) ?></div>
        <div class="cart__total"><?= formatPrice($cart->getSum()) ?></div>
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
        <h3>Payment Method</h3>
        <div class="form-checkout__payment-method">
          <label class="has-radio">
            <input type="radio" name="paymentMethod" required value="credit-card-sca">
            <div>Credit Card</div>
          </label>
          <label class="has-radio">
            <input type="radio" name="paymentMethod" required value="paypal" data-submit-text="continue to PayPal">
            <div>PayPal</div>
          </label>
          <label class="has-radio">
            <input type="radio" name="paymentMethod" required value="sepa-debit">
            <div>SEPA Direct Debit</div>
          </label>
          <label class="has-radio">
            <input type="radio" name="paymentMethod" required value="sofort" data-submit-text="continue to Klarna">
            <div>Klarna</div>
          </label>
        </div>
        <div class="form-checkout__payment-fields">
          <input type="hidden" name="stripePublishableKey" value="<?= option('ww.merx.production') === true ? option('ww.merx.stripe.live.publishable_key') : option('ww.merx.stripe.test.publishable_key') ?>">

          <label class="is-required" hidden data-payment-method="credit-card-sca">
            <strong>Credit Card</strong>
            <div id="stripe-card"></div>
          </label>

          <label class="is-required" hidden data-payment-method="sepa-debit">
            <strong>IBAN</strong>
            <div id="stripe-sepa-debit"></div>
            <small><?= $site->ibanInfo() ?></small>
          </label>
        </div>

        <label class="has-radio" data-name="legal">
          <input type="checkbox" name="legal" required>
          <div>I agree the <a href="<?= page('privacy')->url() ?>" target="_blank">Privacy Policy</a>.</div>
        </label>

        <div class="form-checkout__submit">
          <button type="submit">buy</button>
        </div>
      </div>
    </form>
  </main>
  <?php snippet('footer') ?>
  <script src="https://js.stripe.com/v3/"></script>
</body>
<?php snippet('foot') ?>
