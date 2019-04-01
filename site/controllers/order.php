<?php
return function () {
  $cart = page()->cart();
  return [
    'cart' => $cart,
    'amount' => $cart->findBy('id', 'merx-license')['quantity'],
    'licenses' => explode(', ', page()->licenses()),
  ];
};
