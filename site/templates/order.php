<?php snippet('head') ?>
<body class="l-invoice">
  <header class="header">
    <div class="center">
      <h1>
        <a href="<?= $site->url() ?>">
          <img src="<?= url('assets/images/logo.svg') ?>" alt="<?= $site->title() ?>">
        </a>
      </h1>
    </div>
  </header>
  <main class="invoice" aria-label="Invoice">
    <header class="invoice__header">
      <h1>Invoice</h1>
      <p>
        Invoice Number: <?= $page->invoiceNumber() ?><br>
        Date: <?= $page->invoiceDate()->toDate('Y-m-d h:i a') ?>
      </p>
    </header>
    <div class="invoice__address">
      <h2>Billing Address</h2>
      <p>
        <?= $page->company()->isNotEmpty() ? $page->company()->html() . '<br>' : '' ?>
        <?= $page->name()->isNotEmpty() ? $page->name()->html() . '<br>' : '' ?>
        <?= $page->street()->isNotEmpty() ? $page->street()->html() . '<br>' : '' ?>
        <?= $page->postal_code()->isNotEmpty() ? $page->postal_code()->html() . ' ' : '' ?>
        <?= $page->city()->isNotEmpty() ? $page->city()->html() . '<br>' : '' ?>
        <?= $page->state()->isNotEmpty() ? $page->state()->html() . '<br>' : '' ?>
        <?= option('wagnerwagner.site.countryList')[(string)$page->country()] ?>
      </p>
      <p>
        <?= $page->email()->isNotEmpty() ? $page->email()->html() . '<br>' : '' ?>
      </p>
    </div>
    <div class="invoice__purchase">
      <h2>Purchase</h2>
      <p>You bought <?= $amount === 1 ? '<strong>one</strong> Merx License' : '<strong>' . $amount . '</strong> Merx Licenses' ?>. You chosed <?= $page->paymentMethod() ?> as your payment method.</p>
      <table>
        <thead>
          <tr>
            <th>Product</th>
            <th>Amount</th>
            <th>Price</th>
            <th>Sum</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach($cart as $item): ?>
          <tr>
            <td><?= $item['title'] ?></td>
            <td><?= $item['quantity'] ?></td>
            <td><?= formatPrice($item['price'] - $item['tax']) ?></td>
            <td><?= formatPrice($item['sum'] - $item['sumTax']) ?></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
        <tfoot>
          <tr>
            <th colspan="3">Gross</th>
            <td><?= formatPrice($cart->getSum() - $cart->getTax()) ?></td>
          </tr>
          <tr>
            <th colspan="3">+ Vat (19%)</th>
            <td><?= formatPrice($cart->getTax()) ?></td>
          </tr>
          <tr>
            <th colspan="3">Sum</th>
            <td><?= formatPrice($cart->getSum()) ?></td>
          </tr>
        </tfoot>
      </table>
    </div>
    <div class="">
      <h2><?= count($licenses) === 1 ? 'License Key' : 'License Keys' ?></h2>
      <p>
      <?php foreach ($licenses as $item) :?>
        <code><?= $item ?></code><br>
      <?php endforeach; ?>
      </p>
    </div>
  </main>
  <?php snippet('footer') ?>
</body>
<?php snippet('foot') ?>
