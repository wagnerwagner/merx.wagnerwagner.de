<?php
return function ($page) {
  $cart = $page->cart();
  return [
    'cart' => $cart,
    'quantity' => (int)$cart->findBy('id', 'merx-license')['quantity'],
    'licenses' => explode(', ', $page->licenses()),
  ];
};
