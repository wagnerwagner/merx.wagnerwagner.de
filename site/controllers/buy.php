<?php

return function () {
  $cart = merx()->cart([
    [
      'id' => 'merx-license',
    ],
  ]);
  return [
    'cart' => $cart,
    'region' => kirby()->visitor()->acceptedLanguages()->first()->region(),
  ];
};
