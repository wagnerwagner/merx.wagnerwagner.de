/** @typedef {import('@stripe/stripe-js').Stripe} Stripe */
/** @typedef {import('@stripe/stripe-js').StripeElements} StripeElements */

class OCheckout {
	/** Delay before a change of the quantity is sent, so fast clicks are sent once */
	updateDelay = 400;

	/** @param {HTMLElement} element */
	constructor(element) {
		this.element = element;
		this.formElement = element.querySelector('form');
		this.cartElement = element.querySelector('.m-cart');
		this.submitElement = this.formElement.querySelector('[type="submit"]');

		/** @type {number} */
		this.quantity = Number(this.cartElement.querySelector('[data-cart="quantity"]').textContent);

		/** @type {Promise<void>|undefined} Stripe.js and its fields, loaded once */
		this.stripeLoaded = undefined;

		/** @type {StripeElements[]} Elements of the Apple Pay and Google Pay buttons */
		this.walletElements = [];

		/** @type {Promise|null} Cart update in progress */
		this.cartUpdate = null;

		/** @type {{country?: string, quantity?: number}} Changes waiting to be sent */
		this.pendingChanges = {};

		this.cartElement.querySelectorAll('[data-action]').forEach((button) => {
			button.addEventListener('click', () => {
				this.changeQuantity(button.dataset.action === 'increase' ? 1 : -1);
			});
		});

		this.formElement.elements.country.addEventListener('change', () => {
			this.queueCartUpdate({ country: this.formElement.elements.country.value }, 0);
		});

		this.formElement.querySelectorAll('[name="paymentMethod"]').forEach((input) => {
			input.addEventListener('change', () => this.choosePaymentMethod(input));
			// A browser restores the choice on reload or back navigation
			if (input.checked) {
				this.choosePaymentMethod(input);
			}
		});

		this.formElement.addEventListener('submit', (event) => this.submit(event));

		// Right away, because only Stripe knows whether to offer Apple Pay and Google Pay
		this.loadStripe().then(
			// Without them, the other payment methods still work
			() => this.mountWallets().catch((error) => console.error(error)),
			(error) => this.showError(error.message ?? error),
		);
	}

	/**
	 * Shows the new quantity right away and sends it once the clicks stop
	 *
	 * @param {number} delta
	 */
	changeQuantity(delta) {
		const quantity = Math.max(1, this.quantity + delta);
		if (quantity === this.quantity) {
			return;
		}

		this.quantity = quantity;
		this.cartElement.querySelector('[data-cart="quantity"]').textContent = quantity;
		this.cartElement.querySelector('[data-action="decrease"]').disabled = quantity <= 1;

		this.queueCartUpdate({ quantity });
	}

	/**
	 * @param {{country?: string, quantity?: number}} changes
	 * @param {number} delay
	 */
	queueCartUpdate(changes, delay = this.updateDelay) {
		this.setBusy(true);
		Object.assign(this.pendingChanges, changes);
		clearTimeout(this.updateTimeout);
		this.updateTimeout = setTimeout(() => this.runCartUpdate(), delay);
	}

	get hasPendingChanges() {
		return Object.keys(this.pendingChanges).length > 0;
	}

	/** Sends the pending changes, waiting for an update that is already running */
	async runCartUpdate() {
		clearTimeout(this.updateTimeout);
		await this.cartUpdate;

		const { country, quantity } = this.pendingChanges;
		this.pendingChanges = {};
		if (country === undefined && quantity === undefined) {
			return;
		}

		this.cartUpdate = (async () => {
			try {
				let cart;
				// The country first: it decides the VAT of the quantity’s price
				if (country !== undefined) {
					cart = await this.request('/api/checkout/region', 'POST', { country });
				}
				if (quantity !== undefined) {
					cart = await this.request('/api/shop/cart', 'PATCH', { key: 'merx-license', quantity });
				}
				this.renderCart(cart);
				await this.updateWallets();
			} catch (error) {
				this.showError(error.message ?? error);
			} finally {
				this.cartUpdate = null;
				this.setBusy(this.hasPendingChanges);
			}
		})();

		await this.cartUpdate;
	}

	/**
	 * @param {string} url
	 * @param {string} method
	 * @param {object} body
	 * @returns {Promise<object>} The cart
	 */
	// eslint-disable-next-line class-methods-use-this
	async request(url, method, body) {
		const response = await fetch(url, {
			method,
			headers: {
				Accept: 'application/json',
				'Content-Type': 'application/json',
			},
			body: JSON.stringify(body),
		});
		const json = await response.json();
		if (json.status === 'error') {
			throw json.message;
		}
		return json.data;
	}

	/** @param {object} cart Cart as returned by the Merx API */
	renderCart(cart) {
		this.cartElement.querySelector('[data-action="decrease"]').disabled = cart.quantity <= 1;

		const totalsElement = this.cartElement.querySelector('.m-cart__totals');
		totalsElement.innerHTML = `
			${cart.taxRates.length > 0 ? `
				<div>
					<dt>Subtotal</dt>
					<dd>${cart.total?.priceNet}</dd>
				</div>
				${(cart.taxRates ?? []).map((taxRate) => `
					<div><dt>VAT ${taxRate.rate}</dt><dd>${taxRate.price}</dd></div>
				`).join('')}
			` : ''}
			<div class="m-cart__total">
				<dt>Total</dt>
				<dd>${cart.total?.price}</dd>
			</div>
		`;
		this.formElement.querySelector('[data-cart="total"]').textContent = cart.total?.price;
	}

	/** @param {boolean} isBusy */
	setBusy(isBusy) {
		this.submitElement.disabled = isBusy;
		this.cartElement.toggleAttribute('data-busy', isBusy);
	}

	/** @param {SubmitEvent} event */
	submit(event) {
		event.preventDefault();

		const paymentMethod = this.formElement.querySelector('[name="paymentMethod"]:checked');
		// Apple Pay and Google Pay are paid with their own button
		if (paymentMethod.dataset.wallet !== undefined) {
			return;
		}

		this.placeOrder(paymentMethod, (clientSecret, redirectUrl, formData) => (
			this.confirmStripePayment(paymentMethod.value, clientSecret, redirectUrl, formData)
		));
	}

	/**
	 * Sends the order to Merx, and to Stripe for a payment method of Stripe
	 *
	 * @param {HTMLInputElement} paymentMethod Radio button of the payment method
	 * @param {(clientSecret: string, redirectUrl: string, formData: FormData) => Promise<void>} confirmStripePayment Pays at Stripe once Merx has accepted the order
	 * @returns {Promise<boolean>} Whether the customer is on the way to the order page or to pay
	 */
	async placeOrder(paymentMethod, confirmStripePayment) {
		this.clearErrors();

		// The payment has to be for the cart the customer sees
		if (this.hasPendingChanges) {
			await this.runCartUpdate();
		}
		await this.cartUpdate;

		this.setBusy(true);

		try {
			const formData = new FormData(this.formElement);
			formData.set('paymentGateway', paymentMethod.dataset.gateway);

			// Only Stripe needs a payment intent. Merx adds the one of the session
			// to the order, so it is fetched before the checkout, and fetched now
			// it holds the total of the cart that is submitted.
			let clientSecret;
			if (paymentMethod.dataset.gateway === 'stripe') {
				await this.loadStripe();
				clientSecret = await this.getClientSecret();
			}

			const response = await fetch(this.formElement.action, {
				method: 'post',
				body: formData,
				headers: {
					Accept: 'application/json',
				},
			});
			const json = await response.json();

			if (json.status === 'redirect') {
				if (clientSecret !== undefined) {
					await confirmStripePayment(clientSecret, json.redirectUrl, formData);
				}
				// The order page, or PayPal
				window.location.href = json.redirectUrl;
				return true;
			}

			if (json.key === 'error.merx.fieldsvalidation') {
				this.showFieldErrors(json.details);
			} else {
				throw json.message ?? 'Fatal error';
			}
		} catch (error) {
			this.showError(error.message ?? error);
		} finally {
			this.setBusy(false);
		}

		return false;
	}

	/** @param {object} details */
	showFieldErrors(details) {
		Object.entries(details).forEach(([key, error]) => {
			const labelElement = this.formElement.querySelector(`label:has([name="${key}"])`);
			Object.values(error.message).forEach((errorMessage) => {
				if (labelElement) {
					labelElement.append(OCheckout.createErrorElement(errorMessage));
				} else {
					this.showError(errorMessage);
				}
			});
		});
	}

	/** @param {string} errorMessage */
	static createErrorElement(errorMessage) {
		return Object.assign(document.createElement('p'), {
			className: 'm-form-checkout__error',
			textContent: errorMessage,
		});
	}

	/** Displays a general error message above the submit button */
	showError(/** @type {string} */ errorMessage) {
		const submitWrapper = this.submitElement.parentElement;
		const errorElement = OCheckout.createErrorElement(errorMessage);
		submitWrapper.parentElement.insertBefore(errorElement, submitWrapper);
	}

	clearErrors() {
		this.formElement.querySelectorAll('.m-form-checkout__error').forEach((element) => element.remove());
	}

	/**
	 * Shows the fields of a payment method and hides those of the others
	 *
	 * @param {HTMLInputElement} input Radio button of the payment method
	 */
	choosePaymentMethod(input) {
		this.formElement.querySelectorAll('[data-payment-method]').forEach((element) => {
			element.hidden = element.dataset.paymentMethod !== input.value;
		});

		// E.g. “Continue to PayPal” instead of the total
		const { submitText } = input.dataset;
		this.submitElement.querySelector('[data-submit="pay"]').hidden = submitText !== undefined;
		Object.assign(this.submitElement.querySelector('[data-submit="method"]'), {
			hidden: submitText === undefined,
			textContent: submitText ?? '',
		});

		// Apple Pay and Google Pay bring their own button
		this.submitElement.hidden = input.dataset.wallet !== undefined;
	}

	/**
	 * Loads Stripe.js and mounts the card and IBAN fields, once
	 *
	 * @returns {Promise<void>}
	 */
	loadStripe() {
		this.stripeLoaded ??= new Promise((resolve, reject) => {
			const script = document.createElement('script');
			// The Stripe.js release of the Stripe API version Merx uses
			// https://docs.stripe.com/sdks/stripejs-versioning
			script.src = 'https://js.stripe.com/endive/stripe.js';
			script.addEventListener('load', resolve);
			script.addEventListener('error', () => reject(new Error('Stripe could not be loaded. Please reload the page.')));
			document.head.append(script);
		}).then(() => {
			/** @type {Stripe} */
			this.stripe = window.Stripe(this.formElement.elements.stripePublishableKey.value);

			/** @type {StripeElements} */
			this.stripeElements = this.stripe.elements({
				locale: document.documentElement.lang,
				fonts: [
					{
						cssSrc: '/assets/css/fonts.css',
					},
				],
			});

			// The fields live in an iframe, so they take the look of the other
			// fields from them. Their box is styled by `.m-field__stripe`.
			const inputStyle = getComputedStyle(this.formElement.elements.email);
			const style = {
				base: {
					fontFamily: inputStyle.fontFamily,
					fontSize: inputStyle.fontSize,
					fontWeight: inputStyle.fontWeight,
					color: inputStyle.color,
					'::placeholder': {
						color: getComputedStyle(this.formElement.elements.email, '::placeholder').color,
					},
				},
				// `.m-field__stripe` turns the border red instead
				invalid: {
					color: inputStyle.color,
				},
			};

			// https://docs.stripe.com/js/elements_object/create_element
			this.stripeElements.create('card', {
				style,
				// The billing address is part of the form
				hidePostalCode: true,
				disableLink: true,
			}).mount('#stripe-card');
			this.stripeElements.create('iban', {
				style,
				supportedCountries: ['SEPA'],
			}).mount('#stripe-iban');
		});

		return this.stripeLoaded;
	}

	/**
	 * Creates the payment intent of the cart at Stripe, or updates it to the
	 * cart’s total, and returns its client secret
	 *
	 * @returns {Promise<string>}
	 */
	async getClientSecret() {
		const response = await fetch('/api/shop/stripe-client-secret');
		const json = await response.json();
		if (json.clientSecret === undefined) {
			throw json.message ?? 'Fatal error';
		}
		return json.clientSecret;
	}

	/**
	 * Offers Apple Pay and Google Pay where Stripe says the browser can pay
	 * with them. Stripe shows neither before the domain is registered:
	 * https://docs.stripe.com/payments/payment-methods/pmd-registration
	 *
	 * Their buttons stand in for the submit button, because only a click on
	 * them may open the wallet. The wallet pays the payment intent of the
	 * session, so it is created now, and it is the one Merx adds to the order.
	 *
	 * @see https://docs.stripe.com/elements/express-checkout-element
	 */
	async mountWallets() {
		const inputs = [...this.formElement.querySelectorAll('[name="paymentMethod"][data-wallet]')];
		if (inputs.length === 0) {
			return;
		}

		const clientSecret = await this.getClientSecret();

		this.walletElements = inputs.map((input) => {
			const { wallet } = input.dataset;

			// One element for each wallet, so it shows the button of this wallet
			// only. An element without any button never reports one available.
			/** @type {StripeElements} */
			const elements = this.stripe.elements({
				clientSecret,
				locale: document.documentElement.lang,
			});
			const expressCheckout = elements.create('expressCheckout', {
				paymentMethods: {
					amazonPay: 'never',
					applePay: 'never',
					googlePay: 'never',
					klarna: 'never',
					link: 'never',
					paypal: 'never',
					// Also when the wallet holds no card yet
					[wallet]: 'always',
				},
				buttonType: {
					applePay: 'buy',
					googlePay: 'buy',
				},
				// As high as the submit button, within the 40 to 55 pixels Stripe allows
				buttonHeight: Math.min(55, Math.max(40, Math.round(this.submitElement.getBoundingClientRect().height))),
			});

			expressCheckout.on('ready', ({ availablePaymentMethods }) => {
				input.closest('label').hidden = availablePaymentMethods?.[wallet] !== true;
			});

			expressCheckout.on('click', (event) => {
				// The wallet only pays: email and billing address come from the
				// form, which has to be complete. Stripe waits 1 second at most.
				if (this.submitElement.disabled || this.formElement.reportValidity() === false) {
					event.reject();
					return;
				}
				event.resolve();
			});

			expressCheckout.on('confirm', async (event) => {
				const placed = await this.placeOrder(input, async (_, redirectUrl) => {
					// https://docs.stripe.com/js/payment_intents/confirm_payment
					const { error } = await this.stripe.confirmPayment({
						elements,
						confirmParams: {
							return_url: redirectUrl,
						},
						// A card of the wallet is paid without leaving the page
						redirect: 'if_required',
					});
					if (error) {
						throw error.message;
					}
				});
				if (placed === false) {
					event.paymentFailed();
				}
			});

			expressCheckout.mount(`#express-checkout-${input.value}`);

			return elements;
		});
	}

	/** The wallets show the amount of the payment intent, which follows the cart */
	async updateWallets() {
		if (this.walletElements.length === 0) {
			return;
		}
		await this.getClientSecret();
		await Promise.all(this.walletElements.map((elements) => elements.fetchUpdates()));
	}

	/**
	 * Pays with the payment method of Stripe the customer chose. Stripe asks
	 * for 3-D Secure in a dialog of its own when the bank requires it.
	 *
	 * @param {string} paymentMethod Type of the payment method at Stripe, e.g. `card`
	 * @param {string} clientSecret
	 * @param {string} redirectUrl Where Merx completes the order
	 * @param {FormData} formData
	 */
	async confirmStripePayment(paymentMethod, clientSecret, redirectUrl, formData) {
		const billingDetails = {
			// SEPA Direct Debit and Klarna require a name
			name: formData.get('name') || formData.get('company'),
			email: formData.get('email'),
			// Stripe refuses an empty string, e.g. of the optional state
			address: Object.fromEntries(Object.entries({
				line1: formData.get('street'),
				postal_code: formData.get('postal_code'),
				city: formData.get('city'),
				state: formData.get('state'),
				country: formData.get('country'),
			}).filter(([, value]) => value)),
		};

		let result;
		if (paymentMethod === 'card') {
			// https://docs.stripe.com/js/payment_intents/confirm_card_payment
			result = await this.stripe.confirmCardPayment(clientSecret, {
				payment_method: {
					card: this.stripeElements.getElement('card'),
					billing_details: billingDetails,
				},
			});
		} else if (paymentMethod === 'sepa_debit') {
			// https://docs.stripe.com/js/payment_intents/confirm_sepa_debit_payment
			result = await this.stripe.confirmSepaDebitPayment(clientSecret, {
				payment_method: {
					sepa_debit: this.stripeElements.getElement('iban'),
					billing_details: billingDetails,
				},
			});
		} else {
			// The others send the customer to pay elsewhere, e.g. to Klarna,
			// and from there to the order page
			// https://docs.stripe.com/js/payment_intents/confirm_payment
			result = await this.stripe.confirmPayment({
				clientSecret,
				confirmParams: {
					payment_method_data: {
						type: paymentMethod,
						billing_details: billingDetails,
					},
					return_url: redirectUrl,
				},
			});
		}

		if (result.error) {
			// E.g. a declined card, which the customer can correct
			throw result.error.message;
		}
	}
}

document.querySelectorAll('.o-checkout').forEach((_) => new OCheckout(_));
