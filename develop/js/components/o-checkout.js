/** @typedef {import('@stripe/stripe-js').Stripe} Stripe */
/** @typedef {import('@stripe/stripe-js').StripeElements} StripeElements */
/** @typedef {import('@stripe/stripe-js').StripePaymentElement} StripePaymentElement */

class OCheckout {
	stripeElementsAppearance = {
		theme: 'flat',
		labels: 'above',
		disableAnimations: true,
		variables: {
			fontFamily: "'TT Norms M', 'TT Norms', Avenir, sans-serif",
			borderRadius: '2px',
			inputColorBorder: 'hsl(0, 0%, 33%)',
			tabLogoSelectedColor: 'dark',
			colorBackground: '#fff',
			fontSizeSm: '1rem',
			fontSizeLg: '1.125rem',
			fontSizeXl: '1.4375rem',
			fontLineHeight: '1.25',
			fontWeightMedium: '500',
			fontSize2Xl: '1.4375rem',
			fontSize2Xs: '0.75rem',
			fontWeightNormal: '450',
			colorText: 'hsl(0, 0%, 13%)',
			colorPrimary: 'hsl(0, 0%, 13%)',
			colorDanger: 'hsl(0, 60%, 60%)',
			colorSuccess: 'hsl(103, 64%, 22%)',
			colorWarning: 'hsl(60, 80%, 60%)',
			colorTextSecondary: 'hsl(0, 0%, 40%)',
			colorTextPlaceholder: 'hsl(0, 0%, 60%)',
			iconColor: 'var(--colorPrimary)',
			gridColumnSpacing: '1.25rem',
			gridRowSpacing: '0.75rem',
			accordionItemSpacing: '0.75rem',
		},
		rules: {
			'.Input': {
				border: '1px solid hsl(0, 0%, 60%)',
				padding: '0.5rem 0.4375rem',
			},
			'.Tab--selected, .Tab--selected:focus': {
				border: 'none',
				boxShadow: 'none',
			},
			'.Tab:focus': {
				boxShadow: 'none',
				outline: '2px solid Highlight',
				outlineOffset: '2px',
			},
			'.Input:focus': {
				boxShadow: 'none',
				outline: '2px solid Highlight',
				outlineOffset: '2px',
			},
			'.Tab': {
				padding: '0.5rem 0.75rem',
			},
			'.TabLabel': {
				paddingTop: '0.5rem',
			},
		},
	};

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

		/** @type {Stripe} */
		this.stripe = window.Stripe(this.formElement.elements.stripePublishableKey.value);

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

		this.formElement.addEventListener('submit', (event) => this.submit(event));

		this.mountPaymentElement().catch((error) => this.showError(error.message ?? error));
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

				// A payment intent is created for the total of one cart. A changed
				// total needs a new one, or Merx refuses the payment.
				await this.mountPaymentElement();
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
		const set = (name, value) => {
			this.element.querySelectorAll(`[data-cart="${name}"]`).forEach((element) => {
				// eslint-disable-next-line no-param-reassign
				element.textContent = value ?? '';
			});
		};

		this.quantity = cart.quantity;
		set('quantity', cart.quantity);
		set('price', cart.products?.items[0]?.price?.priceNet);
		set('subtotal', cart.total?.priceNet);
		set('total', cart.total?.price);
		this.cartElement.querySelector('[data-action="decrease"]').disabled = cart.quantity <= 1;

		const totalsElement = this.cartElement.querySelector('.m-cart__totals');
		totalsElement.querySelectorAll('[data-cart="tax"]').forEach((element) => element.remove());
		(cart.taxRates ?? []).forEach((tax) => {
			const row = document.createElement('div');
			row.dataset.cart = 'tax';
			row.append(
				Object.assign(document.createElement('dt'), { textContent: `VAT ${tax.rate}` }),
				Object.assign(document.createElement('dd'), { textContent: tax.price }),
			);
			totalsElement.insertBefore(row, totalsElement.querySelector('.m-cart__total'));
		});
	}

	/** @param {boolean} isBusy */
	setBusy(isBusy) {
		this.submitElement.disabled = isBusy;
		this.cartElement.toggleAttribute('data-busy', isBusy);
	}

	/** @param {SubmitEvent} event */
	async submit(event) {
		event.preventDefault();
		this.clearErrors();

		// The payment has to be for the cart the customer sees
		if (this.hasPendingChanges) {
			await this.runCartUpdate();
		}
		await this.cartUpdate;

		this.setBusy(true);

		try {
			const formData = new FormData(this.formElement);

			const response = await fetch(this.formElement.action, {
				method: 'post',
				body: formData,
				headers: {
					Accept: 'application/json',
				},
			});
			const json = await response.json();

			if (json.status === 'redirect') {
				// Stripe redirects to the order page once the payment is confirmed
				await this.completeStripePayment(json.redirectUrl, formData);
			} else if (json.key === 'error.merx.fieldsvalidation') {
				this.showFieldErrors(json.details);
			} else {
				throw json.message ?? 'Fatal error';
			}
		} catch (error) {
			this.showError(error.message ?? error);
		} finally {
			this.setBusy(false);
		}
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

	async completeStripePayment(redirectUrl, formData) {
		const { error } = await this.stripe.confirmPayment({
			// `Elements` instance that was used to create the Payment Element
			elements: this.stripeElements,
			confirmParams: {
				return_url: redirectUrl,
				payment_method_data: {
					billing_details: {
						// Some payment methods, like SEPA Direct Debit, require a name
						name: formData.get('name') || formData.get('company'),
						email: formData.get('email'),
						address: {
							country: formData.get('country'),
							city: formData.get('city'),
							line1: formData.get('street'),
							postal_code: formData.get('postal_code'),
							state: formData.get('state'),
						},
					},
				},
			},
		});

		if (error) {
			throw error.message;
		}
	}

	/**
	 * Creates the Payment Element for a new payment intent of the current cart
	 *
	 * @see https://docs.stripe.com/js/element/payment_element
	 */
	async mountPaymentElement() {
		const response = await fetch('/api/shop/stripe-client-secret');
		const json = await response.json();
		if (json.clientSecret === undefined) {
			throw json.message ?? 'Fatal error';
		}

		this.paymentElement?.destroy();

		/** @type {StripeElements} */
		this.stripeElements = this.stripe.elements({
			locale: document.querySelector('html').lang,
			loader: 'never',
			clientSecret: json.clientSecret,
			fonts: [
				{
					cssSrc: '/assets/css/fonts.css',
				},
			],
			appearance: this.stripeElementsAppearance,
		});

		/** @type {StripePaymentElement} */
		this.paymentElement = this.stripeElements.create('payment', {
			layout: {
				type: 'tabs',
			},
			fields: {
				billingDetails: {
					name: 'never',
					email: 'never',
					address: 'never',
				},
			},
		});
		this.paymentElement.mount('.m-form-checkout__payment-fields');
	}
}

document.querySelectorAll('.o-checkout').forEach((_) => new OCheckout(_));
