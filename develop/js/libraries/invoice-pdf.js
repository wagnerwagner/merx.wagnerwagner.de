import PDFDocument from 'pdfkit';
import { toBlob } from 'pdfkit/output';
import logo from '../../../public/assets/images/wagnerwagner.svg';

/**
 * The invoice of the order page as PDF/A-3b. For a customer in Germany, it
 * embeds the e-invoice XML of the server (Factur-X/ZUGFeRD, profile EN 16931).
 *
 * The layout follows the invoice of the order page (o-order.css).
 */

const MARGIN = 56;
const COLORS = {
	text: '#212121', // --color-content-primary
	label: '#666666', // --color-content-secondary
	borderPrimary: '#545454', // --color-border-primary
	borderSecondary: '#d4d4d4', // --color-border-secondary
};
// The stylistic sets of the page (typography.css): 19 for the M everywhere,
// 18 for the display headline
const STYLES = {
	display: { font: 'bold', size: 26, lineHeight: 32, features: ['ss18', 'ss19'] },
	label: { font: 'regular', size: 8, lineHeight: 12, color: COLORS.label },
	text: { font: 'regular', size: 10, lineHeight: 15 },
	notice: { font: 'regular', size: 9, lineHeight: 13 },
	total: { font: 'bold', size: 13, lineHeight: 18 },
};

const XML_FILENAME = 'factur-x.xml';
const FACTURX_NAMESPACE = 'urn:factur-x:pdfa:CrossIndustryDocument:invoice:1p0#';

/**
 * XMP metadata which marks the PDF as Factur-X invoice, with the PDF/A
 * extension schema of its namespace
 */
function facturXMetadata() {
	const properties = [
		['DocumentFileName', 'The name of the embedded XML document'],
		['DocumentType', 'The type of the hybrid document in capital letters, e.g. INVOICE or ORDER'],
		['Version', 'The actual version of the standard applying to the embedded XML document'],
		['ConformanceLevel', 'The conformance level of the embedded XML document'],
	];

	return `
		<rdf:Description xmlns:fx="${FACTURX_NAMESPACE}" rdf:about="">
			<fx:DocumentType>INVOICE</fx:DocumentType>
			<fx:DocumentFileName>${XML_FILENAME}</fx:DocumentFileName>
			<fx:Version>1.0</fx:Version>
			<fx:ConformanceLevel>EN 16931</fx:ConformanceLevel>
		</rdf:Description>
		<rdf:Description xmlns:pdfaExtension="http://www.aiim.org/pdfa/ns/extension/" xmlns:pdfaSchema="http://www.aiim.org/pdfa/ns/schema#" xmlns:pdfaProperty="http://www.aiim.org/pdfa/ns/property#" rdf:about="">
			<pdfaExtension:schemas>
				<rdf:Bag>
					<rdf:li rdf:parseType="Resource">
						<pdfaSchema:schema>Factur-X PDFA Extension Schema</pdfaSchema:schema>
						<pdfaSchema:namespaceURI>${FACTURX_NAMESPACE}</pdfaSchema:namespaceURI>
						<pdfaSchema:prefix>fx</pdfaSchema:prefix>
						<pdfaSchema:property>
							<rdf:Seq>${properties.map(([name, description]) => `
								<rdf:li rdf:parseType="Resource">
									<pdfaProperty:name>${name}</pdfaProperty:name>
									<pdfaProperty:valueType>Text</pdfaProperty:valueType>
									<pdfaProperty:category>external</pdfaProperty:category>
									<pdfaProperty:description>${description}</pdfaProperty:description>
								</rdf:li>`).join('')}
							</rdf:Seq>
						</pdfaSchema:property>
					</rdf:li>
				</rdf:Bag>
			</pdfaExtension:schemas>
		</rdf:Description>
	`;
}

/**
 * @param {object} invoice What the order controller hands to the page as `invoice`
 * @param {{regular: Uint8Array, bold: Uint8Array}} fonts Fonts, which PDF/A embeds
 * @returns {Promise<Blob>}
 */
export default async function createInvoicePdf(invoice, fonts) {
	const now = new Date();
	const title = `Invoice ${invoice.number}`;

	const doc = new PDFDocument({
		size: 'A4',
		margin: MARGIN,
		// PDF/A does not allow Helvetica, which is not embedded
		font: null,
		pdfVersion: '1.7',
		subset: 'PDF/A-3b',
		lang: 'en',
		displayTitle: true,
		info: {
			Title: title,
			Author: invoice.seller.address[0],
			Subject: title,
			CreationDate: now,
			ModDate: now,
		},
	});
	const blob = toBlob(doc);

	doc.registerFont('regular', fonts.regular);
	doc.registerFont('bold', fonts.bold);

	const left = MARGIN;
	const right = doc.page.width - MARGIN;
	const width = right - left;
	let y = 0;

	/**
	 * Writes text with the line height of CSS: centered within each line
	 *
	 * @returns {number} Height of the text
	 */
	function text(string, x, top, style, options = {}) {
		const { font, size, lineHeight, color = COLORS.text, features = ['ss19'] } = STYLES[style];
		doc.font(font).fontSize(size).fillColor(color);
		const lineGap = lineHeight - doc.currentLineHeight(true);
		doc.text(string, x, top + lineGap / 2, { lineGap, features, ...options });
		return doc.heightOfString(string, { lineGap, features, ...options });
	}

	function line(top, color, lineWidth = 0.5) {
		doc.moveTo(left, top).lineTo(right, top).lineWidth(lineWidth).strokeColor(color).stroke();
	}

	// Logo, centered: the paths of the SVG, scaled from its height of 20
	const logoScale = 0.6;
	doc.save();
	doc.translate((doc.page.width - 169 * logoScale) / 2, 36).scale(logoScale);
	for (const [, path, color] of logo.matchAll(/<path d="([^"]+)" fill="([^"]+)"/g)) {
		// After an arc, PDFKit 0.20 mirrors an old control point for `s`. Its first
		// control point is the current one there, which `c0 0` says the same.
		doc.path(path.replace(/([aA][^a-zA-Z]*)s/g, '$1c0 0 ')).fill(color, 'even-odd');
	}
	doc.restore();

	y = 96;
	y += text('Invoice', left, y, 'display') + 24;

	// Billed to (two thirds) and the details of the invoice (one third)
	const detailsX = left + width * (2 / 3);
	let leftY = y + text('Billed to', left, y, 'label') + 2;
	leftY += text(invoice.buyer.join('\n'), left, leftY, 'text', { width: detailsX - left - 24 }) + 6;
	leftY += text(invoice.email, left, leftY, 'text', { width: detailsX - left - 24 });

	let rightY = y;
	invoice.details.forEach(([label, value]) => {
		rightY += text(label, detailsX, rightY, 'label') + 2;
		rightY += text(value, detailsX, rightY, 'text', { width: right - detailsX }) + 8;
	});
	y = Math.max(leftY, rightY) + 32;

	// Items: product, quantity, unit price and amount
	const gap = 16;
	const columns = [];
	columns[3] = { x: right - 80, width: 80 };
	columns[2] = { x: columns[3].x - gap - 80, width: 80 };
	columns[1] = { x: columns[2].x - gap - 56, width: 56 };
	columns[0] = { x: left, width: columns[1].x - gap - left };
	const row = (cells, top, style) => Math.max(...cells.map((cell, index) => text(cell, columns[index].x, top, style, {
		width: columns[index].width,
		align: index === 0 ? 'left' : 'right',
	})));

	y += row(['Product', 'Quantity', 'Unit price', 'Amount'], y, 'label') + 6;
	line(y, COLORS.borderPrimary, 0.75);
	invoice.items.forEach((cells) => {
		y += 6;
		y += row(cells, y, 'text') + 6;
		line(y, COLORS.borderSecondary);
	});

	// Subtotal, VAT and total, labelled across the first three columns
	y += 10;
	const totalRow = (label, value, style) => {
		const height = text(label, left, y, style, { width: columns[2].x + columns[2].width - left });
		text(value, columns[3].x, y, style, { width: columns[3].width, align: 'right' });
		y += height + 4;
	};
	invoice.totals.forEach(([label, value]) => totalRow(label, value, 'text'));
	y += 2;
	totalRow('Total', invoice.total, 'total');

	if (invoice.notice) {
		y += 20;
		y += text(invoice.notice, left, y, 'notice', { width });
	}

	// Seller
	y += 28;
	line(y, COLORS.borderSecondary);
	y += 12;
	y += text('From', left, y, 'label') + 2;
	y += text(invoice.seller.address.join('\n'), left, y, 'text', { width }) + 6;
	y += text(invoice.seller.email, left, y, 'text', { width }) + 6;
	text(invoice.seller.tax.join('\n'), left, y, 'text', { width });

	// Factur-X: the XML as attachment and the metadata which points to it
	if (invoice.xml) {
		doc.file(new TextEncoder().encode(invoice.xml), {
			name: XML_FILENAME,
			type: 'text/xml',
			description: 'Factur-X invoice, profile EN 16931',
			relationship: 'Alternative',
			creationDate: now,
			modifiedDate: now,
		});
		doc.appendXML(facturXMetadata());
	}

	doc.end();
	return blob;
}
