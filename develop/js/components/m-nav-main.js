class MNavMain {
	/** @param {HTMLElement} element */
	constructor(element) {
		this.element = element;

		window.addEventListener('resize', () => this.onResize());

		this.onResize();
	}

	onResize() {
		this.element.toggleAttribute('popover', !window.matchMedia('(min-width: 64em').matches);
	}
}

document.querySelectorAll('.m-nav-main').forEach((_) => new MNavMain(_));
