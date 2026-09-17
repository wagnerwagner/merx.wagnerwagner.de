/** @typedef {import('@stripe/stripe-js').Stripe} Stripe */

class OCheckout {
	stripeElementsAppearance = {
		"theme": "flat",
		"labels": "above",
		"disableAnimations": true,
		"variables": {
			"fontFamily": "'TT Norms M', 'TT Norms', Avenir, sans-serif",
			"borderRadius": "2px",
			"inputColorBorder": "hsl(0, 0%, 33%)",
			"tabLogoSelectedColor": "dark",
			"colorBackground": "#fff",
			"fontSizeSm": "1rem",
			"fontSizeLg": "1.125rem",
			"fontSizeXl": "1.4375rem",
			"fontLineHeight": "1.25",
			"fontWeightMedium": "500",
			"fontSize2Xl": "1.4375rem",
			"fontSize2Xs": "0.75rem",
			"fontWeightNormal": "450",
			"colorText": "hsl(0, 0%, 13%)",
			"colorPrimary": "hsl(0, 0%, 13%)",
			"colorDanger": "hsl(0, 60%, 60%)",
			"colorSuccess": "hsl(103, 64%, 22%)",
			"colorWarning": "hsl(60, 80%, 60%)",
			"colorTextSecondary": "hsl(0, 0%, 40%)",
			"colorTextPlaceholder": "hsl(0, 0%, 60%)",
			"iconColor": "var(--colorPrimary)",
			"gridColumnSpacing": "1.25rem",
			"gridRowSpacing": "0.75rem",
			"accordionItemSpacing": "0.75rem",
		},
		"rules": {
			".Input": {
				"border": "1px solid hsl(0, 0%, 60%)",
				"padding": "0.5rem 0.4375rem",
			},
			".Tab--selected, .Tab--selected:focus": {
				"border": "none",
				"boxShadow": "none",
			},
			".Tab:focus": {
				"boxShadow": "none",
				"outline": "2px solid Highlight",
				"outlineOffset": "2px",
			},
			".Input:focus": {
				"boxShadow": "none",
				"outline": "2px solid Highlight",
				"outlineOffset": "2px",
			},
			".Tab": {
				"padding": "0.5rem 0.75rem",
			},
			".TabLabel": {
				"paddingTop": "0.5rem",
			},
		},
	}

	/** @param {HTMLElement} element */
	constructor(element) {
		this.element = element;
		this.formElement = element.querySelector('form');

		/** @type {string} */
		this.stripePublishableKey = this.formElement.elements.stripePublishableKey.value;

		this.formElement.addEventListener('submit', async (event) => {
			event.preventDefault();

			const formData = new FormData(this.formElement);

			const response = await fetch(this.formElement.action, {
				method: 'post',
				body: formData,
				headers: {
					'Accept': 'application/json',
				},
			});
			const json = await response.json();

			try {
				if (json.status === 'redirect') {
					// @see https://docs.stripe.com/js/payment_intents/confirm_payment
					await this.completeStripePayment(json.redirectUrl, formData);
				} else if (json.key === 'error.merx.fieldsvalidation') {
					this.handleFieldErrors(json);
				} else {
					throw json.message ?? 'Fatal error';
				}
			} catch (errorMessage) {
				this.showError(errorMessage, event.submitter);
			}
		});

		this.initStripe().catch((error) => {
			this.showError(error.message, this.formElement.querySelector('[type="submit"]'));
		});
	}

	handleFieldErrors(json) {
		// Handle field errors
		const errorDetails = json.details;

		// eslint-disable-next-line
		for (const [key, error] of Object.entries(errorDetails)) {
			const labelElement = this.formElement.querySelector(`label:has([name="${key}"])`);

			if (labelElement) {
				// eslint-disable-next-line
				for (const errorMessage of Object.values(error.message)) {
					// Display error message(s) inside of label element
					labelElement.appendChild(this.createErrorElement(errorMessage));
				}
			} else {
				throw json.message ?? 'Fatal error';
			}
		}
	}

	// eslint-disable-next-line
	createErrorElement(errorMessage) {
		return Object.assign(document.createElement('p'), {
			classList: 'error',
			innerText: errorMessage,
		});
	}

	showError(/** @type {string} */ errorMessage, /** @type {HTMLElement} */ elementBefore) {
		// Display general error message before submit button
		elementBefore.parentElement.insertBefore(this.createErrorElement(errorMessage), elementBefore);
	}

	async completeStripePayment(redirectUrl, formData) {
		const { error } = await this.stripe.confirmPayment({
			// `Elements` instance that was used to create the Payment Element
			elements: this.stripeElements,
			confirmParams: {
				return_url: redirectUrl,
				payment_method_data: {
					billing_details: {
						name: formData.get('name'),
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

	async initStripe() {
		/** @type {Stripe} */
		this.stripe = window.Stripe(this.stripePublishableKey);

		const response = await fetch('/api/shop/stripe-client-secret')
			.catch((error) => this.showError(error.message, this.formElement.querySelector('[type="submit"]')));

		const { clientSecret } = await response.json();

		this.stripeElements = this.stripe.elements({
			locale: document.querySelector('html').lang,
			loader: 'never',
			clientSecret,
			fonts: [
				{
					cssSrc: 'https://merx.wagnerwagner.test/assets/css/fonts.css',
				},
			],
			appearance: this.stripeElementsAppearance,
		});

		// @see https://docs.stripe.com/js/element/payment_element
		const paymentElement = this.stripeElements.create('payment', {
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
		paymentElement.mount('.m-form-checkout__payment-fields');
	}
}

document.querySelectorAll('.o-checkout').forEach((_) => new OCheckout(_));
