<?php

use Kirby\Content\Field;
use Kirby\Filesystem\F;
use Kirby\Toolkit\Str;

class OrderPage extends Wagnerwagner\Merx\OrderPage
{
	/** Member states of the EU */
	private const EU_COUNTRIES = ['AT', 'BE', 'BG', 'CY', 'CZ', 'DE', 'DK', 'EE', 'ES', 'FI', 'FR', 'GR', 'HR', 'HU', 'IE', 'IT', 'LT', 'LU', 'LV', 'MT', 'NL', 'PL', 'PT', 'RO', 'SE', 'SI', 'SK'];

	/**
	 * Whether the order was cancelled in the Panel
	 */
	public function isCancelled(): bool
	{
		return $this->cancelled()->toBool();
	}

	/**
	 * License keys of the order. Those of a cancelled order are masked,
	 * e.g. `MERX2-RXXXXXXP-DXXXXXXE`.
	 *
	 * @return string[]
	 */
	public function licenseKeys(): array
	{
		$keys = $this->licenses()->split(',');

		if ($this->isCancelled() === false) {
			return $keys;
		}

		return array_map(function (string $key): string {
			$segments = explode('-', $key);
			foreach ($segments as $index => $segment) {
				// The prefix says which Merx the key is for
				if ($index > 0 && strlen($segment) > 2) {
					$segments[$index] = $segment[0] . str_repeat('X', strlen($segment) - 2) . $segment[-1];
				}
			}
			return implode('-', $segments);
		}, $keys);
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

	/**
	 * E-invoice as Factur-X/ZUGFeRD XML, profile EN 16931, see `hasEInvoice()`
	 *
	 * Written on the first call, which is when the order is created, and
	 * unchanged afterwards.
	 */
	public function eInvoiceXml(): string|null
	{
		if ($this->hasEInvoice() === false) {
			return null;
		}

		$file = $this->root() . '/factur-x.xml';
		if ($xml = F::read($file)) {
			return $xml;
		}

		$xml = trim(snippet('factur-x', ['order' => $this], true)) . "\n";
		F::write($file, $xml);
		return $xml;
	}

	/**
	 * Whether the order gets an e-invoice: only a customer in Germany has to
	 */
	public function hasEInvoice(): bool
	{
		return Str::upper((string)$this->country()) === 'DE';
	}

	/**
	 * VAT category of the order, as EN 16931 names it
	 *
	 * - `S`: VAT is charged, for a customer in Germany
	 * - `AE`: reverse charge, the customer in another EU country owes the VAT
	 * - `O`: not taxable in Germany, the customer is outside the EU
	 */
	public function vatCategory(): string
	{
		return match (true) {
			$this->cart()->taxRates() !== [] => 'S',
			in_array(Str::upper((string)$this->country()), self::EU_COUNTRIES, true) => 'AE',
			default => 'O',
		};
	}

	/**
	 * Why the invoice charges no VAT, or null when it does
	 */
	public function vatNotice(): string|null
	{
		return match ($this->vatCategory()) {
			'AE' => 'This invoice is issued under the reverse charge procedure. Therefore, no VAT is charged. The recipient of the supply is liable for the VAT and has to declare and pay it in accordance with the reverse charge mechanism (Art. 196 VAT Directive).',
			'O' => 'Not subject to German VAT: the place of supply is outside the EU (Section 3a German VAT Act).',
			default => null,
		};
	}

	public function title(): Field
	{
		// A draft has no order number yet. Merx titles it “Incomplete”.
		if ($this->isDraft()) {
			return parent::title();
		}

		return new Field($this, 'title', 'Invoice ' . $this->orderNumber());
	}
}
