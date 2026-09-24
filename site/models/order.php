<?php

use Kirby\Content\Field;
use Kirby\Toolkit\Str;

class OrderPage extends Wagnerwagner\Merx\OrderPage
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

	/**
	 * Name of the payment method the customer paid with, e.g. `Apple Pay (Visa)`
	 *
	 * Read from the payment details Merx stores for the order. An order without
	 * them (e.g. an invoice) falls back to the payment gateway.
	 */
	public function paymentMethodName(): string
	{
		$details = $this->paymentDetails();
		$method = $details['payment_method'] ?? null;

		if ($method === null && $this->paymentProvider() === 'paypal') {
			$method = 'paypal';
		}

		if ($method === 'card') {
			$brand = $details['card_brand'] ?? null;
			$brand = match ($brand) {
				null => 'Card',
				'amex' => 'American Express',
				'diners' => 'Diners Club',
				'unionpay' => 'UnionPay',
				default => Str::ucfirst($brand),
			};

			$wallet = $details['card_wallet'] ?? null;
			$wallet = match ($wallet) {
				null => null,
				'apple_pay' => 'Apple Pay',
				'google_pay' => 'Google Pay',
				default => Str::ucwords(Str::replace($wallet, '_', ' ')),
			};

			return $wallet ? $wallet . ' (' . $brand . ')' : $brand;
		}

		return match ($method) {
			null => Str::ucwords(Str::replace((string)$this->paymentGateway(), '-', ' ')),
			'paypal' => 'PayPal',
			'sepa_debit' => 'SEPA Direct Debit',
			'ideal' => 'iDEAL',
			default => Str::ucwords(Str::replace($method, '_', ' ')),
		};
	}

	public function title(): Field
	{
		return new Field($this, 'title', 'Invoice ' . $this->orderNumber());
	}
};
