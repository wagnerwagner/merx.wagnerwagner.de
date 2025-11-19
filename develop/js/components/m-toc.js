class MToc {
	/** @type {number} */
	offset = 100;

	/** @param {HTMLElement} element */
	constructor(element) {
		this.element = element;

		this.headingElements = document.querySelectorAll('.o-main__text h2');
		this.aElements = this.element.querySelectorAll('a');

		if (this.aElements.length > 0) {
			document.addEventListener('scroll', this.onScroll.bind(this), {
				passive: true,
			});
			window.addEventListener('resize', this.onScroll.bind(this), {
				passive: true,
			});

			// Initial scroll position
			this.onResize();
		}
	}

	onResize() {
		// Parse scroll-padding-block-start from :root element, if set, and update offset accordingly
		const computedStyle = getComputedStyle(document.documentElement);
		const scrollPaddingBlockStart = computedStyle.getPropertyValue('scroll-padding-block-start');
		if (scrollPaddingBlockStart) {
			this.offset = parseFloat(scrollPaddingBlockStart);
		}

		this.onScroll();
	}

	onScroll() {
		const { offset } = this;
		const { scrollTop } = document.scrollingElement;
		const { headingElements, aElements } = this;
		const currentHeading = [...headingElements]
			.reverse()
			.find((_) => scrollTop >= _.offsetTop - offset);

		[...aElements].forEach((_) => (_.setAttribute('aria-current', _.getAttribute('href') === `#${currentHeading?.id}`) ? 'true' : 'false'));
	}
}

document.querySelectorAll('.m-toc').forEach((_) => new MToc(_));
