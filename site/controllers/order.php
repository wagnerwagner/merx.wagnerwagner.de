<?php
function getPaymentMethod($key) {
  $paymentMethods = [];
  $paymentMethods['credit-card'] = 'Credit Card';
  $paymentMethods['paypal'] = 'PayPal';
  $paymentMethods['sepa-debit'] = 'SEPA Direct Debit';
  if (key_exists($key, $paymentMethods)) {
    return $paymentMethods[$key];
  }
  return $key;
}

return function ($page) {
  $cart = $page->cart();
  return [
    'cart' => $cart,
    'amount' => (int)$cart->findBy('id', 'merx-license')['quantity'],
    'licenses' => explode(', ', $page->licenses()),
    'paymentMethod' => getPaymentMethod((string)$page->paymentMethod()),
  ];
};
