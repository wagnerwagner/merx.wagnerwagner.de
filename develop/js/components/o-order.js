import createInvoicePdf from '../libraries/invoice-pdf.js';

/**
 * Fonts of the PDF, the weights of the page (450 and 700). PDF/A embeds them.
 * PDFKit cannot embed WOFF2, so these are the OpenType files.
 */
const fontUrls = {
	regular: '/assets/fonts/tt-norms/TT_Norms_Pro_Normal.otf',
	bold: '/assets/fonts/tt-norms/TT_Norms_Pro_Bold.otf',
};

class OOrder {
	/** @param {HTMLElement} element */
	constructor(element) {
		this.buttonElement = element.querySelector('[data-action="download-invoice"]');
		this.invoice = JSON.parse(element.querySelector('#invoice-data').textContent);

		this.buttonElement.addEventListener('click', () => this.download());
	}

	async download() {
		this.buttonElement.disabled = true;

		try {
			const fonts = Object.fromEntries(await Promise.all(Object.entries(fontUrls).map(async ([name, url]) => {
				const response = await fetch(url);
				if (!response.ok) {
					throw new Error(`${url} could not be loaded.`);
				}
				return [name, new Uint8Array(await response.arrayBuffer())];
			})));

			const blob = await createInvoicePdf(this.invoice, fonts);
			const linkElement = document.createElement('a');
			linkElement.href = URL.createObjectURL(blob);
			linkElement.download = this.invoice.filename;
			linkElement.click();
			// Safari starts the download after the click has returned
			setTimeout(() => URL.revokeObjectURL(linkElement.href), 1000);
		} catch (error) {
			console.error(error);
		} finally {
			this.buttonElement.disabled = false;
		}
	}
}

document.querySelectorAll('.o-order').forEach((_) => new OOrder(_));
