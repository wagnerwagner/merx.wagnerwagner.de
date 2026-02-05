class MNAvSidebar {
	scrollTop;

	/**
	 * @param {HTMLElement} element
	 * @param {number} index
	 */
	constructor(element, index) {
		this.element = element;
		this.index = index;

		const linkElements = element.querySelectorAll('a');
		linkElements.forEach((_) => _.addEventListener('click', this.handleLinkClick.bind(this)));

		element.scrollTop = this.readStorage() ?? this.getScrollTopOfOpenLink();
	}

	getScrollTopOfOpenLink() {
		const openElement = this.element.querySelector('[data-state="open"]');
		if (openElement === null) {
			return null;
		}
		const scrollTop = openElement.offsetTop - 50;
		if (scrollTop < this.element.offsetHeight - 50 - 32) {
			return null;
		}
		return scrollTop;
	}

	readStorage() {
		const { index } = this;
		const scrollTop = window.localStorage.getItem(`m-nav-sidebar.scrollTop.${index}`);
		window.localStorage.removeItem(`m-nav-sidebar.scrollTop.${index}`);
		return scrollTop;
	}

	store() {
		const { index, element } = this;
		window.localStorage.setItem(`m-nav-sidebar.scrollTop.${index}`, element.scrollTop);
	}

	/** @param {MouseEvent} event */
	handleLinkClick() {
		window.mNavSidebarElements.forEach((mNavSidebarElement) => {
			mNavSidebarElement.store();
		});
	}
}

window.mNavSidebarElements = [...document.querySelectorAll('.m-nav-sidebar')].map((_, index) => new MNAvSidebar(_, index));
