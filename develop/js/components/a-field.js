import AbstractElement from '../configs/abstract-element';

class AField extends AbstractElement {
	constructor(element) {
		// super
		super(element);

		// variables
		const { theme } = element.dataset;

		// class properties
		this.element = element;
		this.inputElement = element.querySelector('input');

		// functions
		function lorem() {
			// …
		}

		// event functions
		function onChange() {
			// …
		}

		// event listeners
		if (theme === 'positive') {
			this.inputElement.addEventListener('change', onChange);
		}

		// init
		this.init();
	}

	get value() {
		return this.inputElement.value;
	}

	init() {
		// …
	}
}

export default AField;
