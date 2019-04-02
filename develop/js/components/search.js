const element = document.querySelector('.header-search');
const resultListElement = document.createElement('ul');
const ariaLiveElement = document.createElement('span');
let focusElement = null;

function buildList(data) {
  resultListElement.innerHTML = '';
  data.forEach((item) => {
    const liElement = document.createElement('li');
    liElement.innerHTML = `
    <a href="/${item.id}">
      <strong>${item.title}</strong>
      <small>${item.id}</small>
    </a>`;
    resultListElement.appendChild(liElement);
  });
  element.appendChild(resultListElement);
}

function focusFocusElement() {
  if (focusElement) {
    focusElement.classList.add('is-focus');
    ariaLiveElement.textContent = focusElement.textContent;
  }
}

function focusNext() {
  if (focusElement) {
    focusElement.classList.remove('is-focus');
  }
  if (focusElement && focusElement.parentElement.nextElementSibling) {
    focusElement = focusElement.parentElement.nextElementSibling.querySelector('a');
  } else if (resultListElement.children.length > 0) {
    focusElement = resultListElement.children[0].querySelector('a');
  }
  focusFocusElement();
}

function focusPrev() {
  if (focusElement) {
    focusElement.classList.remove('is-focus');
  }
  if (focusElement && focusElement.parentElement.previousElementSibling) {
    focusElement = focusElement.parentElement.previousElementSibling.querySelector('a');
  } else if (resultListElement.children.length > 0) {
    focusElement = resultListElement.children[resultListElement.childElementCount - 1].querySelector('a');
  }
  focusFocusElement();
}

if (element) {
  const submitElement = element.querySelector('button[type="submit"]');
  const inputElement = element.querySelector('input');

  ariaLiveElement.classList.add('visually-hidden');
  ariaLiveElement.setAttribute('role', 'status');
  ariaLiveElement.setAttribute('aria-live', 'assertive');
  ariaLiveElement.setAttribute('aria-relevant', 'additions');
  element.appendChild(ariaLiveElement);

  submitElement.addEventListener('click', (event) => {
    if (!element.classList.contains('is-active')) {
      event.preventDefault();
      inputElement.focus();
    }
  });

  inputElement.addEventListener('focus', () => {
    element.classList.add('is-active');
  });

  inputElement.addEventListener('blur', () => {
    // wait to register clicks
    setTimeout(() => {
      if (document.activeElement !== inputElement) {
        resultListElement.innerHTML = '';
        element.classList.remove('is-active');
      }
    }, 100);
  });

  element.addEventListener('submit', (event) => {
    if (focusElement) {
      event.preventDefault();
      document.location = focusElement.href;
    }
  });

  document.addEventListener('keydown', (event) => {
    if (event.key === 'ArrowDown') {
      focusNext();
    } else if (event.key === 'ArrowUp') {
      focusPrev();
    }
  });

  inputElement.addEventListener('input', () => {
    if (inputElement.validity.valid) {
      fetch(`/search-api?q=${inputElement.value}`).then(response => response.json()).then((data) => {
        buildList(data);
      });
    } else {
      resultListElement.innerHTML = '';
    }
  });
}
