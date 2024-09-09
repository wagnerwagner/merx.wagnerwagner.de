<?php

use Kirby\Cms\Field;

class OrderPage extends OrderPageAbstract
{
  public function errors(): array
  {
    $errors = parent::errors();
    if ($this->company()->isEmpty() && $this->name()->isEmpty()) {
      $additionalErrors['company'] = [
        'label' => 'Company',
        'message' => [
          'required' => 'Please enter either Full Name or Company.'
        ],
      ];
      $errors = array_merge($errors, $additionalErrors);
    }
    return $errors;
  }

  public function paymentMethodName(): string
  {
    $paymentMethodKey = (string)$this->paymentMethod();
    $paymentMethods = [];
    $paymentMethods['credit-card-sca'] = 'Credit Card';
    $paymentMethods['credit-card'] = 'Credit Card';
    $paymentMethods['paypal'] = 'PayPal';
    $paymentMethods['sepa-debit'] = 'SEPA Direct Debit';
    $paymentMethods['sofort'] = 'Klarna';
    $paymentMethods['klarna'] = 'Klarna';
    $paymentMethods['ideal'] = 'iDEAL';
    if (key_exists($paymentMethodKey, $paymentMethods)) {
      return $paymentMethods[$paymentMethodKey];
    }
    return $paymentMethodKey;
  }

  public function title(): Field
    {
      return new Field($this, 'title', 'Invoice ' . $this->invoiceNumber());
    }
};
