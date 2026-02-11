class MSearchDialog {
	/** @param {HTMLDivElement} element */
	constructor(element) {
		this.element = element;
		const inputElement = this.element.querySelector('input[type="search"]');

		document.addEventListener('keydown', (event) => {
			const isMac = /Mac|iPhone|iPad|iPod/i.test(navigator.userAgent || '');
			if (
				((isMac && event.metaKey) || (!isMac && event.ctrlKey))
				&& event.key.toLowerCase() === 'k'
			) {
				event.preventDefault();
				this.element.showPopover();
			}
		});

		inputElement.addEventListener('keydown', (event) => {
			if (event.key === 'Escape') {
				this.element.hidePopover();
			}
		});

		element.addEventListener('toggle', (event) => {
			if (event.newState === 'open') {
				inputElement?.focus();
			}
		});
		element.addEventListener('click', (event) => {
			if (event.target === element) {
				this.element.hidePopover();
			}
		});
	}
}

document.querySelectorAll('.m-search-dialog').forEach((_) => new MSearchDialog(_));
