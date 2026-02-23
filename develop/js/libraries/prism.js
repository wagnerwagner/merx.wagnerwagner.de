/* PrismJS 1.30.0
https://prismjs.com/download#themes=prism-dark&languages=markup+css+clike+javascript+bash+javadoclike+jsdoc+json+markdown+markup-templating+php+phpdoc+php-extras+typescript+yaml&plugins=custom-class+toolbar */
const _self = typeof window !== 'undefined' ? window : typeof WorkerGlobalScope !== 'undefined' && self instanceof WorkerGlobalScope ? self : {}; const Prism = (function (e) {
	const n = /(?:^|\s)lang(?:uage)?-([\w-]+)(?=\s|$)/i; let t = 0; const r = {}; var a = {
		manual: e.Prism && e.Prism.manual,
		disableWorkerMessageHandler: e.Prism && e.Prism.disableWorkerMessageHandler,
		util: {
			encode: function e(n) { return n instanceof i ? new i(n.type, e(n.content), n.alias) : Array.isArray(n) ? n.map(e) : n.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/\u00a0/g, ' '); }, type(e) { return Object.prototype.toString.call(e).slice(8, -1); }, objId(e) { return e.__id || Object.defineProperty(e, '__id', { value: ++t }), e.__id; }, clone: function e(n, t) { let r; let i; switch (t = t || {}, a.util.type(n)) { case 'Object': if (i = a.util.objId(n), t[i]) return t[i]; for (const l in r = {}, t[i] = r, n)n.hasOwnProperty(l) && (r[l] = e(n[l], t)); return r; case 'Array': return i = a.util.objId(n), t[i] ? t[i] : (r = [], t[i] = r, n.forEach(((n, a) => { r[a] = e(n, t); })), r); default: return n; } }, getLanguage(e) { for (;e;) { const t = n.exec(e.className); if (t) return t[1].toLowerCase(); e = e.parentElement; } return 'none'; }, setLanguage(e, t) { e.className = e.className.replace(RegExp(n, 'gi'), ''), e.classList.add(`language-${t}`); }, currentScript() { if (typeof document === 'undefined') return null; if (document.currentScript && document.currentScript.tagName === 'SCRIPT') return document.currentScript; try { throw new Error(); } catch (r) { const e = (/at [^(\r\n]*\((.*):[^:]+:[^:]+\)$/i.exec(r.stack) || [])[1]; if (e) { const n = document.getElementsByTagName('script'); for (const t in n) if (n[t].src == e) return n[t]; } return null; } }, isActive(e, n, t) { for (let r = `no-${n}`; e;) { const a = e.classList; if (a.contains(n)) return !0; if (a.contains(r)) return !1; e = e.parentElement; } return !!t; },
		},
		languages: {
			plain: r, plaintext: r, text: r, txt: r, extend(e, n) { const t = a.util.clone(a.languages[e]); for (const r in n)t[r] = n[r]; return t; }, insertBefore(e, n, t, r) { const i = (r = r || a.languages)[e]; const l = {}; for (const o in i) if (i.hasOwnProperty(o)) { if (o == n) for (const s in t)t.hasOwnProperty(s) && (l[s] = t[s]); t.hasOwnProperty(o) || (l[o] = i[o]); } const u = r[e]; return r[e] = l, a.languages.DFS(a.languages, (function (n, t) { t === u && n != e && (this[n] = l); })), l; }, DFS: function e(n, t, r, i) { i = i || {}; const l = a.util.objId; for (const o in n) if (n.hasOwnProperty(o)) { t.call(n, o, n[o], r || o); const s = n[o]; const u = a.util.type(s); u !== 'Object' || i[l(s)] ? u !== 'Array' || i[l(s)] || (i[l(s)] = !0, e(s, t, o, i)) : (i[l(s)] = !0, e(s, t, null, i)); } },
		},
		plugins: {},
		highlightAll(e, n) { a.highlightAllUnder(document, e, n); },
		highlightAllUnder(e, n, t) { const r = { callback: t, container: e, selector: 'code[class*="language-"], [class*="language-"] code, code[class*="lang-"], [class*="lang-"] code' }; a.hooks.run('before-highlightall', r), r.elements = Array.prototype.slice.apply(r.container.querySelectorAll(r.selector)), a.hooks.run('before-all-elements-highlight', r); for (var i, l = 0; i = r.elements[l++];)a.highlightElement(i, !0 === n, r.callback); },
		highlightElement(n, t, r) {
			const i = a.util.getLanguage(n); const l = a.languages[i]; a.util.setLanguage(n, i); let o = n.parentElement; o && o.nodeName.toLowerCase() === 'pre' && a.util.setLanguage(o, i); const s = {
				element: n, language: i, grammar: l, code: n.textContent,
			}; function u(e) { s.highlightedCode = e, a.hooks.run('before-insert', s), s.element.innerHTML = s.highlightedCode, a.hooks.run('after-highlight', s), a.hooks.run('complete', s), r && r.call(s.element); } if (a.hooks.run('before-sanity-check', s), (o = s.element.parentElement) && o.nodeName.toLowerCase() === 'pre' && !o.hasAttribute('tabindex') && o.setAttribute('tabindex', '0'), !s.code) return a.hooks.run('complete', s), void (r && r.call(s.element)); if (a.hooks.run('before-highlight', s), s.grammar) if (t && e.Worker) { const c = new Worker(a.filename); c.onmessage = function (e) { u(e.data); }, c.postMessage(JSON.stringify({ language: s.language, code: s.code, immediateClose: !0 })); } else u(a.highlight(s.code, s.grammar, s.language)); else u(a.util.encode(s.code));
		},
		highlight(e, n, t) { const r = { code: e, grammar: n, language: t }; if (a.hooks.run('before-tokenize', r), !r.grammar) throw new Error(`The language "${r.language}" has no grammar.`); return r.tokens = a.tokenize(r.code, r.grammar), a.hooks.run('after-tokenize', r), i.stringify(a.util.encode(r.tokens), r.language); },
		tokenize(e, n) { const t = n.rest; if (t) { for (const r in t)n[r] = t[r]; delete n.rest; } const a = new s(); return u(a, a.head, e), o(e, a, n, a.head, 0), (function (e) { for (var n = [], t = e.head.next; t !== e.tail;)n.push(t.value), t = t.next; return n; }(a)); },
		hooks: { all: {}, add(e, n) { const t = a.hooks.all; t[e] = t[e] || [], t[e].push(n); }, run(e, n) { const t = a.hooks.all[e]; if (t && t.length) for (var r, i = 0; r = t[i++];)r(n); } },
		Token: i,
	}; function i(e, n, t, r) { this.type = e, this.content = n, this.alias = t, this.length = 0 | (r || '').length; } function l(e, n, t, r) { e.lastIndex = n; const a = e.exec(t); if (a && r && a[1]) { const i = a[1].length; a.index += i, a[0] = a[0].slice(i); } return a; } function o(e, n, t, r, s, g) { for (const f in t) if (t.hasOwnProperty(f) && t[f]) { let h = t[f]; h = Array.isArray(h) ? h : [h]; for (let d = 0; d < h.length; ++d) { if (g && g.cause == `${f},${d}`) return; const v = h[d]; const p = v.inside; const m = !!v.lookbehind; const y = !!v.greedy; const k = v.alias; if (y && !v.pattern.global) { const x = v.pattern.toString().match(/[imsuy]*$/)[0]; v.pattern = RegExp(v.pattern.source, `${x}g`); } for (let b = v.pattern || v, w = r.next, A = s; w !== n.tail && !(g && A >= g.reach); A += w.value.length, w = w.next) { let P = w.value; if (n.length > e.length) return; if (!(P instanceof i)) { var E; let S = 1; if (y) { if (!(E = l(b, A, e, m)) || E.index >= e.length) break; var L = E.index; const O = E.index + E[0].length; let C = A; for (C += w.value.length; L >= C;)C += (w = w.next).value.length; if (A = C -= w.value.length, w.value instanceof i) continue; for (let j = w; j !== n.tail && (C < O || typeof j.value === 'string'); j = j.next)S++, C += j.value.length; S--, P = e.slice(A, C), E.index -= A; } else if (!(E = l(b, 0, P, m))) continue; L = E.index; const N = E[0]; const _ = P.slice(0, L); const M = P.slice(L + N.length); const W = A + P.length; g && W > g.reach && (g.reach = W); let I = w.prev; if (_ && (I = u(n, I, _), A += _.length), c(n, I, S), w = u(n, I, new i(f, p ? a.tokenize(N, p) : N, k, N)), M && u(n, w, M), S > 1) { const T = { cause: `${f},${d}`, reach: W }; o(e, n, t, w.prev, A, T), g && T.reach > g.reach && (g.reach = T.reach); } } } } } } function s() { const e = { value: null, prev: null, next: null }; const n = { value: null, prev: e, next: null }; e.next = n, this.head = e, this.tail = n, this.length = 0; } function u(e, n, t) { const r = n.next; const a = { value: t, prev: n, next: r }; return n.next = a, r.prev = a, e.length++, a; } function c(e, n, t) { for (var r = n.next, a = 0; a < t && r !== e.tail; a++)r = r.next; n.next = r, r.prev = n, e.length -= a; } if (e.Prism = a, i.stringify = function e(n, t) {
		if (typeof n === 'string') return n; if (Array.isArray(n)) { let r = ''; return n.forEach(((n) => { r += e(n, t); })), r; } const i = {
			type: n.type, content: e(n.content, t), tag: 'span', classes: ['token', n.type], attributes: {}, language: t,
		}; const l = n.alias; l && (Array.isArray(l) ? Array.prototype.push.apply(i.classes, l) : i.classes.push(l)), a.hooks.run('wrap', i); let o = ''; for (const s in i.attributes)o += ` ${s}="${(i.attributes[s] || '').replace(/"/g, '&quot;')}"`; return `<${i.tag} class="${i.classes.join(' ')}"${o}>${i.content}</${i.tag}>`;
	}, !e.document) return e.addEventListener ? (a.disableWorkerMessageHandler || e.addEventListener('message', ((n) => { const t = JSON.parse(n.data); const r = t.language; const i = t.code; const l = t.immediateClose; e.postMessage(a.highlight(i, a.languages[r], r)), l && e.close(); }), !1), a) : a; const g = a.util.currentScript(); function f() { a.manual || a.highlightAll(); } if (g && (a.filename = g.src, g.hasAttribute('data-manual') && (a.manual = !0)), !a.manual) { const h = document.readyState; h === 'loading' || h === 'interactive' && g && g.defer ? document.addEventListener('DOMContentLoaded', f) : window.requestAnimationFrame ? window.requestAnimationFrame(f) : window.setTimeout(f, 16); } return a;
}(_self)); typeof module !== 'undefined' && module.exports && (module.exports = Prism), typeof global !== 'undefined' && (global.Prism = Prism);
Prism.languages.markup = {
	comment: { pattern: /<!--(?:(?!<!--)[\s\S])*?-->/, greedy: !0 },
	prolog: { pattern: /<\?[\s\S]+?\?>/, greedy: !0 },
	doctype: {
		pattern: /<!DOCTYPE(?:[^>"'[\]]|"[^"]*"|'[^']*')+(?:\[(?:[^<"'\]]|"[^"]*"|'[^']*'|<(?!!--)|<!--(?:[^-]|-(?!->))*-->)*\]\s*)?>/i,
		greedy: !0,
		inside: {
			'internal-subset': {
				pattern: /(^[^\[]*\[)[\s\S]+(?=\]>$)/, lookbehind: !0, greedy: !0, inside: null,
			},
			string: { pattern: /"[^"]*"|'[^']*'/, greedy: !0 },
			punctuation: /^<!|>$|[[\]]/,
			'doctype-tag': /^DOCTYPE/i,
			name: /[^\s<>'"]+/,
		},
	},
	cdata: { pattern: /<!\[CDATA\[[\s\S]*?\]\]>/i, greedy: !0 },
	tag: {
		pattern: /<\/?(?!\d)[^\s>\/=$<%]+(?:\s(?:\s*[^\s>\/=]+(?:\s*=\s*(?:"[^"]*"|'[^']*'|[^\s'">=]+(?=[\s>]))|(?=[\s/>])))+)?\s*\/?>/,
		greedy: !0,
		inside: {
			tag: { pattern: /^<\/?[^\s>\/]+/, inside: { punctuation: /^<\/?/, namespace: /^[^\s>\/:]+:/ } }, 'special-attr': [], 'attr-value': { pattern: /=\s*(?:"[^"]*"|'[^']*'|[^\s'">=]+)/, inside: { punctuation: [{ pattern: /^=/, alias: 'attr-equals' }, { pattern: /^(\s*)["']|["']$/, lookbehind: !0 }] } }, punctuation: /\/?>/, 'attr-name': { pattern: /[^\s>\/]+/, inside: { namespace: /^[^\s>\/:]+:/ } },
		},
	},
	entity: [{ pattern: /&[\da-z]{1,8};/i, alias: 'named-entity' }, /&#x?[\da-f]{1,8};/i],
}, Prism.languages.markup.tag.inside['attr-value'].inside.entity = Prism.languages.markup.entity, Prism.languages.markup.doctype.inside['internal-subset'].inside = Prism.languages.markup, Prism.hooks.add('wrap', ((a) => { a.type === 'entity' && (a.attributes.title = a.content.replace(/&amp;/, '&')); })), Object.defineProperty(Prism.languages.markup.tag, 'addInlined', {
	value(a, e) {
		const s = {}; s[`language-${e}`] = { pattern: /(^<!\[CDATA\[)[\s\S]+?(?=\]\]>$)/i, lookbehind: !0, inside: Prism.languages[e] }, s.cdata = /^<!\[CDATA\[|\]\]>$/i; const t = { 'included-cdata': { pattern: /<!\[CDATA\[[\s\S]*?\]\]>/i, inside: s } }; t[`language-${e}`] = { pattern: /[\s\S]+/, inside: Prism.languages[e] }; const n = {}; n[a] = {
			pattern: RegExp('(<__[^>]*>)(?:<!\\[CDATA\\[(?:[^\\]]|\\](?!\\]>))*\\]\\]>|(?!<!\\[CDATA\\[)[^])*?(?=</__>)'.replace(/__/g, (() => a)), 'i'), lookbehind: !0, greedy: !0, inside: t,
		}, Prism.languages.insertBefore('markup', 'cdata', n);
	},
}), Object.defineProperty(Prism.languages.markup.tag, 'addAttribute', {
	value(a, e) {
		Prism.languages.markup.tag.inside['special-attr'].push({
			pattern: RegExp(`(^|["'\\s])(?:${a})\\s*=\\s*(?:"[^"]*"|'[^']*'|[^\\s'">=]+(?=[\\s>]))`, 'i'),
			lookbehind: !0,
			inside: {
				'attr-name': /^[^\s=]+/,
				'attr-value': {
					pattern: /=[\s\S]+/,
					inside: {
						value: {
							pattern: /(^=\s*(["']|(?!["'])))\S[\s\S]*(?=\2$)/, lookbehind: !0, alias: [e, `language-${e}`], inside: Prism.languages[e],
						},
						punctuation: [{ pattern: /^=/, alias: 'attr-equals' }, /"|'/],
					},
				},
			},
		});
	},
}), Prism.languages.html = Prism.languages.markup, Prism.languages.mathml = Prism.languages.markup, Prism.languages.svg = Prism.languages.markup, Prism.languages.xml = Prism.languages.extend('markup', {}), Prism.languages.ssml = Prism.languages.xml, Prism.languages.atom = Prism.languages.xml, Prism.languages.rss = Prism.languages.xml;
!(function (s) {
	const e = /(?:"(?:\\(?:\r\n|[\s\S])|[^"\\\r\n])*"|'(?:\\(?:\r\n|[\s\S])|[^'\\\r\n])*')/; s.languages.css = {
		comment: /\/\*[\s\S]*?\*\//, atrule: { pattern: RegExp(`@[\\w-](?:[^;{\\s"']|\\s+(?!\\s)|${e.source})*?(?:;|(?=\\s*\\{))`), inside: { rule: /^@[\w-]+/, 'selector-function-argument': { pattern: /(\bselector\s*\(\s*(?![\s)]))(?:[^()\s]|\s+(?![\s)])|\((?:[^()]|\([^()]*\))*\))+(?=\s*\))/, lookbehind: !0, alias: 'selector' }, keyword: { pattern: /(^|[^\w-])(?:and|not|only|or)(?![\w-])/, lookbehind: !0 } } }, url: { pattern: RegExp(`\\burl\\((?:${e.source}|(?:[^\\\\\r\n()"']|\\\\[^])*)\\)`, 'i'), greedy: !0, inside: { function: /^url/i, punctuation: /^\(|\)$/, string: { pattern: RegExp(`^${e.source}$`), alias: 'url' } } }, selector: { pattern: RegExp(`(^|[{}\\s])[^{}\\s](?:[^{};"'\\s]|\\s+(?![\\s{])|${e.source})*(?=\\s*\\{)`), lookbehind: !0 }, string: { pattern: e, greedy: !0 }, property: { pattern: /(^|[^-\w\xA0-\uFFFF])(?!\s)[-_a-z\xA0-\uFFFF](?:(?!\s)[-\w\xA0-\uFFFF])*(?=\s*:)/i, lookbehind: !0 }, important: /!important\b/i, function: { pattern: /(^|[^-a-z0-9])[-a-z0-9]+(?=\()/i, lookbehind: !0 }, punctuation: /[(){};:,]/,
	}, s.languages.css.atrule.inside.rest = s.languages.css; const t = s.languages.markup; t && (t.tag.addInlined('style', 'css'), t.tag.addAttribute('style', 'css'));
}(Prism));
Prism.languages.clike = {
	comment: [{ pattern: /(^|[^\\])\/\*[\s\S]*?(?:\*\/|$)/, lookbehind: !0, greedy: !0 }, { pattern: /(^|[^\\:])\/\/.*/, lookbehind: !0, greedy: !0 }], string: { pattern: /(["'])(?:\\(?:\r\n|[\s\S])|(?!\1)[^\\\r\n])*\1/, greedy: !0 }, 'class-name': { pattern: /(\b(?:class|extends|implements|instanceof|interface|new|trait)\s+|\bcatch\s+\()[\w.\\]+/i, lookbehind: !0, inside: { punctuation: /[.\\]/ } }, keyword: /\b(?:break|catch|continue|do|else|finally|for|function|if|in|instanceof|new|null|return|throw|try|while)\b/, boolean: /\b(?:false|true)\b/, function: /\b\w+(?=\()/, number: /\b0x[\da-f]+\b|(?:\b\d+(?:\.\d*)?|\B\.\d+)(?:e[+-]?\d+)?/i, operator: /[<>]=?|[!=]=?=?|--?|\+\+?|&&?|\|\|?|[?*/~^%]/, punctuation: /[{}[\];(),.:]/,
};
Prism.languages.javascript = Prism.languages.extend('clike', {
	'class-name': [Prism.languages.clike['class-name'], { pattern: /(^|[^$\w\xA0-\uFFFF])(?!\s)[_$A-Z\xA0-\uFFFF](?:(?!\s)[$\w\xA0-\uFFFF])*(?=\.(?:constructor|prototype))/, lookbehind: !0 }], keyword: [{ pattern: /((?:^|\})\s*)catch\b/, lookbehind: !0 }, { pattern: /(^|[^.]|\.\.\.\s*)\b(?:as|assert(?=\s*\{)|async(?=\s*(?:function\b|\(|[$\w\xA0-\uFFFF]|$))|await|break|case|class|const|continue|debugger|default|delete|do|else|enum|export|extends|finally(?=\s*(?:\{|$))|for|from(?=\s*(?:['"]|$))|function|(?:get|set)(?=\s*(?:[#\[$\w\xA0-\uFFFF]|$))|if|implements|import|in|instanceof|interface|let|new|null|of|package|private|protected|public|return|static|super|switch|this|throw|try|typeof|undefined|var|void|while|with|yield)\b/, lookbehind: !0 }], function: /#?(?!\s)[_$a-zA-Z\xA0-\uFFFF](?:(?!\s)[$\w\xA0-\uFFFF])*(?=\s*(?:\.\s*(?:apply|bind|call)\s*)?\()/, number: { pattern: RegExp('(^|[^\\w$])(?:NaN|Infinity|0[bB][01]+(?:_[01]+)*n?|0[oO][0-7]+(?:_[0-7]+)*n?|0[xX][\\dA-Fa-f]+(?:_[\\dA-Fa-f]+)*n?|\\d+(?:_\\d+)*n|(?:\\d+(?:_\\d+)*(?:\\.(?:\\d+(?:_\\d+)*)?)?|\\.\\d+(?:_\\d+)*)(?:[Ee][+-]?\\d+(?:_\\d+)*)?)(?![\\w$])'), lookbehind: !0 }, operator: /--|\+\+|\*\*=?|=>|&&=?|\|\|=?|[!=]==|<<=?|>>>?=?|[-+*/%&|^!=<>]=?|\.{3}|\?\?=?|\?\.?|[~:]/,
}), Prism.languages.javascript['class-name'][0].pattern = /(\b(?:class|extends|implements|instanceof|interface|new)\s+)[\w.\\]+/, Prism.languages.insertBefore('javascript', 'keyword', {
	regex: {
		pattern: RegExp("((?:^|[^$\\w\\xA0-\\uFFFF.\"'\\])\\s]|\\b(?:return|yield))\\s*)/(?:(?:\\[(?:[^\\]\\\\\r\n]|\\\\.)*\\]|\\\\.|[^/\\\\\\[\r\n])+/[dgimyus]{0,7}|(?:\\[(?:[^[\\]\\\\\r\n]|\\\\.|\\[(?:[^[\\]\\\\\r\n]|\\\\.|\\[(?:[^[\\]\\\\\r\n]|\\\\.)*\\])*\\])*\\]|\\\\.|[^/\\\\\\[\r\n])+/[dgimyus]{0,7}v[dgimyus]{0,7})(?=(?:\\s|/\\*(?:[^*]|\\*(?!/))*\\*/)*(?:$|[\r\n,.;:})\\]]|//))"),
		lookbehind: !0,
		greedy: !0,
		inside: {
			'regex-source': {
				pattern: /^(\/)[\s\S]+(?=\/[a-z]*$)/, lookbehind: !0, alias: 'language-regex', inside: Prism.languages.regex,
			},
			'regex-delimiter': /^\/|\/$/,
			'regex-flags': /^[a-z]+$/,
		},
	},
	'function-variable': { pattern: /#?(?!\s)[_$a-zA-Z\xA0-\uFFFF](?:(?!\s)[$\w\xA0-\uFFFF])*(?=\s*[=:]\s*(?:async\s*)?(?:\bfunction\b|(?:\((?:[^()]|\([^()]*\))*\)|(?!\s)[_$a-zA-Z\xA0-\uFFFF](?:(?!\s)[$\w\xA0-\uFFFF])*)\s*=>))/, alias: 'function' },
	parameter: [{ pattern: /(function(?:\s+(?!\s)[_$a-zA-Z\xA0-\uFFFF](?:(?!\s)[$\w\xA0-\uFFFF])*)?\s*\(\s*)(?!\s)(?:[^()\s]|\s+(?![\s)])|\([^()]*\))+(?=\s*\))/, lookbehind: !0, inside: Prism.languages.javascript }, { pattern: /(^|[^$\w\xA0-\uFFFF])(?!\s)[_$a-z\xA0-\uFFFF](?:(?!\s)[$\w\xA0-\uFFFF])*(?=\s*=>)/i, lookbehind: !0, inside: Prism.languages.javascript }, { pattern: /(\(\s*)(?!\s)(?:[^()\s]|\s+(?![\s)])|\([^()]*\))+(?=\s*\)\s*=>)/, lookbehind: !0, inside: Prism.languages.javascript }, { pattern: /((?:\b|\s|^)(?!(?:as|async|await|break|case|catch|class|const|continue|debugger|default|delete|do|else|enum|export|extends|finally|for|from|function|get|if|implements|import|in|instanceof|interface|let|new|null|of|package|private|protected|public|return|set|static|super|switch|this|throw|try|typeof|undefined|var|void|while|with|yield)(?![$\w\xA0-\uFFFF]))(?:(?!\s)[_$a-zA-Z\xA0-\uFFFF](?:(?!\s)[$\w\xA0-\uFFFF])*\s*)\(\s*|\]\s*\(\s*)(?!\s)(?:[^()\s]|\s+(?![\s)])|\([^()]*\))+(?=\s*\)\s*\{)/, lookbehind: !0, inside: Prism.languages.javascript }],
	constant: /\b[A-Z](?:[A-Z_]|\dx?)*\b/,
}), Prism.languages.insertBefore('javascript', 'string', {
	hashbang: { pattern: /^#!.*/, greedy: !0, alias: 'comment' },
	'template-string': { pattern: /`(?:\\[\s\S]|\$\{(?:[^{}]|\{(?:[^{}]|\{[^}]*\})*\})+\}|(?!\$\{)[^\\`])*`/, greedy: !0, inside: { 'template-punctuation': { pattern: /^`|`$/, alias: 'string' }, interpolation: { pattern: /((?:^|[^\\])(?:\\{2})*)\$\{(?:[^{}]|\{(?:[^{}]|\{[^}]*\})*\})+\}/, lookbehind: !0, inside: { 'interpolation-punctuation': { pattern: /^\$\{|\}$/, alias: 'punctuation' }, rest: Prism.languages.javascript } }, string: /[\s\S]+/ } },
	'string-property': {
		pattern: /((?:^|[,{])[ \t]*)(["'])(?:\\(?:\r\n|[\s\S])|(?!\2)[^\\\r\n])*\2(?=\s*:)/m, lookbehind: !0, greedy: !0, alias: 'property',
	},
}), Prism.languages.insertBefore('javascript', 'operator', { 'literal-property': { pattern: /((?:^|[,{])[ \t]*)(?!\s)[_$a-zA-Z\xA0-\uFFFF](?:(?!\s)[$\w\xA0-\uFFFF])*(?=\s*:)/m, lookbehind: !0, alias: 'property' } }), Prism.languages.markup && (Prism.languages.markup.tag.addInlined('script', 'javascript'), Prism.languages.markup.tag.addAttribute('on(?:abort|blur|change|click|composition(?:end|start|update)|dblclick|error|focus(?:in|out)?|key(?:down|up)|load|mouse(?:down|enter|leave|move|out|over|up)|reset|resize|scroll|select|slotchange|submit|unload|wheel)', 'javascript')), Prism.languages.js = Prism.languages.javascript;
!(function (e) {
	const t = '\\b(?:BASH|BASHOPTS|BASH_ALIASES|BASH_ARGC|BASH_ARGV|BASH_CMDS|BASH_COMPLETION_COMPAT_DIR|BASH_LINENO|BASH_REMATCH|BASH_SOURCE|BASH_VERSINFO|BASH_VERSION|COLORTERM|COLUMNS|COMP_WORDBREAKS|DBUS_SESSION_BUS_ADDRESS|DEFAULTS_PATH|DESKTOP_SESSION|DIRSTACK|DISPLAY|EUID|GDMSESSION|GDM_LANG|GNOME_KEYRING_CONTROL|GNOME_KEYRING_PID|GPG_AGENT_INFO|GROUPS|HISTCONTROL|HISTFILE|HISTFILESIZE|HISTSIZE|HOME|HOSTNAME|HOSTTYPE|IFS|INSTANCE|JOB|LANG|LANGUAGE|LC_ADDRESS|LC_ALL|LC_IDENTIFICATION|LC_MEASUREMENT|LC_MONETARY|LC_NAME|LC_NUMERIC|LC_PAPER|LC_TELEPHONE|LC_TIME|LESSCLOSE|LESSOPEN|LINES|LOGNAME|LS_COLORS|MACHTYPE|MAILCHECK|MANDATORY_PATH|NO_AT_BRIDGE|OLDPWD|OPTERR|OPTIND|ORBIT_SOCKETDIR|OSTYPE|PAPERSIZE|PATH|PIPESTATUS|PPID|PS1|PS2|PS3|PS4|PWD|RANDOM|REPLY|SECONDS|SELINUX_INIT|SESSION|SESSIONTYPE|SESSION_MANAGER|SHELL|SHELLOPTS|SHLVL|SSH_AUTH_SOCK|TERM|UID|UPSTART_EVENTS|UPSTART_INSTANCE|UPSTART_JOB|UPSTART_SESSION|USER|WINDOWID|XAUTHORITY|XDG_CONFIG_DIRS|XDG_CURRENT_DESKTOP|XDG_DATA_DIRS|XDG_GREETER_DATA_DIR|XDG_MENU_PREFIX|XDG_RUNTIME_DIR|XDG_SEAT|XDG_SEAT_PATH|XDG_SESSION_DESKTOP|XDG_SESSION_ID|XDG_SESSION_PATH|XDG_SESSION_TYPE|XDG_VTNR|XMODIFIERS)\\b'; const a = {
		pattern: /(^(["']?)\w+\2)[ \t]+\S.*/, lookbehind: !0, alias: 'punctuation', inside: null,
	}; const n = {
		bash: a,
		environment: { pattern: RegExp(`\\$${t}`), alias: 'constant' },
		variable: [{
			pattern: /\$?\(\([\s\S]+?\)\)/,
			greedy: !0,
			inside: {
				variable: [{ pattern: /(^\$\(\([\s\S]+)\)\)/, lookbehind: !0 }, /^\$\(\(/], number: /\b0x[\dA-Fa-f]+\b|(?:\b\d+(?:\.\d*)?|\B\.\d+)(?:[Ee]-?\d+)?/, operator: /--|\+\+|\*\*=?|<<=?|>>=?|&&|\|\||[=!+\-*/%<>^&|]=?|[?~:]/, punctuation: /\(\(?|\)\)?|,|;/,
			},
		}, { pattern: /\$\((?:\([^)]+\)|[^()])+\)|`[^`]+`/, greedy: !0, inside: { variable: /^\$\(|^`|\)$|`$/ } }, { pattern: /\$\{[^}]+\}/, greedy: !0, inside: { operator: /:[-=?+]?|[!\/]|##?|%%?|\^\^?|,,?/, punctuation: /[\[\]]/, environment: { pattern: RegExp(`(\\{)${t}`), lookbehind: !0, alias: 'constant' } } }, /\$(?:\w+|[#?*!@$])/],
		entity: /\\(?:[abceEfnrtv\\"]|O?[0-7]{1,3}|U[0-9a-fA-F]{8}|u[0-9a-fA-F]{4}|x[0-9a-fA-F]{1,2})/,
	}; e.languages.bash = {
		shebang: { pattern: /^#!\s*\/.*/, alias: 'important' },
		comment: { pattern: /(^|[^"{\\$])#.*/, lookbehind: !0 },
		'function-name': [{ pattern: /(\bfunction\s+)[\w-]+(?=(?:\s*\(?:\s*\))?\s*\{)/, lookbehind: !0, alias: 'function' }, { pattern: /\b[\w-]+(?=\s*\(\s*\)\s*\{)/, alias: 'function' }],
		'for-or-select': { pattern: /(\b(?:for|select)\s+)\w+(?=\s+in\s)/, alias: 'variable', lookbehind: !0 },
		'assign-left': {
			pattern: /(^|[\s;|&]|[<>]\()\w+(?:\.\w+)*(?=\+?=)/, inside: { environment: { pattern: RegExp(`(^|[\\s;|&]|[<>]\\()${t}`), lookbehind: !0, alias: 'constant' } }, alias: 'variable', lookbehind: !0,
		},
		parameter: { pattern: /(^|\s)-{1,2}(?:\w+:[+-]?)?\w+(?:\.\w+)*(?=[=\s]|$)/, alias: 'variable', lookbehind: !0 },
		string: [{
			pattern: /((?:^|[^<])<<-?\s*)(\w+)\s[\s\S]*?(?:\r?\n|\r)\2/, lookbehind: !0, greedy: !0, inside: n,
		}, {
			pattern: /((?:^|[^<])<<-?\s*)(["'])(\w+)\2\s[\s\S]*?(?:\r?\n|\r)\3/, lookbehind: !0, greedy: !0, inside: { bash: a },
		}, {
			pattern: /(^|[^\\](?:\\\\)*)"(?:\\[\s\S]|\$\([^)]+\)|\$(?!\()|`[^`]+`|[^"\\`$])*"/, lookbehind: !0, greedy: !0, inside: n,
		}, { pattern: /(^|[^$\\])'[^']*'/, lookbehind: !0, greedy: !0 }, { pattern: /\$'(?:[^'\\]|\\[\s\S])*'/, greedy: !0, inside: { entity: n.entity } }],
		environment: { pattern: RegExp(`\\$?${t}`), alias: 'constant' },
		variable: n.variable,
		function: { pattern: /(^|[\s;|&]|[<>]\()(?:add|apropos|apt|apt-cache|apt-get|aptitude|aspell|automysqlbackup|awk|basename|bash|bc|bconsole|bg|bzip2|cal|cargo|cat|cfdisk|chgrp|chkconfig|chmod|chown|chroot|cksum|clear|cmp|column|comm|composer|cp|cron|crontab|csplit|curl|cut|date|dc|dd|ddrescue|debootstrap|df|diff|diff3|dig|dir|dircolors|dirname|dirs|dmesg|docker|docker-compose|du|egrep|eject|env|ethtool|expand|expect|expr|fdformat|fdisk|fg|fgrep|file|find|fmt|fold|format|free|fsck|ftp|fuser|gawk|git|gparted|grep|groupadd|groupdel|groupmod|groups|grub-mkconfig|gzip|halt|head|hg|history|host|hostname|htop|iconv|id|ifconfig|ifdown|ifup|import|install|ip|java|jobs|join|kill|killall|less|link|ln|locate|logname|logrotate|look|lpc|lpr|lprint|lprintd|lprintq|lprm|ls|lsof|lynx|make|man|mc|mdadm|mkconfig|mkdir|mke2fs|mkfifo|mkfs|mkisofs|mknod|mkswap|mmv|more|most|mount|mtools|mtr|mutt|mv|nano|nc|netstat|nice|nl|node|nohup|notify-send|npm|nslookup|op|open|parted|passwd|paste|pathchk|ping|pkill|pnpm|podman|podman-compose|popd|pr|printcap|printenv|ps|pushd|pv|quota|quotacheck|quotactl|ram|rar|rcp|reboot|remsync|rename|renice|rev|rm|rmdir|rpm|rsync|scp|screen|sdiff|sed|sendmail|seq|service|sftp|sh|shellcheck|shuf|shutdown|sleep|slocate|sort|split|ssh|stat|strace|su|sudo|sum|suspend|swapon|sync|sysctl|tac|tail|tar|tee|time|timeout|top|touch|tr|traceroute|tsort|tty|umount|uname|unexpand|uniq|units|unrar|unshar|unzip|update-grub|uptime|useradd|userdel|usermod|users|uudecode|uuencode|v|vcpkg|vdir|vi|vim|virsh|vmstat|wait|watch|wc|wget|whereis|which|who|whoami|write|xargs|xdg-open|yarn|yes|zenity|zip|zsh|zypper)(?=$|[)\s;|&])/, lookbehind: !0 },
		keyword: { pattern: /(^|[\s;|&]|[<>]\()(?:case|do|done|elif|else|esac|fi|for|function|if|in|select|then|until|while)(?=$|[)\s;|&])/, lookbehind: !0 },
		builtin: { pattern: /(^|[\s;|&]|[<>]\()(?:\.|:|alias|bind|break|builtin|caller|cd|command|continue|declare|echo|enable|eval|exec|exit|export|getopts|hash|help|let|local|logout|mapfile|printf|pwd|read|readarray|readonly|return|set|shift|shopt|source|test|times|trap|type|typeset|ulimit|umask|unalias|unset)(?=$|[)\s;|&])/, lookbehind: !0, alias: 'class-name' },
		boolean: { pattern: /(^|[\s;|&]|[<>]\()(?:false|true)(?=$|[)\s;|&])/, lookbehind: !0 },
		'file-descriptor': { pattern: /\B&\d\b/, alias: 'important' },
		operator: { pattern: /\d?<>|>\||\+=|=[=~]?|!=?|<<[<-]?|[&\d]?>>|\d[<>]&?|[<>][&=]?|&[>&]?|\|[&|]?/, inside: { 'file-descriptor': { pattern: /^\d/, alias: 'important' } } },
		punctuation: /\$?\(\(?|\)\)?|\.\.|[{}[\];\\]/,
		number: { pattern: /(^|\s)(?:[1-9]\d*|0)(?:[.,]\d+)?\b/, lookbehind: !0 },
	}, a.inside = e.languages.bash; for (let s = ['comment', 'function-name', 'for-or-select', 'assign-left', 'parameter', 'string', 'environment', 'function', 'keyword', 'builtin', 'boolean', 'file-descriptor', 'operator', 'punctuation', 'number'], o = n.variable[1].inside, i = 0; i < s.length; i++)o[s[i]] = e.languages.bash[s[i]]; e.languages.sh = e.languages.bash, e.languages.shell = e.languages.bash;
}(Prism));
!(function (e) { function n(e, n) { return `___${e.toUpperCase()}${n}___`; }Object.defineProperties(e.languages['markup-templating'] = {}, { buildPlaceholders: { value(t, a, r, o) { if (t.language === a) { const c = t.tokenStack = []; t.code = t.code.replace(r, ((e) => { if (typeof o === 'function' && !o(e)) return e; for (var r, i = c.length; t.code.indexOf(r = n(a, i)) !== -1;)++i; return c[i] = e, r; })), t.grammar = e.languages.markup; } } }, tokenizePlaceholders: { value(t, a) { if (t.language === a && t.tokenStack) { t.grammar = e.languages[a]; let r = 0; const o = Object.keys(t.tokenStack); !(function c(i) { for (let u = 0; u < i.length && !(r >= o.length); u++) { const g = i[u]; if (typeof g === 'string' || g.content && typeof g.content === 'string') { const l = o[r]; const s = t.tokenStack[l]; const f = typeof g === 'string' ? g : g.content; const p = n(a, l); const k = f.indexOf(p); if (k > -1) { ++r; const m = f.substring(0, k); const d = new e.Token(a, e.tokenize(s, t.grammar), `language-${a}`, s); const h = f.substring(k + p.length); const v = []; m && v.push.apply(v, c([m])), v.push(d), h && v.push.apply(v, c([h])), typeof g === 'string' ? i.splice.apply(i, [u, 1].concat(v)) : g.content = v; } } else g.content && c(g.content); } return i; }(t.tokens)); } } } }); }(Prism));
!(function (e) {
	const a = /\/\*[\s\S]*?\*\/|\/\/.*|#(?!\[).*/; const t = [{ pattern: /\b(?:false|true)\b/i, alias: 'boolean' }, { pattern: /(::\s*)\b[a-z_]\w*\b(?!\s*\()/i, greedy: !0, lookbehind: !0 }, { pattern: /(\b(?:case|const)\s+)\b[a-z_]\w*(?=\s*[;=])/i, greedy: !0, lookbehind: !0 }, /\b(?:null)\b/i, /\b[A-Z_][A-Z0-9_]*\b(?!\s*\()/]; const i = /\b0b[01]+(?:_[01]+)*\b|\b0o[0-7]+(?:_[0-7]+)*\b|\b0x[\da-f]+(?:_[\da-f]+)*\b|(?:\b\d+(?:_\d+)*\.?(?:\d+(?:_\d+)*)?|\B\.\d+)(?:e[+-]?\d+)?/i; const n = /<?=>|\?\?=?|\.{3}|\??->|[!=]=?=?|::|\*\*=?|--|\+\+|&&|\|\||<<|>>|[?~]|[/^|%*&<>.+-]=?/; const s = /[{}\[\](),:;]/; e.languages.php = {
		delimiter: { pattern: /\?>$|^<\?(?:php(?=\s)|=)?/i, alias: 'important' },
		comment: a,
		variable: /\$+(?:\w+\b|(?=\{))/,
		package: { pattern: /(namespace\s+|use\s+(?:function\s+)?)(?:\\?\b[a-z_]\w*)+\b(?!\\)/i, lookbehind: !0, inside: { punctuation: /\\/ } },
		'class-name-definition': { pattern: /(\b(?:class|enum|interface|trait)\s+)\b[a-z_]\w*(?!\\)\b/i, lookbehind: !0, alias: 'class-name' },
		'function-definition': { pattern: /(\bfunction\s+)[a-z_]\w*(?=\s*\()/i, lookbehind: !0, alias: 'function' },
		keyword: [{
			pattern: /(\(\s*)\b(?:array|bool|boolean|float|int|integer|object|string)\b(?=\s*\))/i, alias: 'type-casting', greedy: !0, lookbehind: !0,
		}, {
			pattern: /([(,?]\s*)\b(?:array(?!\s*\()|bool|callable|(?:false|null)(?=\s*\|)|float|int|iterable|mixed|object|self|static|string)\b(?=\s*\$)/i, alias: 'type-hint', greedy: !0, lookbehind: !0,
		}, {
			pattern: /(\)\s*:\s*(?:\?\s*)?)\b(?:array(?!\s*\()|bool|callable|(?:false|null)(?=\s*\|)|float|int|iterable|mixed|never|object|self|static|string|void)\b/i, alias: 'return-type', greedy: !0, lookbehind: !0,
		}, { pattern: /\b(?:array(?!\s*\()|bool|float|int|iterable|mixed|object|string|void)\b/i, alias: 'type-declaration', greedy: !0 }, {
			pattern: /(\|\s*)(?:false|null)\b|\b(?:false|null)(?=\s*\|)/i, alias: 'type-declaration', greedy: !0, lookbehind: !0,
		}, { pattern: /\b(?:parent|self|static)(?=\s*::)/i, alias: 'static-context', greedy: !0 }, { pattern: /(\byield\s+)from\b/i, lookbehind: !0 }, /\bclass\b/i, { pattern: /((?:^|[^\s>:]|(?:^|[^-])>|(?:^|[^:]):)\s*)\b(?:abstract|and|array|as|break|callable|case|catch|clone|const|continue|declare|default|die|do|echo|else|elseif|empty|enddeclare|endfor|endforeach|endif|endswitch|endwhile|enum|eval|exit|extends|final|finally|fn|for|foreach|function|global|goto|if|implements|include|include_once|instanceof|insteadof|interface|isset|list|match|namespace|never|new|or|parent|print|private|protected|public|readonly|require|require_once|return|self|static|switch|throw|trait|try|unset|use|var|while|xor|yield|__halt_compiler)\b/i, lookbehind: !0 }],
		'argument-name': { pattern: /([(,]\s*)\b[a-z_]\w*(?=\s*:(?!:))/i, lookbehind: !0 },
		'class-name': [{ pattern: /(\b(?:extends|implements|instanceof|new(?!\s+self|\s+static))\s+|\bcatch\s*\()\b[a-z_]\w*(?!\\)\b/i, greedy: !0, lookbehind: !0 }, { pattern: /(\|\s*)\b[a-z_]\w*(?!\\)\b/i, greedy: !0, lookbehind: !0 }, { pattern: /\b[a-z_]\w*(?!\\)\b(?=\s*\|)/i, greedy: !0 }, {
			pattern: /(\|\s*)(?:\\?\b[a-z_]\w*)+\b/i, alias: 'class-name-fully-qualified', greedy: !0, lookbehind: !0, inside: { punctuation: /\\/ },
		}, {
			pattern: /(?:\\?\b[a-z_]\w*)+\b(?=\s*\|)/i, alias: 'class-name-fully-qualified', greedy: !0, inside: { punctuation: /\\/ },
		}, {
			pattern: /(\b(?:extends|implements|instanceof|new(?!\s+self\b|\s+static\b))\s+|\bcatch\s*\()(?:\\?\b[a-z_]\w*)+\b(?!\\)/i, alias: 'class-name-fully-qualified', greedy: !0, lookbehind: !0, inside: { punctuation: /\\/ },
		}, { pattern: /\b[a-z_]\w*(?=\s*\$)/i, alias: 'type-declaration', greedy: !0 }, {
			pattern: /(?:\\?\b[a-z_]\w*)+(?=\s*\$)/i, alias: ['class-name-fully-qualified', 'type-declaration'], greedy: !0, inside: { punctuation: /\\/ },
		}, { pattern: /\b[a-z_]\w*(?=\s*::)/i, alias: 'static-context', greedy: !0 }, {
			pattern: /(?:\\?\b[a-z_]\w*)+(?=\s*::)/i, alias: ['class-name-fully-qualified', 'static-context'], greedy: !0, inside: { punctuation: /\\/ },
		}, {
			pattern: /([(,?]\s*)[a-z_]\w*(?=\s*\$)/i, alias: 'type-hint', greedy: !0, lookbehind: !0,
		}, {
			pattern: /([(,?]\s*)(?:\\?\b[a-z_]\w*)+(?=\s*\$)/i, alias: ['class-name-fully-qualified', 'type-hint'], greedy: !0, lookbehind: !0, inside: { punctuation: /\\/ },
		}, {
			pattern: /(\)\s*:\s*(?:\?\s*)?)\b[a-z_]\w*(?!\\)\b/i, alias: 'return-type', greedy: !0, lookbehind: !0,
		}, {
			pattern: /(\)\s*:\s*(?:\?\s*)?)(?:\\?\b[a-z_]\w*)+\b(?!\\)/i, alias: ['class-name-fully-qualified', 'return-type'], greedy: !0, lookbehind: !0, inside: { punctuation: /\\/ },
		}],
		constant: t,
		function: { pattern: /(^|[^\\\w])\\?[a-z_](?:[\w\\]*\w)?(?=\s*\()/i, lookbehind: !0, inside: { punctuation: /\\/ } },
		property: { pattern: /(->\s*)\w+/, lookbehind: !0 },
		number: i,
		operator: n,
		punctuation: s,
	}; const l = { pattern: /\{\$(?:\{(?:\{[^{}]+\}|[^{}]+)\}|[^{}])+\}|(^|[^\\{])\$+(?:\w+(?:\[[^\r\n\[\]]+\]|->\w+)?)/, lookbehind: !0, inside: e.languages.php }; const r = [{
		pattern: /<<<'([^']+)'[\r\n](?:.*[\r\n])*?\1;/, alias: 'nowdoc-string', greedy: !0, inside: { delimiter: { pattern: /^<<<'[^']+'|[a-z_]\w*;$/i, alias: 'symbol', inside: { punctuation: /^<<<'?|[';]$/ } } },
	}, {
		pattern: /<<<(?:"([^"]+)"[\r\n](?:.*[\r\n])*?\1;|([a-z_]\w*)[\r\n](?:.*[\r\n])*?\2;)/i, alias: 'heredoc-string', greedy: !0, inside: { delimiter: { pattern: /^<<<(?:"[^"]+"|[a-z_]\w*)|[a-z_]\w*;$/i, alias: 'symbol', inside: { punctuation: /^<<<"?|[";]$/ } }, interpolation: l },
	}, { pattern: /`(?:\\[\s\S]|[^\\`])*`/, alias: 'backtick-quoted-string', greedy: !0 }, { pattern: /'(?:\\[\s\S]|[^\\'])*'/, alias: 'single-quoted-string', greedy: !0 }, {
		pattern: /"(?:\\[\s\S]|[^\\"])*"/, alias: 'double-quoted-string', greedy: !0, inside: { interpolation: l },
	}]; e.languages.insertBefore('php', 'variable', {
		string: r,
		attribute: {
			pattern: /#\[(?:[^"'\/#]|\/(?![*/])|\/\/.*$|#(?!\[).*$|\/\*(?:[^*]|\*(?!\/))*\*\/|"(?:\\[\s\S]|[^\\"])*"|'(?:\\[\s\S]|[^\\'])*')+\](?=\s*[a-z$#])/im,
			greedy: !0,
			inside: {
				'attribute-content': {
					pattern: /^(#\[)[\s\S]+(?=\]$)/,
					lookbehind: !0,
					inside: {
						comment: a,
						string: r,
						'attribute-class-name': [{
							pattern: /([^:]|^)\b[a-z_]\w*(?!\\)\b/i, alias: 'class-name', greedy: !0, lookbehind: !0,
						}, {
							pattern: /([^:]|^)(?:\\?\b[a-z_]\w*)+/i, alias: ['class-name', 'class-name-fully-qualified'], greedy: !0, lookbehind: !0, inside: { punctuation: /\\/ },
						}],
						constant: t,
						number: i,
						operator: n,
						punctuation: s,
					},
				},
				delimiter: { pattern: /^#\[|\]$/, alias: 'punctuation' },
			},
		},
	}), e.hooks.add('before-tokenize', ((a) => { /<\?/.test(a.code) && e.languages['markup-templating'].buildPlaceholders(a, 'php', /<\?(?:[^"'/#]|\/(?![*/])|("|')(?:\\[\s\S]|(?!\1)[^\\])*\1|(?:\/\/|#(?!\[))(?:[^?\n\r]|\?(?!>))*(?=$|\?>|[\r\n])|#\[|\/\*(?:[^*]|\*(?!\/))*(?:\*\/|$))*?(?:\?>|$)/g); })), e.hooks.add('after-tokenize', ((a) => { e.languages['markup-templating'].tokenizePlaceholders(a, 'php'); }));
}(Prism));
!(function (a) { const e = a.languages.javadoclike = { parameter: { pattern: /(^[\t ]*(?:\/{3}|\*|\/\*\*)\s*@(?:arg|arguments|param)\s+)\w+/m, lookbehind: !0 }, keyword: { pattern: /(^[\t ]*(?:\/{3}|\*|\/\*\*)\s*|\{)@[a-z][a-zA-Z-]+\b/m, lookbehind: !0 }, punctuation: /[{}]/ }; Object.defineProperty(e, 'addSupport', { value(e, n) { typeof e === 'string' && (e = [e]), e.forEach(((e) => { !(function (e, n) { const t = 'doc-comment'; let r = a.languages[e]; if (r) { let o = r[t]; if (o || (o = (r = a.languages.insertBefore(e, 'comment', { 'doc-comment': { pattern: /(^|[^\\])\/\*\*[^/][\s\S]*?(?:\*\/|$)/, lookbehind: !0, alias: 'comment' } }))[t]), o instanceof RegExp && (o = r[t] = { pattern: o }), Array.isArray(o)) for (let i = 0, s = o.length; i < s; i++)o[i] instanceof RegExp && (o[i] = { pattern: o[i] }), n(o[i]); else n(o); } }(e, ((a) => { a.inside || (a.inside = {}), a.inside.rest = n; }))); })); } }), e.addSupport(['java', 'javascript', 'php'], e); }(Prism));
!(function (e) {
	e.languages.typescript = e.languages.extend('javascript', {
		'class-name': {
			pattern: /(\b(?:class|extends|implements|instanceof|interface|new|type)\s+)(?!keyof\b)(?!\s)[_$a-zA-Z\xA0-\uFFFF](?:(?!\s)[$\w\xA0-\uFFFF])*(?:\s*<(?:[^<>]|<(?:[^<>]|<[^<>]*>)*>)*>)?/, lookbehind: !0, greedy: !0, inside: null,
		},
		builtin: /\b(?:Array|Function|Promise|any|boolean|console|never|number|string|symbol|unknown)\b/,
	}), e.languages.typescript.keyword.push(/\b(?:abstract|declare|is|keyof|readonly|require)\b/, /\b(?:asserts|infer|interface|module|namespace|type)\b(?=\s*(?:[{_$a-zA-Z\xA0-\uFFFF]|$))/, /\btype\b(?=\s*(?:[\{*]|$))/), delete e.languages.typescript.parameter, delete e.languages.typescript['literal-property']; const s = e.languages.extend('typescript', {}); delete s['class-name'], e.languages.typescript['class-name'].inside = s, e.languages.insertBefore('typescript', 'function', { decorator: { pattern: /@[$\w\xA0-\uFFFF]+/, inside: { at: { pattern: /^@/, alias: 'operator' }, function: /^[\s\S]+/ } }, 'generic-function': { pattern: /#?(?!\s)[_$a-zA-Z\xA0-\uFFFF](?:(?!\s)[$\w\xA0-\uFFFF])*\s*<(?:[^<>]|<(?:[^<>]|<[^<>]*>)*>)*>(?=\s*\()/, greedy: !0, inside: { function: /^#?(?!\s)[_$a-zA-Z\xA0-\uFFFF](?:(?!\s)[$\w\xA0-\uFFFF])*/, generic: { pattern: /<[\s\S]+/, alias: 'class-name', inside: s } } } }), e.languages.ts = e.languages.typescript;
}(Prism));
!(function (e) {
	const a = e.languages.javascript; const n = '\\{(?:[^{}]|\\{(?:[^{}]|\\{[^{}]*\\})*\\})+\\}'; const t = `(@(?:arg|argument|param|property)\\s+(?:${n}\\s+)?)`; e.languages.jsdoc = e.languages.extend('javadoclike', { parameter: { pattern: RegExp(`${t}(?:(?!\\s)[$\\w\\xA0-\\uFFFF.])+(?=\\s|$)`), lookbehind: !0, inside: { punctuation: /\./ } } }), e.languages.insertBefore('jsdoc', 'keyword', {
		'optional-parameter': {
			pattern: RegExp(`${t}\\[(?:(?!\\s)[$\\w\\xA0-\\uFFFF.])+(?:=[^[\\]]+)?\\](?=\\s|$)`),
			lookbehind: !0,
			inside: {
				parameter: { pattern: /(^\[)[$\w\xA0-\uFFFF\.]+/, lookbehind: !0, inside: { punctuation: /\./ } },
				code: {
					pattern: /(=)[\s\S]*(?=\]$)/, lookbehind: !0, inside: a, alias: 'language-javascript',
				},
				punctuation: /[=[\]]/,
			},
		},
		'class-name': [{ pattern: RegExp('(@(?:augments|class|extends|interface|memberof!?|template|this|typedef)\\s+(?:<TYPE>\\s+)?)[A-Z]\\w*(?:\\.[A-Z]\\w*)*'.replace(/<TYPE>/g, (() => n))), lookbehind: !0, inside: { punctuation: /\./ } }, {
			pattern: RegExp(`(@[a-z]+\\s+)${n}`),
			lookbehind: !0,
			inside: {
				string: a.string, number: a.number, boolean: a.boolean, keyword: e.languages.typescript.keyword, operator: /=>|\.\.\.|[&|?:*]/, punctuation: /[.,;=<>{}()[\]]/,
			},
		}],
		example: {
			pattern: /(@example\s+(?!\s))(?:[^@\s]|\s+(?!\s))+?(?=\s*(?:\*\s*)?(?:@\w|\*\/))/,
			lookbehind: !0,
			inside: {
				code: {
					pattern: /^([\t ]*(?:\*\s*)?)\S.*$/m, lookbehind: !0, inside: a, alias: 'language-javascript',
				},
			},
		},
	}), e.languages.javadoclike.addSupport('javascript', e.languages.jsdoc);
}(Prism));
Prism.languages.json = {
	property: { pattern: /(^|[^\\])"(?:\\.|[^\\"\r\n])*"(?=\s*:)/, lookbehind: !0, greedy: !0 }, string: { pattern: /(^|[^\\])"(?:\\.|[^\\"\r\n])*"(?!\s*:)/, lookbehind: !0, greedy: !0 }, comment: { pattern: /\/\/.*|\/\*[\s\S]*?(?:\*\/|$)/, greedy: !0 }, number: /-?\b\d+(?:\.\d+)?(?:e[+-]?\d+)?\b/i, punctuation: /[{}[\],]/, operator: /:/, boolean: /\b(?:false|true)\b/, null: { pattern: /\bnull\b/, alias: 'keyword' },
}, Prism.languages.webmanifest = Prism.languages.json;
!(function (n) {
	function e(n) { return n = n.replace(/<inner>/g, (() => '(?:\\\\.|[^\\\\\n\r]|(?:\n|\r\n?)(?![\r\n]))')), RegExp(`((?:^|[^\\\\])(?:\\\\{2})*)(?:${n})`); } const t = '(?:\\\\.|``(?:[^`\r\n]|`(?!`))+``|`[^`\r\n]+`|[^\\\\|\r\n`])+'; const a = '\\|?__(?:\\|__)+\\|?(?:(?:\n|\r\n?)|(?![^]))'.replace(/__/g, (() => t)); const i = '\\|?[ \t]*:?-{3,}:?[ \t]*(?:\\|[ \t]*:?-{3,}:?[ \t]*)+\\|?(?:\n|\r\n?)'; n.languages.markdown = n.languages.extend('markup', {}), n.languages.insertBefore('markdown', 'prolog', {
		'front-matter-block': {
			pattern: /(^(?:\s*[\r\n])?)---(?!.)[\s\S]*?[\r\n]---(?!.)/, lookbehind: !0, greedy: !0, inside: { punctuation: /^---|---$/, 'front-matter': { pattern: /\S+(?:\s+\S+)*/, alias: ['yaml', 'language-yaml'], inside: n.languages.yaml } },
		},
		blockquote: { pattern: /^>(?:[\t ]*>)*/m, alias: 'punctuation' },
		table: { pattern: RegExp(`^${a}${i}(?:${a})*`, 'm'), inside: { 'table-data-rows': { pattern: RegExp(`^(${a}${i})(?:${a})*$`), lookbehind: !0, inside: { 'table-data': { pattern: RegExp(t), inside: n.languages.markdown }, punctuation: /\|/ } }, 'table-line': { pattern: RegExp(`^(${a})${i}$`), lookbehind: !0, inside: { punctuation: /\||:?-{3,}:?/ } }, 'table-header-row': { pattern: RegExp(`^${a}$`), inside: { 'table-header': { pattern: RegExp(t), alias: 'important', inside: n.languages.markdown }, punctuation: /\|/ } } } },
		code: [{ pattern: /((?:^|\n)[ \t]*\n|(?:^|\r\n?)[ \t]*\r\n?)(?: {4}|\t).+(?:(?:\n|\r\n?)(?: {4}|\t).+)*/, lookbehind: !0, alias: 'keyword' }, { pattern: /^```[\s\S]*?^```$/m, greedy: !0, inside: { 'code-block': { pattern: /^(```.*(?:\n|\r\n?))[\s\S]+?(?=(?:\n|\r\n?)^```$)/m, lookbehind: !0 }, 'code-language': { pattern: /^(```).+/, lookbehind: !0 }, punctuation: /```/ } }],
		title: [{ pattern: /\S.*(?:\n|\r\n?)(?:==+|--+)(?=[ \t]*$)/m, alias: 'important', inside: { punctuation: /==+$|--+$/ } }, {
			pattern: /(^\s*)#.+/m, lookbehind: !0, alias: 'important', inside: { punctuation: /^#+|#+$/ },
		}],
		hr: { pattern: /(^\s*)([*-])(?:[\t ]*\2){2,}(?=\s*$)/m, lookbehind: !0, alias: 'punctuation' },
		list: { pattern: /(^\s*)(?:[*+-]|\d+\.)(?=[\t ].)/m, lookbehind: !0, alias: 'punctuation' },
		'url-reference': { pattern: /!?\[[^\]]+\]:[\t ]+(?:\S+|<(?:\\.|[^>\\])+>)(?:[\t ]+(?:"(?:\\.|[^"\\])*"|'(?:\\.|[^'\\])*'|\((?:\\.|[^)\\])*\)))?/, inside: { variable: { pattern: /^(!?\[)[^\]]+/, lookbehind: !0 }, string: /(?:"(?:\\.|[^"\\])*"|'(?:\\.|[^'\\])*'|\((?:\\.|[^)\\])*\))$/, punctuation: /^[\[\]!:]|[<>]/ }, alias: 'url' },
		bold: {
			pattern: e('\\b__(?:(?!_)<inner>|_(?:(?!_)<inner>)+_)+__\\b|\\*\\*(?:(?!\\*)<inner>|\\*(?:(?!\\*)<inner>)+\\*)+\\*\\*'), lookbehind: !0, greedy: !0, inside: { content: { pattern: /(^..)[\s\S]+(?=..$)/, lookbehind: !0, inside: {} }, punctuation: /\*\*|__/ },
		},
		italic: {
			pattern: e('\\b_(?:(?!_)<inner>|__(?:(?!_)<inner>)+__)+_\\b|\\*(?:(?!\\*)<inner>|\\*\\*(?:(?!\\*)<inner>)+\\*\\*)+\\*'), lookbehind: !0, greedy: !0, inside: { content: { pattern: /(^.)[\s\S]+(?=.$)/, lookbehind: !0, inside: {} }, punctuation: /[*_]/ },
		},
		strike: {
			pattern: e('(~~?)(?:(?!~)<inner>)+\\2'), lookbehind: !0, greedy: !0, inside: { content: { pattern: /(^~~?)[\s\S]+(?=\1$)/, lookbehind: !0, inside: {} }, punctuation: /~~?/ },
		},
		'code-snippet': {
			pattern: /(^|[^\\`])(?:``[^`\r\n]+(?:`[^`\r\n]+)*``(?!`)|`[^`\r\n]+`(?!`))/, lookbehind: !0, greedy: !0, alias: ['code', 'keyword'],
		},
		url: {
			pattern: e('!?\\[(?:(?!\\])<inner>)+\\](?:\\([^\\s)]+(?:[\t ]+"(?:\\\\.|[^"\\\\])*")?\\)|[ \t]?\\[(?:(?!\\])<inner>)+\\])'),
			lookbehind: !0,
			greedy: !0,
			inside: {
				operator: /^!/, content: { pattern: /(^\[)[^\]]+(?=\])/, lookbehind: !0, inside: {} }, variable: { pattern: /(^\][ \t]?\[)[^\]]+(?=\]$)/, lookbehind: !0 }, url: { pattern: /(^\]\()[^\s)]+/, lookbehind: !0 }, string: { pattern: /(^[ \t]+)"(?:\\.|[^"\\])*"(?=\)$)/, lookbehind: !0 },
			},
		},
	}), ['url', 'bold', 'italic', 'strike'].forEach(((e) => { ['url', 'bold', 'italic', 'strike', 'code-snippet'].forEach(((t) => { e !== t && (n.languages.markdown[e].inside.content.inside[t] = n.languages.markdown[t]); })); })), n.hooks.add('after-tokenize', ((n) => { n.language !== 'markdown' && n.language !== 'md' || (function n(e) { if (e && typeof e !== 'string') for (let t = 0, a = e.length; t < a; t++) { const i = e[t]; if (i.type === 'code') { const r = i.content[1]; const o = i.content[3]; if (r && o && r.type === 'code-language' && o.type === 'code-block' && typeof r.content === 'string') { let l = r.content.replace(/\b#/g, 'sharp').replace(/\b\+\+/g, 'pp'); const s = `language-${l = (/[a-z][\w-]*/i.exec(l) || [''])[0].toLowerCase()}`; o.alias ? typeof o.alias === 'string' ? o.alias = [o.alias, s] : o.alias.push(s) : o.alias = [s]; } } else n(i.content); } }(n.tokens)); })), n.hooks.add('wrap', ((e) => { if (e.type === 'code-block') { for (var t = '', a = 0, i = e.classes.length; a < i; a++) { const s = e.classes[a]; const d = /language-(.+)/.exec(s); if (d) { t = d[1]; break; } } const p = n.languages[t]; if (p)e.content = n.highlight(e.content.replace(r, '').replace(/&(\w{1,8}|#x?[\da-f]{1,8});/gi, ((n, e) => { let t; return (e = e.toLowerCase())[0] === '#' ? (t = e[1] === 'x' ? parseInt(e.slice(2), 16) : Number(e.slice(1)), l(t)) : o[e] || n; })), p, t); else if (t && t !== 'none' && n.plugins.autoloader) { const u = `md-${(new Date()).valueOf()}-${Math.floor(1e16 * Math.random())}`; e.attributes.id = u, n.plugins.autoloader.loadLanguages(t, (() => { const e = document.getElementById(u); e && (e.innerHTML = n.highlight(e.textContent, n.languages[t], t)); })); } } })); var r = RegExp(n.languages.markup.tag.pattern.source, 'gi'); var o = {
		amp: '&', lt: '<', gt: '>', quot: '"',
	}; var l = String.fromCodePoint || String.fromCharCode; n.languages.md = n.languages.markdown;
}(Prism));
!(function (a) { const e = '(?:\\b[a-zA-Z]\\w*|[|\\\\[\\]])+'; a.languages.phpdoc = a.languages.extend('javadoclike', { parameter: { pattern: RegExp(`(@(?:global|param|property(?:-read|-write)?|var)\\s+(?:${e}\\s+)?)\\$\\w+`), lookbehind: !0 } }), a.languages.insertBefore('phpdoc', 'keyword', { 'class-name': [{ pattern: RegExp(`(@(?:global|package|param|property(?:-read|-write)?|return|subpackage|throws|var)\\s+)${e}`), lookbehind: !0, inside: { keyword: /\b(?:array|bool|boolean|callback|double|false|float|int|integer|mixed|null|object|resource|self|string|true|void)\b/, punctuation: /[|\\[\]()]/ } }] }), a.languages.javadoclike.addSupport('php', a.languages.phpdoc); }(Prism));
Prism.languages.insertBefore('php', 'variable', { this: { pattern: /\$this\b/, alias: 'keyword' }, global: /\$(?:GLOBALS|HTTP_RAW_POST_DATA|_(?:COOKIE|ENV|FILES|GET|POST|REQUEST|SERVER|SESSION)|argc|argv|http_response_header|php_errormsg)\b/, scope: { pattern: /\b[\w\\]+::/, inside: { keyword: /\b(?:parent|self|static)\b/, punctuation: /::|\\/ } } });
!(function (e) {
	const n = /[*&][^\s[\]{},]+/; const r = /!(?:<[\w\-%#;/?:@&=+$,.!~*'()[\]]+>|(?:[a-zA-Z\d-]*!)?[\w\-%#;/?:@&=+$.~*'()]+)?/; const t = `(?:${r.source}(?:[ \t]+${n.source})?|${n.source}(?:[ \t]+${r.source})?)`; const a = "(?:[^\\s\\x00-\\x08\\x0e-\\x1f!\"#%&'*,\\-:>?@[\\]`{|}\\x7f-\\x84\\x86-\\x9f\\ud800-\\udfff\\ufffe\\uffff]|[?:-]<PLAIN>)(?:[ \t]*(?:(?![#:])<PLAIN>|:<PLAIN>))*".replace(/<PLAIN>/g, (() => '[^\\s\\x00-\\x08\\x0e-\\x1f,[\\]{}\\x7f-\\x84\\x86-\\x9f\\ud800-\\udfff\\ufffe\\uffff]')); const d = "\"(?:[^\"\\\\\r\n]|\\\\.)*\"|'(?:[^'\\\\\r\n]|\\\\.)*'"; function o(e, n) { n = `${(n || '').replace(/m/g, '')}m`; const r = '([:\\-,[{]\\s*(?:\\s<<prop>>[ \t]+)?)(?:<<value>>)(?=[ \t]*(?:$|,|\\]|\\}|(?:[\r\n]\\s*)?#))'.replace(/<<prop>>/g, (() => t)).replace(/<<value>>/g, (() => e)); return RegExp(r, n); }e.languages.yaml = {
		scalar: { pattern: RegExp('([\\-:]\\s*(?:\\s<<prop>>[ \t]+)?[|>])[ \t]*(?:((?:\r?\n|\r)[ \t]+)\\S[^\r\n]*(?:\\2[^\r\n]+)*)'.replace(/<<prop>>/g, (() => t))), lookbehind: !0, alias: 'string' },
		comment: /#.*/,
		key: {
			pattern: RegExp('((?:^|[:\\-,[{\r\n?])[ \t]*(?:<<prop>>[ \t]+)?)<<key>>(?=\\s*:\\s)'.replace(/<<prop>>/g, (() => t)).replace(/<<key>>/g, (() => `(?:${a}|${d})`))), lookbehind: !0, greedy: !0, alias: 'atrule',
		},
		directive: { pattern: /(^[ \t]*)%.+/m, lookbehind: !0, alias: 'important' },
		datetime: { pattern: o('\\d{4}-\\d\\d?-\\d\\d?(?:[tT]|[ \t]+)\\d\\d?:\\d{2}:\\d{2}(?:\\.\\d*)?(?:[ \t]*(?:Z|[-+]\\d\\d?(?::\\d{2})?))?|\\d{4}-\\d{2}-\\d{2}|\\d\\d?:\\d{2}(?::\\d{2}(?:\\.\\d*)?)?'), lookbehind: !0, alias: 'number' },
		boolean: { pattern: o('false|true', 'i'), lookbehind: !0, alias: 'important' },
		null: { pattern: o('null|~', 'i'), lookbehind: !0, alias: 'important' },
		string: { pattern: o(d), lookbehind: !0, greedy: !0 },
		number: { pattern: o('[+-]?(?:0x[\\da-f]+|0o[0-7]+|(?:\\d+(?:\\.\\d*)?|\\.\\d+)(?:e[+-]?\\d+)?|\\.inf|\\.nan)', 'i'), lookbehind: !0 },
		tag: r,
		important: n,
		punctuation: /---|[:[\]{}\-,|>?]|\.\.\./,
	}, e.languages.yml = e.languages.yaml;
}(Prism));
!(function () {
	if (typeof Prism !== 'undefined') {
		let n; var s; var a = ''; Prism.plugins.customClass = {
			add(s) { n = s; }, map(n) { s = typeof n === 'function' ? n : function (s) { return n[s] || s; }; }, prefix(n) { a = n || ''; }, apply: t,
		}, Prism.hooks.add('wrap', ((e) => { if (n) { const u = n({ content: e.content, type: e.type, language: e.language }); Array.isArray(u) ? e.classes.push.apply(e.classes, u) : u && e.classes.push(u); }(s || a) && (e.classes = e.classes.map(((n) => t(n, e.language)))); }));
	} function t(n, t) { return a + (s ? s(n, t) : n); }
}());
!(function () { if (typeof Prism !== 'undefined' && typeof document !== 'undefined') { const e = []; const t = {}; const n = function () {}; Prism.plugins.toolbar = {}; const a = Prism.plugins.toolbar.registerButton = function (n, a) { let r; r = typeof a === 'function' ? a : function (e) { let t; return typeof a.onClick === 'function' ? ((t = document.createElement('button')).type = 'button', t.addEventListener('click', (function () { a.onClick.call(this, e); }))) : typeof a.url === 'string' ? (t = document.createElement('a')).href = a.url : t = document.createElement('span'), a.className && t.classList.add(a.className), t.textContent = a.text, t; }, n in t ? console.warn(`There is a button with the key "${n}" registered already.`) : e.push(t[n] = r); }; const r = Prism.plugins.toolbar.hook = function (a) { const r = a.element.parentNode; if (r && /pre/i.test(r.nodeName) && !r.parentNode.classList.contains('code-toolbar')) { const o = document.createElement('div'); o.classList.add('code-toolbar'), r.parentNode.insertBefore(o, r), o.appendChild(r); const i = document.createElement('div'); i.classList.add('toolbar'); let l = e; const d = (function (e) { for (;e;) { let t = e.getAttribute('data-toolbar-order'); if (t != null) return (t = t.trim()).length ? t.split(/\s*,\s*/g) : []; e = e.parentElement; } }(a.element)); d && (l = d.map(((e) => t[e] || n))), l.forEach(((e) => { const t = e(a); if (t) { const n = document.createElement('div'); n.classList.add('toolbar-item'), n.appendChild(t), i.appendChild(n); } })), o.appendChild(i); } }; a('label', ((e) => { const t = e.element.parentNode; if (t && /pre/i.test(t.nodeName) && t.hasAttribute('data-label')) { let n; let a; const r = t.getAttribute('data-label'); try { a = document.querySelector(`template#${r}`); } catch (e) {} return a ? n = a.content : (t.hasAttribute('data-url') ? (n = document.createElement('a')).href = t.getAttribute('data-url') : n = document.createElement('span'), n.textContent = r), n; } })), Prism.hooks.add('complete', r); } }());
