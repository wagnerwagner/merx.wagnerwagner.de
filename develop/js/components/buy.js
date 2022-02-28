/* eslint-disable no-param-reassign */
/* global Stripe */
const element = document.querySelector('.buy');
let paymentMethod = null;
const stripeStyle = {
  base: {
    fontFamily: '-apple-system, BlinkMacSystemFont, Segoe UI, Helvetica, Arial, sans-serif, Apple Color Emoji, Segoe UI Emoji, Segoe UI Symbol',
    fontSize: '17px',
    color: 'black',
  },
};


function setLoading() {
  const formCheckoutElement = element.querySelector('.form-checkout');
  formCheckoutElement.classList.add('is-loading');
}

function unsetLoading() {
  const formCheckoutElement = element.querySelector('.form-checkout');
  formCheckoutElement.classList.remove('is-loading');
}

function getErrorElement(message) {
  const errorElement = document.createElement('div');
  errorElement.classList.add('error');
  errorElement.textContent = message;
  return errorElement;
}


function showGlobalError(message) {
  const formCheckoutSubmit = element.querySelector('.form-checkout__submit');
  formCheckoutSubmit.appendChild(getErrorElement(message));
}

function showFieldErrors(details) {
  let isFocused = false;
  Object.keys(details).forEach((key) => {
    const labelElement = element.querySelector(`label[data-name="${key}"]`);
    const message = Object.values(details[key].message)[0];
    if (labelElement) {
      labelElement.appendChild(getErrorElement(message));
      const inputElement = labelElement.querySelector('input');
      if (!isFocused && inputElement) {
        inputElement.focus();
        isFocused = true;
      }
    }
  });
}

function removeErrors() {
  [...element.querySelectorAll('.error')].forEach((errorElement) => {
    errorElement.remove();
  });
}

function updateCart(quantity = 1, cartQuantityNumberElement, countrySelectElemet) {
  const cartElement = element.querySelector('.cart');
  const postData = new FormData();
  removeErrors();
  cartElement.classList.add('is-loading');
  postData.append('quantity', quantity);
  postData.append('country', countrySelectElemet.value);
  fetch('merx-api/update-cart', {
    method: 'POST',
    credentials: 'same-origin',
    body: postData,
  }).then((response) => {
    if (response.status !== 500) {
      return response.json();
    }
  }).then((data) => {
    // eslint-disable-next-line prefer-destructuring
    cartQuantityNumberElement.textContent = data.quantity;
    element.querySelector('.cart__sum').textContent = data.sumNet;
    element.querySelector('.cart__tax').textContent = data.sumTax;
    element.querySelector('.cart__total').textContent = data.sum;
    element.querySelector('.cart__tax').hidden = (parseFloat(data.sumTaxRaw) === 0);
    cartElement.classList.remove('is-loading');
  }).catch((reason) => {
    cartElement.classList.remove('is-loading');
    console.error(reason);
    const errorElement = getErrorElement(reason.message);
    cartElement.appendChild(errorElement);
  });
}

function getClientSecret() {
  return fetch('merx-api/get-client-secret').then(response => response.json()).then(data => data.clientSecret).catch((exception) => {
    console.error(exception);
    showGlobalError('Fatal Error: Could not get client secret.');
  });
}

function changeSubmitText(submitElement, text, buttonTextDefault) {
  if (text && text.length > 0) {
    submitElement.textContent = text;
  } else {
    submitElement.textContent = buttonTextDefault;
  }
}

function changePaymentMethod(paymentMethodElements) {
  [...paymentMethodElements].forEach((paymentMethodElement) => {
    if (paymentMethodElement.dataset.paymentMethod === paymentMethod) {
      paymentMethodElement.hidden = false;
    } else {
      paymentMethodElement.hidden = true;
    }
  });
}

function setStripeToken(token, formElement) {
  const hiddenInput = document.createElement('input');
  hiddenInput.setAttribute('type', 'hidden');
  hiddenInput.setAttribute('name', 'stripeToken');
  hiddenInput.setAttribute('value', token.id);
  formElement.appendChild(hiddenInput);
}

function initStripe() {
  const stripePublishableKey = element.querySelector('input[name="stripePublishableKey"]').value;
  const stripe = Stripe(stripePublishableKey);
  const stripeElements = stripe.elements();

  const stripeCardElement = document.querySelector('#stripe-card');
  const stripeSepaDebitElement = document.querySelector('#stripe-sepa-debit');

  const stripeCard = stripeElements.create('card', {
    style: stripeStyle,
    hidePostalCode: true,
  });

  const stripeSepaDebit = stripeElements.create('iban', {
    style: stripeStyle,
    supportedCountries: ['SEPA'],
    placeholderCountry: 'DE',
  });

  stripeCard.mount(stripeCardElement);
  stripeSepaDebit.mount(stripeSepaDebitElement);

  stripe.card = stripeCard;
  stripe.cardElement = stripeCardElement;

  stripe.sepaDebit = stripeSepaDebit;
  stripe.sepaDebitElement = stripeSepaDebitElement;

  return stripe;
}

function submit(formElement) {
  setLoading();
  fetch(formElement.action, {
    method: 'POST',
    credentials: 'same-origin',
    body: new FormData(formElement),
  }).then((response) => {
    if (response.status !== 500) {
      return response.json();
    }
    return {
      message: 'Unknow Server Error.',
    };
  }).then((data) => {
    unsetLoading();
    if (data.status === 201) {
      window.location = data.redirect;
    } else if (data.code === 'error.merx.fieldsvalidation') {
      showFieldErrors(data.details);
    } else {
      showGlobalError(data.message);
    }
  }).catch((reason) => {
    unsetLoading();
    console.error(reason);
    const errorElement = getErrorElement(reason.message);
    formElement.querySelector('.form-checkout').appendChild(errorElement);
  });
}


if (element) {
  const stripe = initStripe();
  const formElement = element.querySelector('.buy__form');
  const cartQuantityElement = formElement.querySelector('.cart__quantity');
  const cartQuantityNumberElement = cartQuantityElement.querySelector('span');
  const cartQuantityDecreaseElement = cartQuantityElement.querySelector('button[data-action="decrease"]');
  const cartQuantityIncreaseElement = cartQuantityElement.querySelector('button[data-action="increase"]');
  const submitElement = formElement.querySelector('button[type="submit"]');
  const paymentMethodElements = element.querySelectorAll('label[data-payment-method]');
  const radioPaymentMethodElements = element.querySelectorAll('input[name="paymentMethod"]');
  const buttonTextDefault = submitElement.textContent;
  const countrySelectElemet = formElement.country;

  [...radioPaymentMethodElements].forEach((radioPaymentMethodElement) => {
    radioPaymentMethodElement.addEventListener('change', () => {
      paymentMethod = radioPaymentMethodElement.value;
      const { submitText } = radioPaymentMethodElement.dataset;
      changeSubmitText(submitElement, submitText, buttonTextDefault);
      changePaymentMethod(paymentMethodElements);
    });
  });


  formElement.addEventListener('submit', async (event) => {
    event.preventDefault();
    removeErrors();
    if (formElement.checkValidity()) {
      if (paymentMethod === 'credit-card-sca') {
        const clientSecret = await getClientSecret();
        const { error } = await stripe.handleCardPayment(
          clientSecret, stripe.card, {
            payment_method_data: {
              billing_details: {
                email: formElement.email.value,
                name: formElement.name.value,
                address: {
                  line1: formElement.street.value,
                  city: formElement.city.value,
                  country: formElement.country.value,
                },
              },
            },
          },
        );
        if (error) {
          const errorElement = getErrorElement(error.message);
          stripe.cardElement.parentElement.appendChild(errorElement);
        } else {
          submit(formElement);
        }
      } else if (paymentMethod === 'sepa-debit') {
        const sourceData = {
          type: 'sepa_debit',
          currency: 'eur',
          owner: {
            name: formElement.name.value,
            email: formElement.email.value,
          },
        };

        stripe.createSource(stripe.sepaDebit, sourceData).then((result) => {
          if (result.error) {
            const errorElement = getErrorElement(result.error.message);
            stripe.sepaDebitElement.parentElement.appendChild(errorElement);
          } else {
            setStripeToken(result.source, formElement);
            submit(formElement);
          }
        });
      } else {
        submit(formElement);
      }
    } else {
      showGlobalError('Checkout form is not valid.');
    }
  });

  cartQuantityDecreaseElement.addEventListener('click', () => {
    const newQuantity = parseInt(cartQuantityNumberElement.textContent, 10) - 1;
    if (newQuantity >= 1) {
      updateCart(newQuantity, cartQuantityNumberElement, countrySelectElemet);
    }
  });
  cartQuantityIncreaseElement.addEventListener('click', () => {
    const newQuantity = parseInt(cartQuantityNumberElement.textContent, 10) + 1;
    if (newQuantity <= 10) {
      updateCart(newQuantity, cartQuantityNumberElement, countrySelectElemet);
    }
  });
  countrySelectElemet.addEventListener('change', () => {
    const newQuantity = parseInt(cartQuantityNumberElement.textContent, 10);
    updateCart(newQuantity, cartQuantityNumberElement, countrySelectElemet);
  });

  updateCart(1, cartQuantityNumberElement, countrySelectElemet);
}
