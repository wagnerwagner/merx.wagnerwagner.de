<?php
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

};
