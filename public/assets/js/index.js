(() => {
  var __create = Object.create;
  var __defProp = Object.defineProperty;
  var __getOwnPropDesc = Object.getOwnPropertyDescriptor;
  var __getOwnPropNames = Object.getOwnPropertyNames;
  var __getProtoOf = Object.getPrototypeOf;
  var __hasOwnProp = Object.prototype.hasOwnProperty;
  var __defNormalProp = (obj, key, value) => key in obj ? __defProp(obj, key, { enumerable: true, configurable: true, writable: true, value }) : obj[key] = value;
  var __esm = (fn, res) => function __init() {
    return fn && (res = (0, fn[__getOwnPropNames(fn)[0]])(fn = 0)), res;
  };
  var __commonJS = (cb, mod) => function __require() {
    return mod || (0, cb[__getOwnPropNames(cb)[0]])((mod = { exports: {} }).exports, mod), mod.exports;
  };
  var __export = (target, all) => {
    for (var name in all)
      __defProp(target, name, { get: all[name], enumerable: true });
  };
  var __copyProps = (to, from, except, desc) => {
    if (from && typeof from === "object" || typeof from === "function") {
      for (let key of __getOwnPropNames(from))
        if (!__hasOwnProp.call(to, key) && key !== except)
          __defProp(to, key, { get: () => from[key], enumerable: !(desc = __getOwnPropDesc(from, key)) || desc.enumerable });
    }
    return to;
  };
  var __toESM = (mod, isNodeMode, target) => (target = mod != null ? __create(__getProtoOf(mod)) : {}, __copyProps(
    // If the importer is in node compatibility mode or this is not an ESM
    // file that has been converted to a CommonJS file using a Babel-
    // compatible transform (i.e. "__esModule" has not been set), then set
    // "default" to the CommonJS "module.exports" for node compatibility.
    isNodeMode || !mod || !mod.__esModule ? __defProp(target, "default", { value: mod, enumerable: true }) : target,
    mod
  ));
  var __publicField = (obj, key, value) => __defNormalProp(obj, typeof key !== "symbol" ? key + "" : key, value);

  // develop/js/libraries/prism.js
  var require_prism = __commonJS({
    "develop/js/libraries/prism.js"(exports, module) {
      var _self = typeof window !== "undefined" ? window : typeof WorkerGlobalScope !== "undefined" && self instanceof WorkerGlobalScope ? self : {};
      var Prism2 = (function(e) {
        const n = /(?:^|\s)lang(?:uage)?-([\w-]+)(?=\s|$)/i;
        let t = 0;
        const r = {};
        var a = {
          manual: e.Prism && e.Prism.manual,
          disableWorkerMessageHandler: e.Prism && e.Prism.disableWorkerMessageHandler,
          util: {
            encode: function e2(n2) {
              return n2 instanceof i ? new i(n2.type, e2(n2.content), n2.alias) : Array.isArray(n2) ? n2.map(e2) : n2.replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/\u00a0/g, " ");
            },
            type(e2) {
              return Object.prototype.toString.call(e2).slice(8, -1);
            },
            objId(e2) {
              return e2.__id || Object.defineProperty(e2, "__id", { value: ++t }), e2.__id;
            },
            clone: function e2(n2, t2) {
              let r2;
              let i2;
              switch (t2 = t2 || {}, a.util.type(n2)) {
                case "Object":
                  if (i2 = a.util.objId(n2), t2[i2]) return t2[i2];
                  for (const l2 in r2 = {}, t2[i2] = r2, n2) n2.hasOwnProperty(l2) && (r2[l2] = e2(n2[l2], t2));
                  return r2;
                case "Array":
                  return i2 = a.util.objId(n2), t2[i2] ? t2[i2] : (r2 = [], t2[i2] = r2, n2.forEach(((n3, a2) => {
                    r2[a2] = e2(n3, t2);
                  })), r2);
                default:
                  return n2;
              }
            },
            getLanguage(e2) {
              for (; e2; ) {
                const t2 = n.exec(e2.className);
                if (t2) return t2[1].toLowerCase();
                e2 = e2.parentElement;
              }
              return "none";
            },
            setLanguage(e2, t2) {
              e2.className = e2.className.replace(RegExp(n, "gi"), ""), e2.classList.add(`language-${t2}`);
            },
            currentScript() {
              if (typeof document === "undefined") return null;
              if (document.currentScript && document.currentScript.tagName === "SCRIPT") return document.currentScript;
              try {
                throw new Error();
              } catch (r2) {
                const e2 = (/at [^(\r\n]*\((.*):[^:]+:[^:]+\)$/i.exec(r2.stack) || [])[1];
                if (e2) {
                  const n2 = document.getElementsByTagName("script");
                  for (const t2 in n2) if (n2[t2].src == e2) return n2[t2];
                }
                return null;
              }
            },
            isActive(e2, n2, t2) {
              for (let r2 = `no-${n2}`; e2; ) {
                const a2 = e2.classList;
                if (a2.contains(n2)) return true;
                if (a2.contains(r2)) return false;
                e2 = e2.parentElement;
              }
              return !!t2;
            }
          },
          languages: {
            plain: r,
            plaintext: r,
            text: r,
            txt: r,
            extend(e2, n2) {
              const t2 = a.util.clone(a.languages[e2]);
              for (const r2 in n2) t2[r2] = n2[r2];
              return t2;
            },
            insertBefore(e2, n2, t2, r2) {
              const i2 = (r2 = r2 || a.languages)[e2];
              const l2 = {};
              for (const o2 in i2) if (i2.hasOwnProperty(o2)) {
                if (o2 == n2) for (const s2 in t2) t2.hasOwnProperty(s2) && (l2[s2] = t2[s2]);
                t2.hasOwnProperty(o2) || (l2[o2] = i2[o2]);
              }
              const u2 = r2[e2];
              return r2[e2] = l2, a.languages.DFS(a.languages, (function(n3, t3) {
                t3 === u2 && n3 != e2 && (this[n3] = l2);
              })), l2;
            },
            DFS: function e2(n2, t2, r2, i2) {
              i2 = i2 || {};
              const l2 = a.util.objId;
              for (const o2 in n2) if (n2.hasOwnProperty(o2)) {
                t2.call(n2, o2, n2[o2], r2 || o2);
                const s2 = n2[o2];
                const u2 = a.util.type(s2);
                u2 !== "Object" || i2[l2(s2)] ? u2 !== "Array" || i2[l2(s2)] || (i2[l2(s2)] = true, e2(s2, t2, o2, i2)) : (i2[l2(s2)] = true, e2(s2, t2, null, i2));
              }
            }
          },
          plugins: {},
          highlightAll(e2, n2) {
            a.highlightAllUnder(document, e2, n2);
          },
          highlightAllUnder(e2, n2, t2) {
            const r2 = { callback: t2, container: e2, selector: 'code[class*="language-"], [class*="language-"] code, code[class*="lang-"], [class*="lang-"] code' };
            a.hooks.run("before-highlightall", r2), r2.elements = Array.prototype.slice.apply(r2.container.querySelectorAll(r2.selector)), a.hooks.run("before-all-elements-highlight", r2);
            for (var i2, l2 = 0; i2 = r2.elements[l2++]; ) a.highlightElement(i2, true === n2, r2.callback);
          },
          highlightElement(n2, t2, r2) {
            const i2 = a.util.getLanguage(n2);
            const l2 = a.languages[i2];
            a.util.setLanguage(n2, i2);
            let o2 = n2.parentElement;
            o2 && o2.nodeName.toLowerCase() === "pre" && a.util.setLanguage(o2, i2);
            const s2 = {
              element: n2,
              language: i2,
              grammar: l2,
              code: n2.textContent
            };
            function u2(e2) {
              s2.highlightedCode = e2, a.hooks.run("before-insert", s2), s2.element.innerHTML = s2.highlightedCode, a.hooks.run("after-highlight", s2), a.hooks.run("complete", s2), r2 && r2.call(s2.element);
            }
            if (a.hooks.run("before-sanity-check", s2), (o2 = s2.element.parentElement) && o2.nodeName.toLowerCase() === "pre" && !o2.hasAttribute("tabindex") && o2.setAttribute("tabindex", "0"), !s2.code) return a.hooks.run("complete", s2), void (r2 && r2.call(s2.element));
            if (a.hooks.run("before-highlight", s2), s2.grammar) if (t2 && e.Worker) {
              const c2 = new Worker(a.filename);
              c2.onmessage = function(e2) {
                u2(e2.data);
              }, c2.postMessage(JSON.stringify({ language: s2.language, code: s2.code, immediateClose: true }));
            } else u2(a.highlight(s2.code, s2.grammar, s2.language));
            else u2(a.util.encode(s2.code));
          },
          highlight(e2, n2, t2) {
            const r2 = { code: e2, grammar: n2, language: t2 };
            if (a.hooks.run("before-tokenize", r2), !r2.grammar) throw new Error(`The language "${r2.language}" has no grammar.`);
            return r2.tokens = a.tokenize(r2.code, r2.grammar), a.hooks.run("after-tokenize", r2), i.stringify(a.util.encode(r2.tokens), r2.language);
          },
          tokenize(e2, n2) {
            const t2 = n2.rest;
            if (t2) {
              for (const r2 in t2) n2[r2] = t2[r2];
              delete n2.rest;
            }
            const a2 = new s();
            return u(a2, a2.head, e2), o(e2, a2, n2, a2.head, 0), (function(e3) {
              for (var n3 = [], t3 = e3.head.next; t3 !== e3.tail; ) n3.push(t3.value), t3 = t3.next;
              return n3;
            })(a2);
          },
          hooks: { all: {}, add(e2, n2) {
            const t2 = a.hooks.all;
            t2[e2] = t2[e2] || [], t2[e2].push(n2);
          }, run(e2, n2) {
            const t2 = a.hooks.all[e2];
            if (t2 && t2.length) for (var r2, i2 = 0; r2 = t2[i2++]; ) r2(n2);
          } },
          Token: i
        };
        function i(e2, n2, t2, r2) {
          this.type = e2, this.content = n2, this.alias = t2, this.length = 0 | (r2 || "").length;
        }
        function l(e2, n2, t2, r2) {
          e2.lastIndex = n2;
          const a2 = e2.exec(t2);
          if (a2 && r2 && a2[1]) {
            const i2 = a2[1].length;
            a2.index += i2, a2[0] = a2[0].slice(i2);
          }
          return a2;
        }
        function o(e2, n2, t2, r2, s2, g2) {
          for (const f2 in t2) if (t2.hasOwnProperty(f2) && t2[f2]) {
            let h = t2[f2];
            h = Array.isArray(h) ? h : [h];
            for (let d = 0; d < h.length; ++d) {
              if (g2 && g2.cause == `${f2},${d}`) return;
              const v = h[d];
              const p = v.inside;
              const m = !!v.lookbehind;
              const y = !!v.greedy;
              const k = v.alias;
              if (y && !v.pattern.global) {
                const x = v.pattern.toString().match(/[imsuy]*$/)[0];
                v.pattern = RegExp(v.pattern.source, `${x}g`);
              }
              for (let b = v.pattern || v, w = r2.next, A = s2; w !== n2.tail && !(g2 && A >= g2.reach); A += w.value.length, w = w.next) {
                let P = w.value;
                if (n2.length > e2.length) return;
                if (!(P instanceof i)) {
                  var E;
                  let S = 1;
                  if (y) {
                    if (!(E = l(b, A, e2, m)) || E.index >= e2.length) break;
                    var L = E.index;
                    const O = E.index + E[0].length;
                    let C = A;
                    for (C += w.value.length; L >= C; ) C += (w = w.next).value.length;
                    if (A = C -= w.value.length, w.value instanceof i) continue;
                    for (let j = w; j !== n2.tail && (C < O || typeof j.value === "string"); j = j.next) S++, C += j.value.length;
                    S--, P = e2.slice(A, C), E.index -= A;
                  } else if (!(E = l(b, 0, P, m))) continue;
                  L = E.index;
                  const N = E[0];
                  const _ = P.slice(0, L);
                  const M = P.slice(L + N.length);
                  const W = A + P.length;
                  g2 && W > g2.reach && (g2.reach = W);
                  let I = w.prev;
                  if (_ && (I = u(n2, I, _), A += _.length), c(n2, I, S), w = u(n2, I, new i(f2, p ? a.tokenize(N, p) : N, k, N)), M && u(n2, w, M), S > 1) {
                    const T = { cause: `${f2},${d}`, reach: W };
                    o(e2, n2, t2, w.prev, A, T), g2 && T.reach > g2.reach && (g2.reach = T.reach);
                  }
                }
              }
            }
          }
        }
        function s() {
          const e2 = { value: null, prev: null, next: null };
          const n2 = { value: null, prev: e2, next: null };
          e2.next = n2, this.head = e2, this.tail = n2, this.length = 0;
        }
        function u(e2, n2, t2) {
          const r2 = n2.next;
          const a2 = { value: t2, prev: n2, next: r2 };
          return n2.next = a2, r2.prev = a2, e2.length++, a2;
        }
        function c(e2, n2, t2) {
          for (var r2 = n2.next, a2 = 0; a2 < t2 && r2 !== e2.tail; a2++) r2 = r2.next;
          n2.next = r2, r2.prev = n2, e2.length -= a2;
        }
        if (e.Prism = a, i.stringify = function e2(n2, t2) {
          if (typeof n2 === "string") return n2;
          if (Array.isArray(n2)) {
            let r2 = "";
            return n2.forEach(((n3) => {
              r2 += e2(n3, t2);
            })), r2;
          }
          const i2 = {
            type: n2.type,
            content: e2(n2.content, t2),
            tag: "span",
            classes: ["token", n2.type],
            attributes: {},
            language: t2
          };
          const l2 = n2.alias;
          l2 && (Array.isArray(l2) ? Array.prototype.push.apply(i2.classes, l2) : i2.classes.push(l2)), a.hooks.run("wrap", i2);
          let o2 = "";
          for (const s2 in i2.attributes) o2 += ` ${s2}="${(i2.attributes[s2] || "").replace(/"/g, "&quot;")}"`;
          return `<${i2.tag} class="${i2.classes.join(" ")}"${o2}>${i2.content}</${i2.tag}>`;
        }, !e.document) return e.addEventListener ? (a.disableWorkerMessageHandler || e.addEventListener("message", ((n2) => {
          const t2 = JSON.parse(n2.data);
          const r2 = t2.language;
          const i2 = t2.code;
          const l2 = t2.immediateClose;
          e.postMessage(a.highlight(i2, a.languages[r2], r2)), l2 && e.close();
        }), false), a) : a;
        const g = a.util.currentScript();
        function f() {
          a.manual || a.highlightAll();
        }
        if (g && (a.filename = g.src, g.hasAttribute("data-manual") && (a.manual = true)), !a.manual) {
          const h = document.readyState;
          h === "loading" || h === "interactive" && g && g.defer ? document.addEventListener("DOMContentLoaded", f) : window.requestAnimationFrame ? window.requestAnimationFrame(f) : window.setTimeout(f, 16);
        }
        return a;
      })(_self);
      typeof module !== "undefined" && module.exports && (module.exports = Prism2), typeof global !== "undefined" && (global.Prism = Prism2);
      Prism2.languages.markup = {
        comment: { pattern: /<!--(?:(?!<!--)[\s\S])*?-->/, greedy: true },
        prolog: { pattern: /<\?[\s\S]+?\?>/, greedy: true },
        doctype: {
          pattern: /<!DOCTYPE(?:[^>"'[\]]|"[^"]*"|'[^']*')+(?:\[(?:[^<"'\]]|"[^"]*"|'[^']*'|<(?!!--)|<!--(?:[^-]|-(?!->))*-->)*\]\s*)?>/i,
          greedy: true,
          inside: {
            "internal-subset": {
              pattern: /(^[^\[]*\[)[\s\S]+(?=\]>$)/,
              lookbehind: true,
              greedy: true,
              inside: null
            },
            string: { pattern: /"[^"]*"|'[^']*'/, greedy: true },
            punctuation: /^<!|>$|[[\]]/,
            "doctype-tag": /^DOCTYPE/i,
            name: /[^\s<>'"]+/
          }
        },
        cdata: { pattern: /<!\[CDATA\[[\s\S]*?\]\]>/i, greedy: true },
        tag: {
          pattern: /<\/?(?!\d)[^\s>\/=$<%]+(?:\s(?:\s*[^\s>\/=]+(?:\s*=\s*(?:"[^"]*"|'[^']*'|[^\s'">=]+(?=[\s>]))|(?=[\s/>])))+)?\s*\/?>/,
          greedy: true,
          inside: {
            tag: { pattern: /^<\/?[^\s>\/]+/, inside: { punctuation: /^<\/?/, namespace: /^[^\s>\/:]+:/ } },
            "special-attr": [],
            "attr-value": { pattern: /=\s*(?:"[^"]*"|'[^']*'|[^\s'">=]+)/, inside: { punctuation: [{ pattern: /^=/, alias: "attr-equals" }, { pattern: /^(\s*)["']|["']$/, lookbehind: true }] } },
            punctuation: /\/?>/,
            "attr-name": { pattern: /[^\s>\/]+/, inside: { namespace: /^[^\s>\/:]+:/ } }
          }
        },
        entity: [{ pattern: /&[\da-z]{1,8};/i, alias: "named-entity" }, /&#x?[\da-f]{1,8};/i]
      }, Prism2.languages.markup.tag.inside["attr-value"].inside.entity = Prism2.languages.markup.entity, Prism2.languages.markup.doctype.inside["internal-subset"].inside = Prism2.languages.markup, Prism2.hooks.add("wrap", ((a) => {
        a.type === "entity" && (a.attributes.title = a.content.replace(/&amp;/, "&"));
      })), Object.defineProperty(Prism2.languages.markup.tag, "addInlined", {
        value(a, e) {
          const s = {};
          s[`language-${e}`] = { pattern: /(^<!\[CDATA\[)[\s\S]+?(?=\]\]>$)/i, lookbehind: true, inside: Prism2.languages[e] }, s.cdata = /^<!\[CDATA\[|\]\]>$/i;
          const t = { "included-cdata": { pattern: /<!\[CDATA\[[\s\S]*?\]\]>/i, inside: s } };
          t[`language-${e}`] = { pattern: /[\s\S]+/, inside: Prism2.languages[e] };
          const n = {};
          n[a] = {
            pattern: RegExp("(<__[^>]*>)(?:<!\\[CDATA\\[(?:[^\\]]|\\](?!\\]>))*\\]\\]>|(?!<!\\[CDATA\\[)[^])*?(?=</__>)".replace(/__/g, (() => a)), "i"),
            lookbehind: true,
            greedy: true,
            inside: t
          }, Prism2.languages.insertBefore("markup", "cdata", n);
        }
      }), Object.defineProperty(Prism2.languages.markup.tag, "addAttribute", {
        value(a, e) {
          Prism2.languages.markup.tag.inside["special-attr"].push({
            pattern: RegExp(`(^|["'\\s])(?:${a})\\s*=\\s*(?:"[^"]*"|'[^']*'|[^\\s'">=]+(?=[\\s>]))`, "i"),
            lookbehind: true,
            inside: {
              "attr-name": /^[^\s=]+/,
              "attr-value": {
                pattern: /=[\s\S]+/,
                inside: {
                  value: {
                    pattern: /(^=\s*(["']|(?!["'])))\S[\s\S]*(?=\2$)/,
                    lookbehind: true,
                    alias: [e, `language-${e}`],
                    inside: Prism2.languages[e]
                  },
                  punctuation: [{ pattern: /^=/, alias: "attr-equals" }, /"|'/]
                }
              }
            }
          });
        }
      }), Prism2.languages.html = Prism2.languages.markup, Prism2.languages.mathml = Prism2.languages.markup, Prism2.languages.svg = Prism2.languages.markup, Prism2.languages.xml = Prism2.languages.extend("markup", {}), Prism2.languages.ssml = Prism2.languages.xml, Prism2.languages.atom = Prism2.languages.xml, Prism2.languages.rss = Prism2.languages.xml;
      !(function(s) {
        const e = /(?:"(?:\\(?:\r\n|[\s\S])|[^"\\\r\n])*"|'(?:\\(?:\r\n|[\s\S])|[^'\\\r\n])*')/;
        s.languages.css = {
          comment: /\/\*[\s\S]*?\*\//,
          atrule: { pattern: RegExp(`@[\\w-](?:[^;{\\s"']|\\s+(?!\\s)|${e.source})*?(?:;|(?=\\s*\\{))`), inside: { rule: /^@[\w-]+/, "selector-function-argument": { pattern: /(\bselector\s*\(\s*(?![\s)]))(?:[^()\s]|\s+(?![\s)])|\((?:[^()]|\([^()]*\))*\))+(?=\s*\))/, lookbehind: true, alias: "selector" }, keyword: { pattern: /(^|[^\w-])(?:and|not|only|or)(?![\w-])/, lookbehind: true } } },
          url: { pattern: RegExp(`\\burl\\((?:${e.source}|(?:[^\\\\\r
()"']|\\\\[^])*)\\)`, "i"), greedy: true, inside: { function: /^url/i, punctuation: /^\(|\)$/, string: { pattern: RegExp(`^${e.source}$`), alias: "url" } } },
          selector: { pattern: RegExp(`(^|[{}\\s])[^{}\\s](?:[^{};"'\\s]|\\s+(?![\\s{])|${e.source})*(?=\\s*\\{)`), lookbehind: true },
          string: { pattern: e, greedy: true },
          property: { pattern: /(^|[^-\w\xA0-\uFFFF])(?!\s)[-_a-z\xA0-\uFFFF](?:(?!\s)[-\w\xA0-\uFFFF])*(?=\s*:)/i, lookbehind: true },
          important: /!important\b/i,
          function: { pattern: /(^|[^-a-z0-9])[-a-z0-9]+(?=\()/i, lookbehind: true },
          punctuation: /[(){};:,]/
        }, s.languages.css.atrule.inside.rest = s.languages.css;
        const t = s.languages.markup;
        t && (t.tag.addInlined("style", "css"), t.tag.addAttribute("style", "css"));
      })(Prism2);
      Prism2.languages.clike = {
        comment: [{ pattern: /(^|[^\\])\/\*[\s\S]*?(?:\*\/|$)/, lookbehind: true, greedy: true }, { pattern: /(^|[^\\:])\/\/.*/, lookbehind: true, greedy: true }],
        string: { pattern: /(["'])(?:\\(?:\r\n|[\s\S])|(?!\1)[^\\\r\n])*\1/, greedy: true },
        "class-name": { pattern: /(\b(?:class|extends|implements|instanceof|interface|new|trait)\s+|\bcatch\s+\()[\w.\\]+/i, lookbehind: true, inside: { punctuation: /[.\\]/ } },
        keyword: /\b(?:break|catch|continue|do|else|finally|for|function|if|in|instanceof|new|null|return|throw|try|while)\b/,
        boolean: /\b(?:false|true)\b/,
        function: /\b\w+(?=\()/,
        number: /\b0x[\da-f]+\b|(?:\b\d+(?:\.\d*)?|\B\.\d+)(?:e[+-]?\d+)?/i,
        operator: /[<>]=?|[!=]=?=?|--?|\+\+?|&&?|\|\|?|[?*/~^%]/,
        punctuation: /[{}[\];(),.:]/
      };
      Prism2.languages.javascript = Prism2.languages.extend("clike", {
        "class-name": [Prism2.languages.clike["class-name"], { pattern: /(^|[^$\w\xA0-\uFFFF])(?!\s)[_$A-Z\xA0-\uFFFF](?:(?!\s)[$\w\xA0-\uFFFF])*(?=\.(?:constructor|prototype))/, lookbehind: true }],
        keyword: [{ pattern: /((?:^|\})\s*)catch\b/, lookbehind: true }, { pattern: /(^|[^.]|\.\.\.\s*)\b(?:as|assert(?=\s*\{)|async(?=\s*(?:function\b|\(|[$\w\xA0-\uFFFF]|$))|await|break|case|class|const|continue|debugger|default|delete|do|else|enum|export|extends|finally(?=\s*(?:\{|$))|for|from(?=\s*(?:['"]|$))|function|(?:get|set)(?=\s*(?:[#\[$\w\xA0-\uFFFF]|$))|if|implements|import|in|instanceof|interface|let|new|null|of|package|private|protected|public|return|static|super|switch|this|throw|try|typeof|undefined|var|void|while|with|yield)\b/, lookbehind: true }],
        function: /#?(?!\s)[_$a-zA-Z\xA0-\uFFFF](?:(?!\s)[$\w\xA0-\uFFFF])*(?=\s*(?:\.\s*(?:apply|bind|call)\s*)?\()/,
        number: { pattern: RegExp("(^|[^\\w$])(?:NaN|Infinity|0[bB][01]+(?:_[01]+)*n?|0[oO][0-7]+(?:_[0-7]+)*n?|0[xX][\\dA-Fa-f]+(?:_[\\dA-Fa-f]+)*n?|\\d+(?:_\\d+)*n|(?:\\d+(?:_\\d+)*(?:\\.(?:\\d+(?:_\\d+)*)?)?|\\.\\d+(?:_\\d+)*)(?:[Ee][+-]?\\d+(?:_\\d+)*)?)(?![\\w$])"), lookbehind: true },
        operator: /--|\+\+|\*\*=?|=>|&&=?|\|\|=?|[!=]==|<<=?|>>>?=?|[-+*/%&|^!=<>]=?|\.{3}|\?\?=?|\?\.?|[~:]/
      }), Prism2.languages.javascript["class-name"][0].pattern = /(\b(?:class|extends|implements|instanceof|interface|new)\s+)[\w.\\]+/, Prism2.languages.insertBefore("javascript", "keyword", {
        regex: {
          pattern: RegExp(`((?:^|[^$\\w\\xA0-\\uFFFF."'\\])\\s]|\\b(?:return|yield))\\s*)/(?:(?:\\[(?:[^\\]\\\\\r
]|\\\\.)*\\]|\\\\.|[^/\\\\\\[\r
])+/[dgimyus]{0,7}|(?:\\[(?:[^[\\]\\\\\r
]|\\\\.|\\[(?:[^[\\]\\\\\r
]|\\\\.|\\[(?:[^[\\]\\\\\r
]|\\\\.)*\\])*\\])*\\]|\\\\.|[^/\\\\\\[\r
])+/[dgimyus]{0,7}v[dgimyus]{0,7})(?=(?:\\s|/\\*(?:[^*]|\\*(?!/))*\\*/)*(?:$|[\r
,.;:})\\]]|//))`),
          lookbehind: true,
          greedy: true,
          inside: {
            "regex-source": {
              pattern: /^(\/)[\s\S]+(?=\/[a-z]*$)/,
              lookbehind: true,
              alias: "language-regex",
              inside: Prism2.languages.regex
            },
            "regex-delimiter": /^\/|\/$/,
            "regex-flags": /^[a-z]+$/
          }
        },
        "function-variable": { pattern: /#?(?!\s)[_$a-zA-Z\xA0-\uFFFF](?:(?!\s)[$\w\xA0-\uFFFF])*(?=\s*[=:]\s*(?:async\s*)?(?:\bfunction\b|(?:\((?:[^()]|\([^()]*\))*\)|(?!\s)[_$a-zA-Z\xA0-\uFFFF](?:(?!\s)[$\w\xA0-\uFFFF])*)\s*=>))/, alias: "function" },
        parameter: [{ pattern: /(function(?:\s+(?!\s)[_$a-zA-Z\xA0-\uFFFF](?:(?!\s)[$\w\xA0-\uFFFF])*)?\s*\(\s*)(?!\s)(?:[^()\s]|\s+(?![\s)])|\([^()]*\))+(?=\s*\))/, lookbehind: true, inside: Prism2.languages.javascript }, { pattern: /(^|[^$\w\xA0-\uFFFF])(?!\s)[_$a-z\xA0-\uFFFF](?:(?!\s)[$\w\xA0-\uFFFF])*(?=\s*=>)/i, lookbehind: true, inside: Prism2.languages.javascript }, { pattern: /(\(\s*)(?!\s)(?:[^()\s]|\s+(?![\s)])|\([^()]*\))+(?=\s*\)\s*=>)/, lookbehind: true, inside: Prism2.languages.javascript }, { pattern: /((?:\b|\s|^)(?!(?:as|async|await|break|case|catch|class|const|continue|debugger|default|delete|do|else|enum|export|extends|finally|for|from|function|get|if|implements|import|in|instanceof|interface|let|new|null|of|package|private|protected|public|return|set|static|super|switch|this|throw|try|typeof|undefined|var|void|while|with|yield)(?![$\w\xA0-\uFFFF]))(?:(?!\s)[_$a-zA-Z\xA0-\uFFFF](?:(?!\s)[$\w\xA0-\uFFFF])*\s*)\(\s*|\]\s*\(\s*)(?!\s)(?:[^()\s]|\s+(?![\s)])|\([^()]*\))+(?=\s*\)\s*\{)/, lookbehind: true, inside: Prism2.languages.javascript }],
        constant: /\b[A-Z](?:[A-Z_]|\dx?)*\b/
      }), Prism2.languages.insertBefore("javascript", "string", {
        hashbang: { pattern: /^#!.*/, greedy: true, alias: "comment" },
        "template-string": { pattern: /`(?:\\[\s\S]|\$\{(?:[^{}]|\{(?:[^{}]|\{[^}]*\})*\})+\}|(?!\$\{)[^\\`])*`/, greedy: true, inside: { "template-punctuation": { pattern: /^`|`$/, alias: "string" }, interpolation: { pattern: /((?:^|[^\\])(?:\\{2})*)\$\{(?:[^{}]|\{(?:[^{}]|\{[^}]*\})*\})+\}/, lookbehind: true, inside: { "interpolation-punctuation": { pattern: /^\$\{|\}$/, alias: "punctuation" }, rest: Prism2.languages.javascript } }, string: /[\s\S]+/ } },
        "string-property": {
          pattern: /((?:^|[,{])[ \t]*)(["'])(?:\\(?:\r\n|[\s\S])|(?!\2)[^\\\r\n])*\2(?=\s*:)/m,
          lookbehind: true,
          greedy: true,
          alias: "property"
        }
      }), Prism2.languages.insertBefore("javascript", "operator", { "literal-property": { pattern: /((?:^|[,{])[ \t]*)(?!\s)[_$a-zA-Z\xA0-\uFFFF](?:(?!\s)[$\w\xA0-\uFFFF])*(?=\s*:)/m, lookbehind: true, alias: "property" } }), Prism2.languages.markup && (Prism2.languages.markup.tag.addInlined("script", "javascript"), Prism2.languages.markup.tag.addAttribute("on(?:abort|blur|change|click|composition(?:end|start|update)|dblclick|error|focus(?:in|out)?|key(?:down|up)|load|mouse(?:down|enter|leave|move|out|over|up)|reset|resize|scroll|select|slotchange|submit|unload|wheel)", "javascript")), Prism2.languages.js = Prism2.languages.javascript;
      !(function(e) {
        const t = "\\b(?:BASH|BASHOPTS|BASH_ALIASES|BASH_ARGC|BASH_ARGV|BASH_CMDS|BASH_COMPLETION_COMPAT_DIR|BASH_LINENO|BASH_REMATCH|BASH_SOURCE|BASH_VERSINFO|BASH_VERSION|COLORTERM|COLUMNS|COMP_WORDBREAKS|DBUS_SESSION_BUS_ADDRESS|DEFAULTS_PATH|DESKTOP_SESSION|DIRSTACK|DISPLAY|EUID|GDMSESSION|GDM_LANG|GNOME_KEYRING_CONTROL|GNOME_KEYRING_PID|GPG_AGENT_INFO|GROUPS|HISTCONTROL|HISTFILE|HISTFILESIZE|HISTSIZE|HOME|HOSTNAME|HOSTTYPE|IFS|INSTANCE|JOB|LANG|LANGUAGE|LC_ADDRESS|LC_ALL|LC_IDENTIFICATION|LC_MEASUREMENT|LC_MONETARY|LC_NAME|LC_NUMERIC|LC_PAPER|LC_TELEPHONE|LC_TIME|LESSCLOSE|LESSOPEN|LINES|LOGNAME|LS_COLORS|MACHTYPE|MAILCHECK|MANDATORY_PATH|NO_AT_BRIDGE|OLDPWD|OPTERR|OPTIND|ORBIT_SOCKETDIR|OSTYPE|PAPERSIZE|PATH|PIPESTATUS|PPID|PS1|PS2|PS3|PS4|PWD|RANDOM|REPLY|SECONDS|SELINUX_INIT|SESSION|SESSIONTYPE|SESSION_MANAGER|SHELL|SHELLOPTS|SHLVL|SSH_AUTH_SOCK|TERM|UID|UPSTART_EVENTS|UPSTART_INSTANCE|UPSTART_JOB|UPSTART_SESSION|USER|WINDOWID|XAUTHORITY|XDG_CONFIG_DIRS|XDG_CURRENT_DESKTOP|XDG_DATA_DIRS|XDG_GREETER_DATA_DIR|XDG_MENU_PREFIX|XDG_RUNTIME_DIR|XDG_SEAT|XDG_SEAT_PATH|XDG_SESSION_DESKTOP|XDG_SESSION_ID|XDG_SESSION_PATH|XDG_SESSION_TYPE|XDG_VTNR|XMODIFIERS)\\b";
        const a = {
          pattern: /(^(["']?)\w+\2)[ \t]+\S.*/,
          lookbehind: true,
          alias: "punctuation",
          inside: null
        };
        const n = {
          bash: a,
          environment: { pattern: RegExp(`\\$${t}`), alias: "constant" },
          variable: [{
            pattern: /\$?\(\([\s\S]+?\)\)/,
            greedy: true,
            inside: {
              variable: [{ pattern: /(^\$\(\([\s\S]+)\)\)/, lookbehind: true }, /^\$\(\(/],
              number: /\b0x[\dA-Fa-f]+\b|(?:\b\d+(?:\.\d*)?|\B\.\d+)(?:[Ee]-?\d+)?/,
              operator: /--|\+\+|\*\*=?|<<=?|>>=?|&&|\|\||[=!+\-*/%<>^&|]=?|[?~:]/,
              punctuation: /\(\(?|\)\)?|,|;/
            }
          }, { pattern: /\$\((?:\([^)]+\)|[^()])+\)|`[^`]+`/, greedy: true, inside: { variable: /^\$\(|^`|\)$|`$/ } }, { pattern: /\$\{[^}]+\}/, greedy: true, inside: { operator: /:[-=?+]?|[!\/]|##?|%%?|\^\^?|,,?/, punctuation: /[\[\]]/, environment: { pattern: RegExp(`(\\{)${t}`), lookbehind: true, alias: "constant" } } }, /\$(?:\w+|[#?*!@$])/],
          entity: /\\(?:[abceEfnrtv\\"]|O?[0-7]{1,3}|U[0-9a-fA-F]{8}|u[0-9a-fA-F]{4}|x[0-9a-fA-F]{1,2})/
        };
        e.languages.bash = {
          shebang: { pattern: /^#!\s*\/.*/, alias: "important" },
          comment: { pattern: /(^|[^"{\\$])#.*/, lookbehind: true },
          "function-name": [{ pattern: /(\bfunction\s+)[\w-]+(?=(?:\s*\(?:\s*\))?\s*\{)/, lookbehind: true, alias: "function" }, { pattern: /\b[\w-]+(?=\s*\(\s*\)\s*\{)/, alias: "function" }],
          "for-or-select": { pattern: /(\b(?:for|select)\s+)\w+(?=\s+in\s)/, alias: "variable", lookbehind: true },
          "assign-left": {
            pattern: /(^|[\s;|&]|[<>]\()\w+(?:\.\w+)*(?=\+?=)/,
            inside: { environment: { pattern: RegExp(`(^|[\\s;|&]|[<>]\\()${t}`), lookbehind: true, alias: "constant" } },
            alias: "variable",
            lookbehind: true
          },
          parameter: { pattern: /(^|\s)-{1,2}(?:\w+:[+-]?)?\w+(?:\.\w+)*(?=[=\s]|$)/, alias: "variable", lookbehind: true },
          string: [{
            pattern: /((?:^|[^<])<<-?\s*)(\w+)\s[\s\S]*?(?:\r?\n|\r)\2/,
            lookbehind: true,
            greedy: true,
            inside: n
          }, {
            pattern: /((?:^|[^<])<<-?\s*)(["'])(\w+)\2\s[\s\S]*?(?:\r?\n|\r)\3/,
            lookbehind: true,
            greedy: true,
            inside: { bash: a }
          }, {
            pattern: /(^|[^\\](?:\\\\)*)"(?:\\[\s\S]|\$\([^)]+\)|\$(?!\()|`[^`]+`|[^"\\`$])*"/,
            lookbehind: true,
            greedy: true,
            inside: n
          }, { pattern: /(^|[^$\\])'[^']*'/, lookbehind: true, greedy: true }, { pattern: /\$'(?:[^'\\]|\\[\s\S])*'/, greedy: true, inside: { entity: n.entity } }],
          environment: { pattern: RegExp(`\\$?${t}`), alias: "constant" },
          variable: n.variable,
          function: { pattern: /(^|[\s;|&]|[<>]\()(?:add|apropos|apt|apt-cache|apt-get|aptitude|aspell|automysqlbackup|awk|basename|bash|bc|bconsole|bg|bzip2|cal|cargo|cat|cfdisk|chgrp|chkconfig|chmod|chown|chroot|cksum|clear|cmp|column|comm|composer|cp|cron|crontab|csplit|curl|cut|date|dc|dd|ddrescue|debootstrap|df|diff|diff3|dig|dir|dircolors|dirname|dirs|dmesg|docker|docker-compose|du|egrep|eject|env|ethtool|expand|expect|expr|fdformat|fdisk|fg|fgrep|file|find|fmt|fold|format|free|fsck|ftp|fuser|gawk|git|gparted|grep|groupadd|groupdel|groupmod|groups|grub-mkconfig|gzip|halt|head|hg|history|host|hostname|htop|iconv|id|ifconfig|ifdown|ifup|import|install|ip|java|jobs|join|kill|killall|less|link|ln|locate|logname|logrotate|look|lpc|lpr|lprint|lprintd|lprintq|lprm|ls|lsof|lynx|make|man|mc|mdadm|mkconfig|mkdir|mke2fs|mkfifo|mkfs|mkisofs|mknod|mkswap|mmv|more|most|mount|mtools|mtr|mutt|mv|nano|nc|netstat|nice|nl|node|nohup|notify-send|npm|nslookup|op|open|parted|passwd|paste|pathchk|ping|pkill|pnpm|podman|podman-compose|popd|pr|printcap|printenv|ps|pushd|pv|quota|quotacheck|quotactl|ram|rar|rcp|reboot|remsync|rename|renice|rev|rm|rmdir|rpm|rsync|scp|screen|sdiff|sed|sendmail|seq|service|sftp|sh|shellcheck|shuf|shutdown|sleep|slocate|sort|split|ssh|stat|strace|su|sudo|sum|suspend|swapon|sync|sysctl|tac|tail|tar|tee|time|timeout|top|touch|tr|traceroute|tsort|tty|umount|uname|unexpand|uniq|units|unrar|unshar|unzip|update-grub|uptime|useradd|userdel|usermod|users|uudecode|uuencode|v|vcpkg|vdir|vi|vim|virsh|vmstat|wait|watch|wc|wget|whereis|which|who|whoami|write|xargs|xdg-open|yarn|yes|zenity|zip|zsh|zypper)(?=$|[)\s;|&])/, lookbehind: true },
          keyword: { pattern: /(^|[\s;|&]|[<>]\()(?:case|do|done|elif|else|esac|fi|for|function|if|in|select|then|until|while)(?=$|[)\s;|&])/, lookbehind: true },
          builtin: { pattern: /(^|[\s;|&]|[<>]\()(?:\.|:|alias|bind|break|builtin|caller|cd|command|continue|declare|echo|enable|eval|exec|exit|export|getopts|hash|help|let|local|logout|mapfile|printf|pwd|read|readarray|readonly|return|set|shift|shopt|source|test|times|trap|type|typeset|ulimit|umask|unalias|unset)(?=$|[)\s;|&])/, lookbehind: true, alias: "class-name" },
          boolean: { pattern: /(^|[\s;|&]|[<>]\()(?:false|true)(?=$|[)\s;|&])/, lookbehind: true },
          "file-descriptor": { pattern: /\B&\d\b/, alias: "important" },
          operator: { pattern: /\d?<>|>\||\+=|=[=~]?|!=?|<<[<-]?|[&\d]?>>|\d[<>]&?|[<>][&=]?|&[>&]?|\|[&|]?/, inside: { "file-descriptor": { pattern: /^\d/, alias: "important" } } },
          punctuation: /\$?\(\(?|\)\)?|\.\.|[{}[\];\\]/,
          number: { pattern: /(^|\s)(?:[1-9]\d*|0)(?:[.,]\d+)?\b/, lookbehind: true }
        }, a.inside = e.languages.bash;
        for (let s = ["comment", "function-name", "for-or-select", "assign-left", "parameter", "string", "environment", "function", "keyword", "builtin", "boolean", "file-descriptor", "operator", "punctuation", "number"], o = n.variable[1].inside, i = 0; i < s.length; i++) o[s[i]] = e.languages.bash[s[i]];
        e.languages.sh = e.languages.bash, e.languages.shell = e.languages.bash;
      })(Prism2);
      !(function(e) {
        function n(e2, n2) {
          return `___${e2.toUpperCase()}${n2}___`;
        }
        Object.defineProperties(e.languages["markup-templating"] = {}, { buildPlaceholders: { value(t, a, r, o) {
          if (t.language === a) {
            const c = t.tokenStack = [];
            t.code = t.code.replace(r, ((e2) => {
              if (typeof o === "function" && !o(e2)) return e2;
              for (var r2, i = c.length; t.code.indexOf(r2 = n(a, i)) !== -1; ) ++i;
              return c[i] = e2, r2;
            })), t.grammar = e.languages.markup;
          }
        } }, tokenizePlaceholders: { value(t, a) {
          if (t.language === a && t.tokenStack) {
            t.grammar = e.languages[a];
            let r = 0;
            const o = Object.keys(t.tokenStack);
            !(function c(i) {
              for (let u = 0; u < i.length && !(r >= o.length); u++) {
                const g = i[u];
                if (typeof g === "string" || g.content && typeof g.content === "string") {
                  const l = o[r];
                  const s = t.tokenStack[l];
                  const f = typeof g === "string" ? g : g.content;
                  const p = n(a, l);
                  const k = f.indexOf(p);
                  if (k > -1) {
                    ++r;
                    const m = f.substring(0, k);
                    const d = new e.Token(a, e.tokenize(s, t.grammar), `language-${a}`, s);
                    const h = f.substring(k + p.length);
                    const v = [];
                    m && v.push.apply(v, c([m])), v.push(d), h && v.push.apply(v, c([h])), typeof g === "string" ? i.splice.apply(i, [u, 1].concat(v)) : g.content = v;
                  }
                } else g.content && c(g.content);
              }
              return i;
            })(t.tokens);
          }
        } } });
      })(Prism2);
      !(function(e) {
        const a = /\/\*[\s\S]*?\*\/|\/\/.*|#(?!\[).*/;
        const t = [{ pattern: /\b(?:false|true)\b/i, alias: "boolean" }, { pattern: /(::\s*)\b[a-z_]\w*\b(?!\s*\()/i, greedy: true, lookbehind: true }, { pattern: /(\b(?:case|const)\s+)\b[a-z_]\w*(?=\s*[;=])/i, greedy: true, lookbehind: true }, /\b(?:null)\b/i, /\b[A-Z_][A-Z0-9_]*\b(?!\s*\()/];
        const i = /\b0b[01]+(?:_[01]+)*\b|\b0o[0-7]+(?:_[0-7]+)*\b|\b0x[\da-f]+(?:_[\da-f]+)*\b|(?:\b\d+(?:_\d+)*\.?(?:\d+(?:_\d+)*)?|\B\.\d+)(?:e[+-]?\d+)?/i;
        const n = /<?=>|\?\?=?|\.{3}|\??->|[!=]=?=?|::|\*\*=?|--|\+\+|&&|\|\||<<|>>|[?~]|[/^|%*&<>.+-]=?/;
        const s = /[{}\[\](),:;]/;
        e.languages.php = {
          delimiter: { pattern: /\?>$|^<\?(?:php(?=\s)|=)?/i, alias: "important" },
          comment: a,
          variable: /\$+(?:\w+\b|(?=\{))/,
          package: { pattern: /(namespace\s+|use\s+(?:function\s+)?)(?:\\?\b[a-z_]\w*)+\b(?!\\)/i, lookbehind: true, inside: { punctuation: /\\/ } },
          "class-name-definition": { pattern: /(\b(?:class|enum|interface|trait)\s+)\b[a-z_]\w*(?!\\)\b/i, lookbehind: true, alias: "class-name" },
          "function-definition": { pattern: /(\bfunction\s+)[a-z_]\w*(?=\s*\()/i, lookbehind: true, alias: "function" },
          keyword: [{
            pattern: /(\(\s*)\b(?:array|bool|boolean|float|int|integer|object|string)\b(?=\s*\))/i,
            alias: "type-casting",
            greedy: true,
            lookbehind: true
          }, {
            pattern: /([(,?]\s*)\b(?:array(?!\s*\()|bool|callable|(?:false|null)(?=\s*\|)|float|int|iterable|mixed|object|self|static|string)\b(?=\s*\$)/i,
            alias: "type-hint",
            greedy: true,
            lookbehind: true
          }, {
            pattern: /(\)\s*:\s*(?:\?\s*)?)\b(?:array(?!\s*\()|bool|callable|(?:false|null)(?=\s*\|)|float|int|iterable|mixed|never|object|self|static|string|void)\b/i,
            alias: "return-type",
            greedy: true,
            lookbehind: true
          }, { pattern: /\b(?:array(?!\s*\()|bool|float|int|iterable|mixed|object|string|void)\b/i, alias: "type-declaration", greedy: true }, {
            pattern: /(\|\s*)(?:false|null)\b|\b(?:false|null)(?=\s*\|)/i,
            alias: "type-declaration",
            greedy: true,
            lookbehind: true
          }, { pattern: /\b(?:parent|self|static)(?=\s*::)/i, alias: "static-context", greedy: true }, { pattern: /(\byield\s+)from\b/i, lookbehind: true }, /\bclass\b/i, { pattern: /((?:^|[^\s>:]|(?:^|[^-])>|(?:^|[^:]):)\s*)\b(?:abstract|and|array|as|break|callable|case|catch|clone|const|continue|declare|default|die|do|echo|else|elseif|empty|enddeclare|endfor|endforeach|endif|endswitch|endwhile|enum|eval|exit|extends|final|finally|fn|for|foreach|function|global|goto|if|implements|include|include_once|instanceof|insteadof|interface|isset|list|match|namespace|never|new|or|parent|print|private|protected|public|readonly|require|require_once|return|self|static|switch|throw|trait|try|unset|use|var|while|xor|yield|__halt_compiler)\b/i, lookbehind: true }],
          "argument-name": { pattern: /([(,]\s*)\b[a-z_]\w*(?=\s*:(?!:))/i, lookbehind: true },
          "class-name": [{ pattern: /(\b(?:extends|implements|instanceof|new(?!\s+self|\s+static))\s+|\bcatch\s*\()\b[a-z_]\w*(?!\\)\b/i, greedy: true, lookbehind: true }, { pattern: /(\|\s*)\b[a-z_]\w*(?!\\)\b/i, greedy: true, lookbehind: true }, { pattern: /\b[a-z_]\w*(?!\\)\b(?=\s*\|)/i, greedy: true }, {
            pattern: /(\|\s*)(?:\\?\b[a-z_]\w*)+\b/i,
            alias: "class-name-fully-qualified",
            greedy: true,
            lookbehind: true,
            inside: { punctuation: /\\/ }
          }, {
            pattern: /(?:\\?\b[a-z_]\w*)+\b(?=\s*\|)/i,
            alias: "class-name-fully-qualified",
            greedy: true,
            inside: { punctuation: /\\/ }
          }, {
            pattern: /(\b(?:extends|implements|instanceof|new(?!\s+self\b|\s+static\b))\s+|\bcatch\s*\()(?:\\?\b[a-z_]\w*)+\b(?!\\)/i,
            alias: "class-name-fully-qualified",
            greedy: true,
            lookbehind: true,
            inside: { punctuation: /\\/ }
          }, { pattern: /\b[a-z_]\w*(?=\s*\$)/i, alias: "type-declaration", greedy: true }, {
            pattern: /(?:\\?\b[a-z_]\w*)+(?=\s*\$)/i,
            alias: ["class-name-fully-qualified", "type-declaration"],
            greedy: true,
            inside: { punctuation: /\\/ }
          }, { pattern: /\b[a-z_]\w*(?=\s*::)/i, alias: "static-context", greedy: true }, {
            pattern: /(?:\\?\b[a-z_]\w*)+(?=\s*::)/i,
            alias: ["class-name-fully-qualified", "static-context"],
            greedy: true,
            inside: { punctuation: /\\/ }
          }, {
            pattern: /([(,?]\s*)[a-z_]\w*(?=\s*\$)/i,
            alias: "type-hint",
            greedy: true,
            lookbehind: true
          }, {
            pattern: /([(,?]\s*)(?:\\?\b[a-z_]\w*)+(?=\s*\$)/i,
            alias: ["class-name-fully-qualified", "type-hint"],
            greedy: true,
            lookbehind: true,
            inside: { punctuation: /\\/ }
          }, {
            pattern: /(\)\s*:\s*(?:\?\s*)?)\b[a-z_]\w*(?!\\)\b/i,
            alias: "return-type",
            greedy: true,
            lookbehind: true
          }, {
            pattern: /(\)\s*:\s*(?:\?\s*)?)(?:\\?\b[a-z_]\w*)+\b(?!\\)/i,
            alias: ["class-name-fully-qualified", "return-type"],
            greedy: true,
            lookbehind: true,
            inside: { punctuation: /\\/ }
          }],
          constant: t,
          function: { pattern: /(^|[^\\\w])\\?[a-z_](?:[\w\\]*\w)?(?=\s*\()/i, lookbehind: true, inside: { punctuation: /\\/ } },
          property: { pattern: /(->\s*)\w+/, lookbehind: true },
          number: i,
          operator: n,
          punctuation: s
        };
        const l = { pattern: /\{\$(?:\{(?:\{[^{}]+\}|[^{}]+)\}|[^{}])+\}|(^|[^\\{])\$+(?:\w+(?:\[[^\r\n\[\]]+\]|->\w+)?)/, lookbehind: true, inside: e.languages.php };
        const r = [{
          pattern: /<<<'([^']+)'[\r\n](?:.*[\r\n])*?\1;/,
          alias: "nowdoc-string",
          greedy: true,
          inside: { delimiter: { pattern: /^<<<'[^']+'|[a-z_]\w*;$/i, alias: "symbol", inside: { punctuation: /^<<<'?|[';]$/ } } }
        }, {
          pattern: /<<<(?:"([^"]+)"[\r\n](?:.*[\r\n])*?\1;|([a-z_]\w*)[\r\n](?:.*[\r\n])*?\2;)/i,
          alias: "heredoc-string",
          greedy: true,
          inside: { delimiter: { pattern: /^<<<(?:"[^"]+"|[a-z_]\w*)|[a-z_]\w*;$/i, alias: "symbol", inside: { punctuation: /^<<<"?|[";]$/ } }, interpolation: l }
        }, { pattern: /`(?:\\[\s\S]|[^\\`])*`/, alias: "backtick-quoted-string", greedy: true }, { pattern: /'(?:\\[\s\S]|[^\\'])*'/, alias: "single-quoted-string", greedy: true }, {
          pattern: /"(?:\\[\s\S]|[^\\"])*"/,
          alias: "double-quoted-string",
          greedy: true,
          inside: { interpolation: l }
        }];
        e.languages.insertBefore("php", "variable", {
          string: r,
          attribute: {
            pattern: /#\[(?:[^"'\/#]|\/(?![*/])|\/\/.*$|#(?!\[).*$|\/\*(?:[^*]|\*(?!\/))*\*\/|"(?:\\[\s\S]|[^\\"])*"|'(?:\\[\s\S]|[^\\'])*')+\](?=\s*[a-z$#])/im,
            greedy: true,
            inside: {
              "attribute-content": {
                pattern: /^(#\[)[\s\S]+(?=\]$)/,
                lookbehind: true,
                inside: {
                  comment: a,
                  string: r,
                  "attribute-class-name": [{
                    pattern: /([^:]|^)\b[a-z_]\w*(?!\\)\b/i,
                    alias: "class-name",
                    greedy: true,
                    lookbehind: true
                  }, {
                    pattern: /([^:]|^)(?:\\?\b[a-z_]\w*)+/i,
                    alias: ["class-name", "class-name-fully-qualified"],
                    greedy: true,
                    lookbehind: true,
                    inside: { punctuation: /\\/ }
                  }],
                  constant: t,
                  number: i,
                  operator: n,
                  punctuation: s
                }
              },
              delimiter: { pattern: /^#\[|\]$/, alias: "punctuation" }
            }
          }
        }), e.hooks.add("before-tokenize", ((a2) => {
          /<\?/.test(a2.code) && e.languages["markup-templating"].buildPlaceholders(a2, "php", /<\?(?:[^"'/#]|\/(?![*/])|("|')(?:\\[\s\S]|(?!\1)[^\\])*\1|(?:\/\/|#(?!\[))(?:[^?\n\r]|\?(?!>))*(?=$|\?>|[\r\n])|#\[|\/\*(?:[^*]|\*(?!\/))*(?:\*\/|$))*?(?:\?>|$)/g);
        })), e.hooks.add("after-tokenize", ((a2) => {
          e.languages["markup-templating"].tokenizePlaceholders(a2, "php");
        }));
      })(Prism2);
      !(function(a) {
        const e = a.languages.javadoclike = { parameter: { pattern: /(^[\t ]*(?:\/{3}|\*|\/\*\*)\s*@(?:arg|arguments|param)\s+)\w+/m, lookbehind: true }, keyword: { pattern: /(^[\t ]*(?:\/{3}|\*|\/\*\*)\s*|\{)@[a-z][a-zA-Z-]+\b/m, lookbehind: true }, punctuation: /[{}]/ };
        Object.defineProperty(e, "addSupport", { value(e2, n) {
          typeof e2 === "string" && (e2 = [e2]), e2.forEach(((e3) => {
            !(function(e4, n2) {
              const t = "doc-comment";
              let r = a.languages[e4];
              if (r) {
                let o = r[t];
                if (o || (o = (r = a.languages.insertBefore(e4, "comment", { "doc-comment": { pattern: /(^|[^\\])\/\*\*[^/][\s\S]*?(?:\*\/|$)/, lookbehind: true, alias: "comment" } }))[t]), o instanceof RegExp && (o = r[t] = { pattern: o }), Array.isArray(o)) for (let i = 0, s = o.length; i < s; i++) o[i] instanceof RegExp && (o[i] = { pattern: o[i] }), n2(o[i]);
                else n2(o);
              }
            })(e3, ((a2) => {
              a2.inside || (a2.inside = {}), a2.inside.rest = n;
            }));
          }));
        } }), e.addSupport(["java", "javascript", "php"], e);
      })(Prism2);
      !(function(e) {
        e.languages.typescript = e.languages.extend("javascript", {
          "class-name": {
            pattern: /(\b(?:class|extends|implements|instanceof|interface|new|type)\s+)(?!keyof\b)(?!\s)[_$a-zA-Z\xA0-\uFFFF](?:(?!\s)[$\w\xA0-\uFFFF])*(?:\s*<(?:[^<>]|<(?:[^<>]|<[^<>]*>)*>)*>)?/,
            lookbehind: true,
            greedy: true,
            inside: null
          },
          builtin: /\b(?:Array|Function|Promise|any|boolean|console|never|number|string|symbol|unknown)\b/
        }), e.languages.typescript.keyword.push(/\b(?:abstract|declare|is|keyof|readonly|require)\b/, /\b(?:asserts|infer|interface|module|namespace|type)\b(?=\s*(?:[{_$a-zA-Z\xA0-\uFFFF]|$))/, /\btype\b(?=\s*(?:[\{*]|$))/), delete e.languages.typescript.parameter, delete e.languages.typescript["literal-property"];
        const s = e.languages.extend("typescript", {});
        delete s["class-name"], e.languages.typescript["class-name"].inside = s, e.languages.insertBefore("typescript", "function", { decorator: { pattern: /@[$\w\xA0-\uFFFF]+/, inside: { at: { pattern: /^@/, alias: "operator" }, function: /^[\s\S]+/ } }, "generic-function": { pattern: /#?(?!\s)[_$a-zA-Z\xA0-\uFFFF](?:(?!\s)[$\w\xA0-\uFFFF])*\s*<(?:[^<>]|<(?:[^<>]|<[^<>]*>)*>)*>(?=\s*\()/, greedy: true, inside: { function: /^#?(?!\s)[_$a-zA-Z\xA0-\uFFFF](?:(?!\s)[$\w\xA0-\uFFFF])*/, generic: { pattern: /<[\s\S]+/, alias: "class-name", inside: s } } } }), e.languages.ts = e.languages.typescript;
      })(Prism2);
      !(function(e) {
        const a = e.languages.javascript;
        const n = "\\{(?:[^{}]|\\{(?:[^{}]|\\{[^{}]*\\})*\\})+\\}";
        const t = `(@(?:arg|argument|param|property)\\s+(?:${n}\\s+)?)`;
        e.languages.jsdoc = e.languages.extend("javadoclike", { parameter: { pattern: RegExp(`${t}(?:(?!\\s)[$\\w\\xA0-\\uFFFF.])+(?=\\s|$)`), lookbehind: true, inside: { punctuation: /\./ } } }), e.languages.insertBefore("jsdoc", "keyword", {
          "optional-parameter": {
            pattern: RegExp(`${t}\\[(?:(?!\\s)[$\\w\\xA0-\\uFFFF.])+(?:=[^[\\]]+)?\\](?=\\s|$)`),
            lookbehind: true,
            inside: {
              parameter: { pattern: /(^\[)[$\w\xA0-\uFFFF\.]+/, lookbehind: true, inside: { punctuation: /\./ } },
              code: {
                pattern: /(=)[\s\S]*(?=\]$)/,
                lookbehind: true,
                inside: a,
                alias: "language-javascript"
              },
              punctuation: /[=[\]]/
            }
          },
          "class-name": [{ pattern: RegExp("(@(?:augments|class|extends|interface|memberof!?|template|this|typedef)\\s+(?:<TYPE>\\s+)?)[A-Z]\\w*(?:\\.[A-Z]\\w*)*".replace(/<TYPE>/g, (() => n))), lookbehind: true, inside: { punctuation: /\./ } }, {
            pattern: RegExp(`(@[a-z]+\\s+)${n}`),
            lookbehind: true,
            inside: {
              string: a.string,
              number: a.number,
              boolean: a.boolean,
              keyword: e.languages.typescript.keyword,
              operator: /=>|\.\.\.|[&|?:*]/,
              punctuation: /[.,;=<>{}()[\]]/
            }
          }],
          example: {
            pattern: /(@example\s+(?!\s))(?:[^@\s]|\s+(?!\s))+?(?=\s*(?:\*\s*)?(?:@\w|\*\/))/,
            lookbehind: true,
            inside: {
              code: {
                pattern: /^([\t ]*(?:\*\s*)?)\S.*$/m,
                lookbehind: true,
                inside: a,
                alias: "language-javascript"
              }
            }
          }
        }), e.languages.javadoclike.addSupport("javascript", e.languages.jsdoc);
      })(Prism2);
      Prism2.languages.json = {
        property: { pattern: /(^|[^\\])"(?:\\.|[^\\"\r\n])*"(?=\s*:)/, lookbehind: true, greedy: true },
        string: { pattern: /(^|[^\\])"(?:\\.|[^\\"\r\n])*"(?!\s*:)/, lookbehind: true, greedy: true },
        comment: { pattern: /\/\/.*|\/\*[\s\S]*?(?:\*\/|$)/, greedy: true },
        number: /-?\b\d+(?:\.\d+)?(?:e[+-]?\d+)?\b/i,
        punctuation: /[{}[\],]/,
        operator: /:/,
        boolean: /\b(?:false|true)\b/,
        null: { pattern: /\bnull\b/, alias: "keyword" }
      }, Prism2.languages.webmanifest = Prism2.languages.json;
      !(function(n) {
        function e(n2) {
          return n2 = n2.replace(/<inner>/g, (() => "(?:\\\\.|[^\\\\\n\r]|(?:\n|\r\n?)(?![\r\n]))")), RegExp(`((?:^|[^\\\\])(?:\\\\{2})*)(?:${n2})`);
        }
        const t = "(?:\\\\.|``(?:[^`\r\n]|`(?!`))+``|`[^`\r\n]+`|[^\\\\|\r\n`])+";
        const a = "\\|?__(?:\\|__)+\\|?(?:(?:\n|\r\n?)|(?![^]))".replace(/__/g, (() => t));
        const i = "\\|?[ 	]*:?-{3,}:?[ 	]*(?:\\|[ 	]*:?-{3,}:?[ 	]*)+\\|?(?:\n|\r\n?)";
        n.languages.markdown = n.languages.extend("markup", {}), n.languages.insertBefore("markdown", "prolog", {
          "front-matter-block": {
            pattern: /(^(?:\s*[\r\n])?)---(?!.)[\s\S]*?[\r\n]---(?!.)/,
            lookbehind: true,
            greedy: true,
            inside: { punctuation: /^---|---$/, "front-matter": { pattern: /\S+(?:\s+\S+)*/, alias: ["yaml", "language-yaml"], inside: n.languages.yaml } }
          },
          blockquote: { pattern: /^>(?:[\t ]*>)*/m, alias: "punctuation" },
          table: { pattern: RegExp(`^${a}${i}(?:${a})*`, "m"), inside: { "table-data-rows": { pattern: RegExp(`^(${a}${i})(?:${a})*$`), lookbehind: true, inside: { "table-data": { pattern: RegExp(t), inside: n.languages.markdown }, punctuation: /\|/ } }, "table-line": { pattern: RegExp(`^(${a})${i}$`), lookbehind: true, inside: { punctuation: /\||:?-{3,}:?/ } }, "table-header-row": { pattern: RegExp(`^${a}$`), inside: { "table-header": { pattern: RegExp(t), alias: "important", inside: n.languages.markdown }, punctuation: /\|/ } } } },
          code: [{ pattern: /((?:^|\n)[ \t]*\n|(?:^|\r\n?)[ \t]*\r\n?)(?: {4}|\t).+(?:(?:\n|\r\n?)(?: {4}|\t).+)*/, lookbehind: true, alias: "keyword" }, { pattern: /^```[\s\S]*?^```$/m, greedy: true, inside: { "code-block": { pattern: /^(```.*(?:\n|\r\n?))[\s\S]+?(?=(?:\n|\r\n?)^```$)/m, lookbehind: true }, "code-language": { pattern: /^(```).+/, lookbehind: true }, punctuation: /```/ } }],
          title: [{ pattern: /\S.*(?:\n|\r\n?)(?:==+|--+)(?=[ \t]*$)/m, alias: "important", inside: { punctuation: /==+$|--+$/ } }, {
            pattern: /(^\s*)#.+/m,
            lookbehind: true,
            alias: "important",
            inside: { punctuation: /^#+|#+$/ }
          }],
          hr: { pattern: /(^\s*)([*-])(?:[\t ]*\2){2,}(?=\s*$)/m, lookbehind: true, alias: "punctuation" },
          list: { pattern: /(^\s*)(?:[*+-]|\d+\.)(?=[\t ].)/m, lookbehind: true, alias: "punctuation" },
          "url-reference": { pattern: /!?\[[^\]]+\]:[\t ]+(?:\S+|<(?:\\.|[^>\\])+>)(?:[\t ]+(?:"(?:\\.|[^"\\])*"|'(?:\\.|[^'\\])*'|\((?:\\.|[^)\\])*\)))?/, inside: { variable: { pattern: /^(!?\[)[^\]]+/, lookbehind: true }, string: /(?:"(?:\\.|[^"\\])*"|'(?:\\.|[^'\\])*'|\((?:\\.|[^)\\])*\))$/, punctuation: /^[\[\]!:]|[<>]/ }, alias: "url" },
          bold: {
            pattern: e("\\b__(?:(?!_)<inner>|_(?:(?!_)<inner>)+_)+__\\b|\\*\\*(?:(?!\\*)<inner>|\\*(?:(?!\\*)<inner>)+\\*)+\\*\\*"),
            lookbehind: true,
            greedy: true,
            inside: { content: { pattern: /(^..)[\s\S]+(?=..$)/, lookbehind: true, inside: {} }, punctuation: /\*\*|__/ }
          },
          italic: {
            pattern: e("\\b_(?:(?!_)<inner>|__(?:(?!_)<inner>)+__)+_\\b|\\*(?:(?!\\*)<inner>|\\*\\*(?:(?!\\*)<inner>)+\\*\\*)+\\*"),
            lookbehind: true,
            greedy: true,
            inside: { content: { pattern: /(^.)[\s\S]+(?=.$)/, lookbehind: true, inside: {} }, punctuation: /[*_]/ }
          },
          strike: {
            pattern: e("(~~?)(?:(?!~)<inner>)+\\2"),
            lookbehind: true,
            greedy: true,
            inside: { content: { pattern: /(^~~?)[\s\S]+(?=\1$)/, lookbehind: true, inside: {} }, punctuation: /~~?/ }
          },
          "code-snippet": {
            pattern: /(^|[^\\`])(?:``[^`\r\n]+(?:`[^`\r\n]+)*``(?!`)|`[^`\r\n]+`(?!`))/,
            lookbehind: true,
            greedy: true,
            alias: ["code", "keyword"]
          },
          url: {
            pattern: e('!?\\[(?:(?!\\])<inner>)+\\](?:\\([^\\s)]+(?:[	 ]+"(?:\\\\.|[^"\\\\])*")?\\)|[ 	]?\\[(?:(?!\\])<inner>)+\\])'),
            lookbehind: true,
            greedy: true,
            inside: {
              operator: /^!/,
              content: { pattern: /(^\[)[^\]]+(?=\])/, lookbehind: true, inside: {} },
              variable: { pattern: /(^\][ \t]?\[)[^\]]+(?=\]$)/, lookbehind: true },
              url: { pattern: /(^\]\()[^\s)]+/, lookbehind: true },
              string: { pattern: /(^[ \t]+)"(?:\\.|[^"\\])*"(?=\)$)/, lookbehind: true }
            }
          }
        }), ["url", "bold", "italic", "strike"].forEach(((e2) => {
          ["url", "bold", "italic", "strike", "code-snippet"].forEach(((t2) => {
            e2 !== t2 && (n.languages.markdown[e2].inside.content.inside[t2] = n.languages.markdown[t2]);
          }));
        })), n.hooks.add("after-tokenize", ((n2) => {
          n2.language !== "markdown" && n2.language !== "md" || (function n3(e2) {
            if (e2 && typeof e2 !== "string") for (let t2 = 0, a2 = e2.length; t2 < a2; t2++) {
              const i2 = e2[t2];
              if (i2.type === "code") {
                const r2 = i2.content[1];
                const o2 = i2.content[3];
                if (r2 && o2 && r2.type === "code-language" && o2.type === "code-block" && typeof r2.content === "string") {
                  let l2 = r2.content.replace(/\b#/g, "sharp").replace(/\b\+\+/g, "pp");
                  const s = `language-${l2 = (/[a-z][\w-]*/i.exec(l2) || [""])[0].toLowerCase()}`;
                  o2.alias ? typeof o2.alias === "string" ? o2.alias = [o2.alias, s] : o2.alias.push(s) : o2.alias = [s];
                }
              } else n3(i2.content);
            }
          })(n2.tokens);
        })), n.hooks.add("wrap", ((e2) => {
          if (e2.type === "code-block") {
            for (var t2 = "", a2 = 0, i2 = e2.classes.length; a2 < i2; a2++) {
              const s = e2.classes[a2];
              const d = /language-(.+)/.exec(s);
              if (d) {
                t2 = d[1];
                break;
              }
            }
            const p = n.languages[t2];
            if (p) e2.content = n.highlight(e2.content.replace(r, "").replace(/&(\w{1,8}|#x?[\da-f]{1,8});/gi, ((n2, e3) => {
              let t3;
              return (e3 = e3.toLowerCase())[0] === "#" ? (t3 = e3[1] === "x" ? parseInt(e3.slice(2), 16) : Number(e3.slice(1)), l(t3)) : o[e3] || n2;
            })), p, t2);
            else if (t2 && t2 !== "none" && n.plugins.autoloader) {
              const u = `md-${(/* @__PURE__ */ new Date()).valueOf()}-${Math.floor(1e16 * Math.random())}`;
              e2.attributes.id = u, n.plugins.autoloader.loadLanguages(t2, (() => {
                const e3 = document.getElementById(u);
                e3 && (e3.innerHTML = n.highlight(e3.textContent, n.languages[t2], t2));
              }));
            }
          }
        }));
        var r = RegExp(n.languages.markup.tag.pattern.source, "gi");
        var o = {
          amp: "&",
          lt: "<",
          gt: ">",
          quot: '"'
        };
        var l = String.fromCodePoint || String.fromCharCode;
        n.languages.md = n.languages.markdown;
      })(Prism2);
      !(function(a) {
        const e = "(?:\\b[a-zA-Z]\\w*|[|\\\\[\\]])+";
        a.languages.phpdoc = a.languages.extend("javadoclike", { parameter: { pattern: RegExp(`(@(?:global|param|property(?:-read|-write)?|var)\\s+(?:${e}\\s+)?)\\$\\w+`), lookbehind: true } }), a.languages.insertBefore("phpdoc", "keyword", { "class-name": [{ pattern: RegExp(`(@(?:global|package|param|property(?:-read|-write)?|return|subpackage|throws|var)\\s+)${e}`), lookbehind: true, inside: { keyword: /\b(?:array|bool|boolean|callback|double|false|float|int|integer|mixed|null|object|resource|self|string|true|void)\b/, punctuation: /[|\\[\]()]/ } }] }), a.languages.javadoclike.addSupport("php", a.languages.phpdoc);
      })(Prism2);
      Prism2.languages.insertBefore("php", "variable", { this: { pattern: /\$this\b/, alias: "keyword" }, global: /\$(?:GLOBALS|HTTP_RAW_POST_DATA|_(?:COOKIE|ENV|FILES|GET|POST|REQUEST|SERVER|SESSION)|argc|argv|http_response_header|php_errormsg)\b/, scope: { pattern: /\b[\w\\]+::/, inside: { keyword: /\b(?:parent|self|static)\b/, punctuation: /::|\\/ } } });
      !(function(e) {
        const n = /[*&][^\s[\]{},]+/;
        const r = /!(?:<[\w\-%#;/?:@&=+$,.!~*'()[\]]+>|(?:[a-zA-Z\d-]*!)?[\w\-%#;/?:@&=+$.~*'()]+)?/;
        const t = `(?:${r.source}(?:[ 	]+${n.source})?|${n.source}(?:[ 	]+${r.source})?)`;
        const a = "(?:[^\\s\\x00-\\x08\\x0e-\\x1f!\"#%&'*,\\-:>?@[\\]`{|}\\x7f-\\x84\\x86-\\x9f\\ud800-\\udfff\\ufffe\\uffff]|[?:-]<PLAIN>)(?:[ 	]*(?:(?![#:])<PLAIN>|:<PLAIN>))*".replace(/<PLAIN>/g, (() => "[^\\s\\x00-\\x08\\x0e-\\x1f,[\\]{}\\x7f-\\x84\\x86-\\x9f\\ud800-\\udfff\\ufffe\\uffff]"));
        const d = `"(?:[^"\\\\\r
]|\\\\.)*"|'(?:[^'\\\\\r
]|\\\\.)*'`;
        function o(e2, n2) {
          n2 = `${(n2 || "").replace(/m/g, "")}m`;
          const r2 = "([:\\-,[{]\\s*(?:\\s<<prop>>[ 	]+)?)(?:<<value>>)(?=[ 	]*(?:$|,|\\]|\\}|(?:[\r\n]\\s*)?#))".replace(/<<prop>>/g, (() => t)).replace(/<<value>>/g, (() => e2));
          return RegExp(r2, n2);
        }
        e.languages.yaml = {
          scalar: { pattern: RegExp("([\\-:]\\s*(?:\\s<<prop>>[ 	]+)?[|>])[ 	]*(?:((?:\r?\n|\r)[ 	]+)\\S[^\r\n]*(?:\\2[^\r\n]+)*)".replace(/<<prop>>/g, (() => t))), lookbehind: true, alias: "string" },
          comment: /#.*/,
          key: {
            pattern: RegExp("((?:^|[:\\-,[{\r\n?])[ 	]*(?:<<prop>>[ 	]+)?)<<key>>(?=\\s*:\\s)".replace(/<<prop>>/g, (() => t)).replace(/<<key>>/g, (() => `(?:${a}|${d})`))),
            lookbehind: true,
            greedy: true,
            alias: "atrule"
          },
          directive: { pattern: /(^[ \t]*)%.+/m, lookbehind: true, alias: "important" },
          datetime: { pattern: o("\\d{4}-\\d\\d?-\\d\\d?(?:[tT]|[ 	]+)\\d\\d?:\\d{2}:\\d{2}(?:\\.\\d*)?(?:[ 	]*(?:Z|[-+]\\d\\d?(?::\\d{2})?))?|\\d{4}-\\d{2}-\\d{2}|\\d\\d?:\\d{2}(?::\\d{2}(?:\\.\\d*)?)?"), lookbehind: true, alias: "number" },
          boolean: { pattern: o("false|true", "i"), lookbehind: true, alias: "important" },
          null: { pattern: o("null|~", "i"), lookbehind: true, alias: "important" },
          string: { pattern: o(d), lookbehind: true, greedy: true },
          number: { pattern: o("[+-]?(?:0x[\\da-f]+|0o[0-7]+|(?:\\d+(?:\\.\\d*)?|\\.\\d+)(?:e[+-]?\\d+)?|\\.inf|\\.nan)", "i"), lookbehind: true },
          tag: r,
          important: n,
          punctuation: /---|[:[\]{}\-,|>?]|\.\.\./
        }, e.languages.yml = e.languages.yaml;
      })(Prism2);
      !(function() {
        if (typeof Prism2 !== "undefined") {
          let n;
          var s;
          var a = "";
          Prism2.plugins.customClass = {
            add(s2) {
              n = s2;
            },
            map(n2) {
              s = typeof n2 === "function" ? n2 : function(s2) {
                return n2[s2] || s2;
              };
            },
            prefix(n2) {
              a = n2 || "";
            },
            apply: t
          }, Prism2.hooks.add("wrap", ((e) => {
            if (n) {
              const u = n({ content: e.content, type: e.type, language: e.language });
              Array.isArray(u) ? e.classes.push.apply(e.classes, u) : u && e.classes.push(u);
            }
            (s || a) && (e.classes = e.classes.map(((n2) => t(n2, e.language))));
          }));
        }
        function t(n, t2) {
          return a + (s ? s(n, t2) : n);
        }
      })();
      !(function() {
        if (typeof Prism2 !== "undefined" && typeof document !== "undefined") {
          const e = [];
          const t = {};
          const n = function() {
          };
          Prism2.plugins.toolbar = {};
          const a = Prism2.plugins.toolbar.registerButton = function(n2, a2) {
            let r2;
            r2 = typeof a2 === "function" ? a2 : function(e2) {
              let t2;
              return typeof a2.onClick === "function" ? ((t2 = document.createElement("button")).type = "button", t2.addEventListener("click", (function() {
                a2.onClick.call(this, e2);
              }))) : typeof a2.url === "string" ? (t2 = document.createElement("a")).href = a2.url : t2 = document.createElement("span"), a2.className && t2.classList.add(a2.className), t2.textContent = a2.text, t2;
            }, n2 in t ? console.warn(`There is a button with the key "${n2}" registered already.`) : e.push(t[n2] = r2);
          };
          const r = Prism2.plugins.toolbar.hook = function(a2) {
            const r2 = a2.element.parentNode;
            if (r2 && /pre/i.test(r2.nodeName) && !r2.parentNode.classList.contains("code-toolbar")) {
              const o = document.createElement("div");
              o.classList.add("code-toolbar"), r2.parentNode.insertBefore(o, r2), o.appendChild(r2);
              const i = document.createElement("div");
              i.classList.add("toolbar");
              let l = e;
              const d = (function(e2) {
                for (; e2; ) {
                  let t2 = e2.getAttribute("data-toolbar-order");
                  if (t2 != null) return (t2 = t2.trim()).length ? t2.split(/\s*,\s*/g) : [];
                  e2 = e2.parentElement;
                }
              })(a2.element);
              d && (l = d.map(((e2) => t[e2] || n))), l.forEach(((e2) => {
                const t2 = e2(a2);
                if (t2) {
                  const n2 = document.createElement("div");
                  n2.classList.add("toolbar-item"), n2.appendChild(t2), i.appendChild(n2);
                }
              })), o.appendChild(i);
            }
          };
          a("label", ((e2) => {
            const t2 = e2.element.parentNode;
            if (t2 && /pre/i.test(t2.nodeName) && t2.hasAttribute("data-label")) {
              let n2;
              let a2;
              const r2 = t2.getAttribute("data-label");
              try {
                a2 = document.querySelector(`template#${r2}`);
              } catch (e3) {
              }
              return a2 ? n2 = a2.content : (t2.hasAttribute("data-url") ? (n2 = document.createElement("a")).href = t2.getAttribute("data-url") : n2 = document.createElement("span"), n2.textContent = r2), n2;
            }
          })), Prism2.hooks.add("complete", r);
        }
      })();
    }
  });

  // develop/js/libraries/clipboard.js
  var clipboard_exports = {};
  __export(clipboard_exports, {
    default: () => clipboard_default
  });
  function makeError() {
    return new DOMException("The request is not allowed", "NotAllowedError");
  }
  async function copyClipboardApi(e) {
    if (!navigator.clipboard) throw makeError();
    return navigator.clipboard.writeText(e);
  }
  async function copyExecCommand(e) {
    const o = document.createElement("span");
    o.textContent = e, o.style.whiteSpace = "pre", o.style.webkitUserSelect = "auto", o.style.userSelect = "all", document.body.appendChild(o);
    const t = window.getSelection();
    const r = window.document.createRange();
    t.removeAllRanges(), r.selectNode(o), t.addRange(r);
    let n = false;
    try {
      n = window.document.execCommand("copy");
    } finally {
      t.removeAllRanges(), window.document.body.removeChild(o);
    }
    if (!n) throw makeError();
  }
  async function clipboardCopy(e) {
    try {
      await copyClipboardApi(e);
    } catch (o) {
      try {
        await copyExecCommand(e);
      } catch (e2) {
        throw e2 || o || makeError();
      }
    }
  }
  var clipboard_default;
  var init_clipboard = __esm({
    "develop/js/libraries/clipboard.js"() {
      clipboard_default = clipboardCopy;
    }
  });

  // develop/js/components/m-code.js
  var MCode = class {
    constructor() {
      this.init();
    }
    async init() {
      await Promise.resolve().then(() => __toESM(require_prism()));
      this.setLanguages();
      Prism.highlightAll();
    }
    setLanguages() {
      Prism.languages.kirbytext = Prism.languages.extend("markdown", {});
      Prism.languages.insertBefore("kirbytext", "prolog", {
        kirbytag: {
          pattern: /\([a-z0-9_-]+:.*?\)/i,
          inside: {
            "kirbytag-bracket": /^\(|\)$/,
            "kirbytag-name": {
              pattern: /^[a-z0-9_-]+:/i
            },
            "kirbytag-attr": {
              pattern: /([^:]\s+)[a-z0-9_-]+:/i,
              lookbehind: true
            }
          }
        }
      });
      Prism.languages.kirbycontent = {
        delimiter: /\n----\s*\n*/,
        property: {
          pattern: /(^\n*|\n----\s*\n*)[a-zA-Z0-9_\-\u0020]+:/,
          lookbehind: true
        }
      };
    }
    setToolbar() {
      Prism.plugins.toolbar.registerButton("select-code", (env) => {
        const button = document.createElement("button");
        button.insertAdjacentHTML(
          "beforeend",
          '<svg viewBox="0 0 16 16" class="icon" ara-hidden="true"><path d="M10,4H2C1.4,4,1,4.4,1,5v10c0,0.6,0.4,1,1,1h8c0.6,0,1-0.4,1-1V5C11,4.4,10.6,4,10,4z"></path> <path data-color="color-2" d="M14,0H4v2h9v11h2V1C15,0.4,14.6,0,14,0z"></path></svg>'
        );
        const text = document.createElement("span");
        text.textContent = "Copy";
        button.appendChild(text);
        button.addEventListener("click", async () => {
          const { default: clipboard } = await Promise.resolve().then(() => (init_clipboard(), clipboard_exports));
          try {
            await clipboard(env.code);
            text.textContent = "Copied!";
          } catch (error) {
            text.textContent = "Press Ctrl+C/\u2318+C to copy";
          } finally {
            setTimeout(() => {
              text.textContent = "Copy";
            }, 5e3);
          }
        });
        return button;
      });
    }
  };
  new MCode();

  // develop/js/components/m-nav-main.js
  var MNavMain = class {
    /** @param {HTMLElement} element */
    constructor(element) {
      this.element = element;
      window.addEventListener("resize", () => this.onResize());
      this.onResize();
    }
    onResize() {
      this.element.toggleAttribute("popover", !window.matchMedia("(min-width: 64em").matches);
    }
  };
  document.querySelectorAll(".m-nav-main").forEach((_) => new MNavMain(_));

  // develop/js/components/m-nav-sidebar.js
  var MNAvSidebar = class {
    /**
     * @param {HTMLElement} element
     * @param {number} index
     */
    constructor(element, index) {
      __publicField(this, "scrollTop");
      this.element = element;
      this.index = index;
      const linkElements = element.querySelectorAll("a");
      linkElements.forEach((_) => _.addEventListener("click", this.handleLinkClick.bind(this)));
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
  };
  window.mNavSidebarElements = [...document.querySelectorAll(".m-nav-sidebar")].map((_, index) => new MNAvSidebar(_, index));

  // develop/js/components/m-nav-sidebars.js
  var MNavSidebars = class {
    /** @param {HTMLElement} element */
    constructor(element) {
      this.element = element;
      window.addEventListener("resize", () => this.onResize());
      this.onResize();
    }
    onResize() {
      this.element.toggleAttribute("popover", !window.matchMedia("(min-width: 64em").matches);
    }
  };
  document.querySelectorAll(".m-nav-sidebars").forEach((_) => new MNavSidebars(_));

  // develop/js/components/m-toc.js
  var MToc = class {
    /** @param {HTMLElement} element */
    constructor(element) {
      /** @type {number} */
      __publicField(this, "offset", 100);
      this.element = element;
      this.headingElements = document.querySelectorAll(".o-main__text h2");
      this.aElements = this.element.querySelectorAll("a");
      if (this.aElements.length > 0) {
        document.addEventListener("scroll", this.onScroll.bind(this), {
          passive: true
        });
        window.addEventListener("resize", this.onScroll.bind(this), {
          passive: true
        });
        this.onResize();
      }
    }
    onResize() {
      const computedStyle = getComputedStyle(document.documentElement);
      const scrollPaddingBlockStart = computedStyle.getPropertyValue("scroll-padding-block-start");
      if (scrollPaddingBlockStart) {
        this.offset = parseFloat(scrollPaddingBlockStart);
      }
      this.onScroll();
    }
    onScroll() {
      const { offset } = this;
      const { scrollTop } = document.scrollingElement;
      const { headingElements, aElements } = this;
      const currentHeading = [...headingElements].reverse().find((_) => scrollTop >= _.offsetTop - offset);
      [...aElements].forEach((_) => _.setAttribute("aria-current", _.getAttribute("href") === `#${currentHeading?.id}`) ? "true" : "false");
    }
  };
  document.querySelectorAll(".m-toc").forEach((_) => new MToc(_));

  // develop/js/components/m-search-dialog.js
  var MSearchDialog = class {
    /** @param {HTMLDivElement} element */
    constructor(element) {
      this.element = element;
      const inputElement = this.element.querySelector('input[type="search"]');
      document.addEventListener("keydown", (event) => {
        const isMac = /Mac|iPhone|iPad|iPod/i.test(navigator.userAgent || "");
        if ((isMac && event.metaKey || !isMac && event.ctrlKey) && event.key.toLowerCase() === "k") {
          event.preventDefault();
          this.element.showPopover();
        }
      });
      inputElement.addEventListener("keydown", (event) => {
        if (event.key === "Escape") {
          this.element.hidePopover();
        }
      });
      element.addEventListener("toggle", (event) => {
        if (event.newState === "open") {
          inputElement?.focus();
        }
      });
      element.addEventListener("click", (event) => {
        if (event.target === element) {
          this.element.hidePopover();
        }
      });
    }
  };
  document.querySelectorAll(".m-search-dialog").forEach((_) => new MSearchDialog(_));

  // develop/js/components/m-search.js
  var MSearch = class {
    /** @param {HTMLFormElement} element */
    constructor(element) {
      __publicField(this, "selectedIndex", -1);
      this.element = element;
      this.resultsElement = element.querySelector(".m-search__results");
      this.inputElement = element.querySelector('input[type="search"]');
      this.infoElement = element.querySelector(".m-search__info");
      this.inputElement.addEventListener("input", () => {
        if (this.infoElement) {
          this.infoElement.innerText = "";
        }
        const query = this.inputElement.value;
        if (query.length < 3) {
          this.renderResults([]);
          return;
        }
        this.search(query);
      });
      this.element.addEventListener("submit", (event) => {
        if (this.selectedIndex !== -1) {
          event.preventDefault();
          this.openCurrentIndex();
        }
      });
      this.element.addEventListener("mousemove", (event) => {
        const index = parseInt(event.target.closest('[role="option"]')?.dataset.index ?? -1, 10);
        this.selectResult(index);
      });
      this.inputElement.addEventListener("keydown", (event) => {
        if (event.key === "ArrowUp" || event.key === "ArrowDown") {
          event.preventDefault();
          this.selectResult(event.key === "ArrowUp" ? this.selectedIndex - 1 : this.selectedIndex + 1);
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
          result.setAttribute("aria-selected", "true");
          this.selectedIndex = index;
        } else {
          result.removeAttribute("aria-selected");
        }
      });
    }
    renderResults(results) {
      this.element.querySelector(".m-search__results").innerHTML = results.map((result, index) => `
			<li role="option" data-index="${index}">
				<a href="${result.url}">
					<strong>${result.title}</strong>
					<small>${result.breadcrumbs.join(" / ")}</small>
				</a>
			</li>
		`).join("");
    }
    async search(query) {
      const response = await fetch(`/api/search?limit=20&q=${query}`).catch((error) => {
        this.infoElement.innerText = error.message;
      });
      const { data = [], message } = await response.json();
      if (data.length === 0) {
        this.infoElement.innerText = message;
      }
      this.renderResults(data);
    }
  };
  document.querySelectorAll('.m-search[data-variant="combobox"]').forEach((_) => new MSearch(_));

  // develop/js/components/o-checkout.js
  var OCheckout = class {
    /** @param {HTMLElement} element */
    constructor(element) {
      __publicField(this, "stripeElementsAppearance", {
        "theme": "flat",
        "labels": "above",
        "disableAnimations": true,
        "variables": {
          "fontFamily": "'TT Norms M', 'TT Norms', Avenir, sans-serif",
          "borderRadius": "2px",
          "inputColorBorder": "hsl(0, 0%, 33%)",
          "tabLogoSelectedColor": "dark",
          "colorBackground": "#fff",
          "fontSizeSm": "1rem",
          "fontSizeLg": "1.125rem",
          "fontSizeXl": "1.4375rem",
          "fontLineHeight": "1.25",
          "fontWeightMedium": "500",
          "fontSize2Xl": "1.4375rem",
          "fontSize2Xs": "0.75rem",
          "fontWeightNormal": "450",
          "colorText": "hsl(0, 0%, 13%)",
          "colorPrimary": "hsl(0, 0%, 13%)",
          "colorDanger": "hsl(0, 60%, 60%)",
          "colorSuccess": "hsl(103, 64%, 22%)",
          "colorWarning": "hsl(60, 80%, 60%)",
          "colorTextSecondary": "hsl(0, 0%, 40%)",
          "colorTextPlaceholder": "hsl(0, 0%, 60%)",
          "iconColor": "var(--colorPrimary)",
          "gridColumnSpacing": "1.25rem",
          "gridRowSpacing": "0.75rem",
          "accordionItemSpacing": "0.75rem"
        },
        "rules": {
          ".Input": {
            "border": "1px solid hsl(0, 0%, 60%)",
            "padding": "0.5rem 0.4375rem"
          },
          ".Tab--selected, .Tab--selected:focus": {
            "border": "none",
            "boxShadow": "none"
          },
          ".Tab:focus": {
            "boxShadow": "none",
            "outline": "2px solid Highlight",
            "outlineOffset": "2px"
          },
          ".Input:focus": {
            "boxShadow": "none",
            "outline": "2px solid Highlight",
            "outlineOffset": "2px"
          },
          ".Tab": {
            "padding": "0.5rem 0.75rem"
          },
          ".TabLabel": {
            "paddingTop": "0.5rem"
          }
        }
      });
      this.element = element;
      this.formElement = element.querySelector("form");
      this.stripePublishableKey = this.formElement.elements.stripePublishableKey.value;
      this.formElement.addEventListener("submit", async (event) => {
        event.preventDefault();
        const formData = new FormData(this.formElement);
        const response = await fetch(this.formElement.action, {
          method: "post",
          body: formData,
          headers: {
            "Accept": "application/json"
          }
        });
        const json = await response.json();
        try {
          if (json.status === "redirect") {
            await this.completeStripePayment(json.redirectUrl, formData);
          } else if (json.key === "error.merx.fieldsvalidation") {
            this.handleFieldErrors(json);
          } else {
            throw json.message ?? "Fatal error";
          }
        } catch (errorMessage) {
          this.showError(errorMessage, event.submitter);
        }
      });
      this.initStripe().catch((error) => {
        this.showError(error.message, this.formElement.querySelector('[type="submit"]'));
      });
    }
    handleFieldErrors(json) {
      const errorDetails = json.details;
      for (const [key, error] of Object.entries(errorDetails)) {
        const labelElement = this.formElement.querySelector(`label:has([name="${key}"])`);
        if (labelElement) {
          for (const errorMessage of Object.values(error.message)) {
            labelElement.appendChild(this.createErrorElement(errorMessage));
          }
        } else {
          throw json.message ?? "Fatal error";
        }
      }
    }
    // eslint-disable-next-line
    createErrorElement(errorMessage) {
      return Object.assign(document.createElement("p"), {
        classList: "error",
        innerText: errorMessage
      });
    }
    showError(errorMessage, elementBefore) {
      elementBefore.parentElement.insertBefore(this.createErrorElement(errorMessage), elementBefore);
    }
    async completeStripePayment(redirectUrl, formData) {
      const { error } = await this.stripe.confirmPayment({
        // `Elements` instance that was used to create the Payment Element
        elements: this.stripeElements,
        confirmParams: {
          return_url: redirectUrl,
          payment_method_data: {
            billing_details: {
              name: formData.get("name"),
              email: formData.get("email"),
              address: {
                country: formData.get("country"),
                city: formData.get("city"),
                line1: formData.get("street"),
                postal_code: formData.get("postal_code"),
                state: formData.get("state")
              }
            }
          }
        }
      });
      if (error) {
        throw error.message;
      }
    }
    async initStripe() {
      this.stripe = window.Stripe(this.stripePublishableKey);
      const response = await fetch("/api/shop/stripe-client-secret").catch((error) => this.showError(error.message, this.formElement.querySelector('[type="submit"]')));
      const { clientSecret } = await response.json();
      this.stripeElements = this.stripe.elements({
        locale: document.querySelector("html").lang,
        loader: "never",
        clientSecret,
        fonts: [
          {
            cssSrc: "https://merx.wagnerwagner.test/assets/css/fonts.css"
          }
        ],
        appearance: this.stripeElementsAppearance
      });
      const paymentElement = this.stripeElements.create("payment", {
        layout: {
          type: "tabs"
        },
        fields: {
          billingDetails: {
            name: "never",
            email: "never",
            address: "never"
          }
        }
      });
      paymentElement.mount(".m-form-checkout__payment-fields");
    }
  };
  document.querySelectorAll(".o-checkout").forEach((_) => new OCheckout(_));
})();
/*! clipboard-copy. MIT License. Feross Aboukhadijeh <https://feross.org/opensource> */
//# sourceMappingURL=index.js.map
