class MSearch {
	selectedIndex = -1;

	/** @param {HTMLFormElement} element */
	constructor(element) {
		this.element = element;
		this.resultsElement = element.querySelector('.m-search__results');
		this.inputElement = element.querySelector('input[type="search"]');
		this.infoElement = element.querySelector('.m-search__info');

		this.inputElement.addEventListener('input', () => {
			this.infoElement.innerText = '';
			const query = this.inputElement.value;
			if (query.length < 3) {
				this.renderResults([]);
				return;
			}
			this.search(query);
		});

		this.element.addEventListener('submit', (event) => {
			if (this.selectedIndex !== -1) {
				event.preventDefault();
				this.openCurrentIndex();
			}
		});

		this.element.addEventListener('mousemove', (event) => {
			const index = parseInt(event.target.closest('[role="option"]')?.dataset.index ?? -1, 10);
			this.selectResult(index);
		});

		this.inputElement.addEventListener('keydown', (event) => {
			if (event.key === 'ArrowUp' || event.key === 'ArrowDown') {
				event.preventDefault();
				this.selectResult(event.key === 'ArrowUp' ? this.selectedIndex - 1 : this.selectedIndex + 1);
			}
		});
	}

	openCurrentIndex() {
		this.resultsElement.querySelector('li[role="option"][aria-selected="true"] a')?.click();
	}

	selectResult(index) {
		const results = this.resultsElement.querySelectorAll('li[role="option"]');
		this.selectedIndex = -1;
		results.forEach((result, _index) => {
			if (_index === index) {
				result.setAttribute('aria-selected', 'true');
				this.selectedIndex = index;
			} else {
				result.removeAttribute('aria-selected');
			}
		});
	}

	renderResults(results) {
		this.element.querySelector('.m-search__results').innerHTML = results.map((result, index) => `
			<li role="option" data-index="${index}">
				<a href="${result.url}">
					<strong>${result.title}</strong>
					<small>${result.breadcrumbs.join(' / ')}</small>
				</a>
			</li>
		`).join('');
	}

	async search(query) {
		const response = await fetch(`/api/search?q=${query}`)
			.catch((error) => {
				this.infoElement.innerText = error.message;
			});

		const { data = [], message } = await response.json();

		if (data.length === 0) {
			this.infoElement.innerText = message;
		}

		this.renderResults(data);
	}
}

document.querySelectorAll('.m-search').forEach((_) => new MSearch(_));
