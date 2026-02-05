/** @typedef {import('@stripe/stripe-js').Stripe} Stripe */

class OCheckout {
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

		this.initStripe();
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

	showError(errorMessage, elementBefore) {
		// Display general error message before submit button
		this.formElement.insertBefore(this.createErrorElement(errorMessage), elementBefore);
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
		const response = await fetch('/api/shop/stripe-client-secret')
			.catch((error) => this.showError(error.message, this.formElement.querySelector('[type="submit"]')));
		const { clientSecret } = await response.json();

		/** @type {Stripe} */
		this.stripe = window.Stripe(this.stripePublishableKey);

		this.stripeElements = this.stripe.elements({
			locale: document.querySelector('html').lang,
			loader: 'never',
			clientSecret,
			appearance: {
				theme: 'stripe',
				labels: 'above',
				disableAnimations: true,
				variables: {
					borderRadius: 0,
				},
			},
		});

		// @see https:docs.stripe.com/js/element/payment_element
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
		paymentElement.mount('.form-checkout__payment-fields');
	}
}

document.querySelectorAll('.o-checkout').forEach((_) => new OCheckout(_));
