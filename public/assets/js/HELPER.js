!function(t){var n={};function r(e){if(n[e])return n[e].exports;var o=n[e]={i:e,l:!1,exports:{}};return t[e].call(o.exports,o,o.exports,r),o.l=!0,o.exports}r.m=t,r.c=n,r.d=function(t,n,e){r.o(t,n)||Object.defineProperty(t,n,{enumerable:!0,get:e})},r.r=function(t){"undefined"!=typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(t,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(t,"__esModule",{value:!0})},r.t=function(t,n){if(1&n&&(t=r(t)),8&n)return t;if(4&n&&"object"==typeof t&&t&&t.__esModule)return t;var e=Object.create(null);if(r.r(e),Object.defineProperty(e,"default",{enumerable:!0,value:t}),2&n&&"string"!=typeof t)for(var o in t)r.d(e,o,function(n){return t[n]}.bind(null,o));return e},r.n=function(t){var n=t&&t.__esModule?function(){return t.default}:function(){return t};return r.d(n,"a",n),n},r.o=function(t,n){return Object.prototype.hasOwnProperty.call(t,n)},r.p="",r(r.s=91)}([function(t,n,r){(function(n){var r=function(t){return t&&t.Math==Math&&t};t.exports=r("object"==typeof globalThis&&globalThis)||r("object"==typeof window&&window)||r("object"==typeof self&&self)||r("object"==typeof n&&n)||function(){return this}()||Function("return this")()}).call(this,r(28))},function(t,n,r){var e=r(0),o=r(31),i=r(4),c=r(32),u=r(30),f=r(40),a=o("wks"),s=e.Symbol,l=f?s:s&&s.withoutSetter||c;t.exports=function(t){return i(a,t)&&(u||"string"==typeof a[t])||(u&&i(s,t)?a[t]=s[t]:a[t]=l("Symbol."+t)),a[t]}},function(t,n){t.exports=function(t){try{return!!t()}catch(t){return!0}}},function(t,n){t.exports=function(t){return"object"==typeof t?null!==t:"function"==typeof t}},function(t,n,r){var e=r(12),o={}.hasOwnProperty;t.exports=Object.hasOwn||function(t,n){return o.call(e(t),n)}},function(t,n,r){var e=r(2);t.exports=!e((function(){return 7!=Object.defineProperty({},1,{get:function(){return 7}})[1]}))},function(t,n,r){var e=r(5),o=r(42),i=r(8),c=r(15),u=Object.defineProperty;n.f=e?u:function(t,n,r){if(i(t),n=c(n),i(r),o)try{return u(t,n,r)}catch(t){}if("get"in r||"set"in r)throw TypeError("Accessors not supported");return"value"in r&&(t[n]=r.value),t}},function(t,n,r){var e=r(0),o=r(18).f,i=r(10),c=r(17),u=r(24),f=r(49),a=r(51);t.exports=function(t,n){var r,s,l,p,v,d=t.target,y=t.global,b=t.stat;if(r=y?e:b?e[d]||u(d,{}):(e[d]||{}).prototype)for(s in n){if(p=n[s],l=t.noTargetGet?(v=o(r,s))&&v.value:r[s],!a(y?s:d+(b?".":"#")+s,t.forced)&&void 0!==l){if(typeof p==typeof l)continue;f(p,l)}(t.sham||l&&l.sham)&&i(p,"sham",!0),c(r,s,p,t)}}},function(t,n,r){var e=r(3);t.exports=function(t){if(!e(t))throw TypeError(String(t)+" is not an object");return t}},function(t,n,r){var e=r(39),o=r(20);t.exports=function(t){return e(o(t))}},function(t,n,r){var e=r(5),o=r(6),i=r(13);t.exports=e?function(t,n,r){return o.f(t,n,i(1,r))}:function(t,n,r){return t[n]=r,t}},function(t,n,r){var e=r(0),o=function(t){return"function"==typeof t?t:void 0};t.exports=function(t,n){return arguments.length<2?o(e[t]):e[t]&&e[t][n]}},function(t,n,r){var e=r(20);t.exports=function(t){return Object(e(t))}},function(t,n){t.exports=function(t,n){return{enumerable:!(1&t),configurable:!(2&t),writable:!(4&t),value:n}}},function(t,n,r){var e=r(35),o=Math.min;t.exports=function(t){return t>0?o(e(t),9007199254740991):0}},function(t,n,r){var e=r(57),o=r(21);t.exports=function(t){var n=e(t,"string");return o(n)?n:String(n)}},function(t,n,r){var e,o,i=r(0),c=r(41),u=i.process,f=i.Deno,a=u&&u.versions||f&&f.version,s=a&&a.v8;s?o=(e=s.split("."))[0]<4?1:e[0]+e[1]:c&&(!(e=c.match(/Edge\/(\d+)/))||e[1]>=74)&&(e=c.match(/Chrome\/(\d+)/))&&(o=e[1]),t.exports=o&&+o},function(t,n,r){var e=r(0),o=r(10),i=r(4),c=r(24),u=r(33),f=r(25),a=f.get,s=f.enforce,l=String(String).split("String");(t.exports=function(t,n,r,u){var f,a=!!u&&!!u.unsafe,p=!!u&&!!u.enumerable,v=!!u&&!!u.noTargetGet;"function"==typeof r&&("string"!=typeof n||i(r,"name")||o(r,"name",n),(f=s(r)).source||(f.source=l.join("string"==typeof n?n:""))),t!==e?(a?!v&&t[n]&&(p=!0):delete t[n],p?t[n]=r:o(t,n,r)):p?t[n]=r:c(n,r)})(Function.prototype,"toString",(function(){return"function"==typeof this&&a(this).source||u(this)}))},function(t,n,r){var e=r(5),o=r(29),i=r(13),c=r(9),u=r(15),f=r(4),a=r(42),s=Object.getOwnPropertyDescriptor;n.f=e?s:function(t,n){if(t=c(t),n=u(n),a)try{return s(t,n)}catch(t){}if(f(t,n))return i(!o.f.call(t,n),t[n])}},function(t,n){var r={}.toString;t.exports=function(t){return r.call(t).slice(8,-1)}},function(t,n){t.exports=function(t){if(null==t)throw TypeError("Can't call method on "+t);return t}},function(t,n,r){var e=r(11),o=r(40);t.exports=o?function(t){return"symbol"==typeof t}:function(t){var n=e("Symbol");return"function"==typeof n&&Object(t)instanceof n}},function(t,n){t.exports=!1},function(t,n,r){var e=r(0),o=r(24),i=e["__core-js_shared__"]||o("__core-js_shared__",{});t.exports=i},function(t,n,r){var e=r(0);t.exports=function(t,n){try{Object.defineProperty(e,t,{value:n,configurable:!0,writable:!0})}catch(r){e[t]=n}return n}},function(t,n,r){var e,o,i,c=r(59),u=r(0),f=r(3),a=r(10),s=r(4),l=r(23),p=r(34),v=r(26),d=u.WeakMap;if(c||l.state){var y=l.state||(l.state=new d),b=y.get,g=y.has,h=y.set;e=function(t,n){if(g.call(y,t))throw new TypeError("Object already initialized");return n.facade=t,h.call(y,t,n),n},o=function(t){return b.call(y,t)||{}},i=function(t){return g.call(y,t)}}else{var m=p("state");v[m]=!0,e=function(t,n){if(s(t,m))throw new TypeError("Object already initialized");return n.facade=t,a(t,m,n),n},o=function(t){return s(t,m)?t[m]:{}},i=function(t){return s(t,m)}}t.exports={set:e,get:o,has:i,enforce:function(t){return i(t)?o(t):e(t,{})},getterFor:function(t){return function(n){var r;if(!f(n)||(r=o(n)).type!==t)throw TypeError("Incompatible receiver, "+t+" required");return r}}}},function(t,n){t.exports={}},function(t,n,r){var e=r(19);t.exports=Array.isArray||function(t){return"Array"==e(t)}},function(t,n){var r;r=function(){return this}();try{r=r||new Function("return this")()}catch(t){"object"==typeof window&&(r=window)}t.exports=r},function(t,n,r){"use strict";var e={}.propertyIsEnumerable,o=Object.getOwnPropertyDescriptor,i=o&&!e.call({1:2},1);n.f=i?function(t){var n=o(this,t);return!!n&&n.enumerable}:e},function(t,n,r){var e=r(16),o=r(2);t.exports=!!Object.getOwnPropertySymbols&&!o((function(){var t=Symbol();return!String(t)||!(Object(t)instanceof Symbol)||!Symbol.sham&&e&&e<41}))},function(t,n,r){var e=r(22),o=r(23);(t.exports=function(t,n){return o[t]||(o[t]=void 0!==n?n:{})})("versions",[]).push({version:"3.16.0",mode:e?"pure":"global",copyright:"© 2021 Denis Pushkarev (zloirock.ru)"})},function(t,n){var r=0,e=Math.random();t.exports=function(t){return"Symbol("+String(void 0===t?"":t)+")_"+(++r+e).toString(36)}},function(t,n,r){var e=r(23),o=Function.toString;"function"!=typeof e.inspectSource&&(e.inspectSource=function(t){return o.call(t)}),t.exports=e.inspectSource},function(t,n,r){var e=r(31),o=r(32),i=e("keys");t.exports=function(t){return i[t]||(i[t]=o(t))}},function(t,n){var r=Math.ceil,e=Math.floor;t.exports=function(t){return isNaN(t=+t)?0:(t>0?e:r)(t)}},function(t,n){t.exports=["constructor","hasOwnProperty","isPrototypeOf","propertyIsEnumerable","toLocaleString","toString","valueOf"]},function(t,n,r){var e=r(45),o=r(36);t.exports=Object.keys||function(t){return e(t,o)}},,function(t,n,r){var e=r(2),o=r(19),i="".split;t.exports=e((function(){return!Object("z").propertyIsEnumerable(0)}))?function(t){return"String"==o(t)?i.call(t,""):Object(t)}:Object},function(t,n,r){var e=r(30);t.exports=e&&!Symbol.sham&&"symbol"==typeof Symbol.iterator},function(t,n,r){var e=r(11);t.exports=e("navigator","userAgent")||""},function(t,n,r){var e=r(5),o=r(2),i=r(43);t.exports=!e&&!o((function(){return 7!=Object.defineProperty(i("div"),"a",{get:function(){return 7}}).a}))},function(t,n,r){var e=r(0),o=r(3),i=e.document,c=o(i)&&o(i.createElement);t.exports=function(t){return c?i.createElement(t):{}}},function(t,n,r){var e=r(45),o=r(36).concat("length","prototype");n.f=Object.getOwnPropertyNames||function(t){return e(t,o)}},function(t,n,r){var e=r(4),o=r(9),i=r(61).indexOf,c=r(26);t.exports=function(t,n){var r,u=o(t),f=0,a=[];for(r in u)!e(c,r)&&e(u,r)&&a.push(r);for(;n.length>f;)e(u,r=n[f++])&&(~i(a,r)||a.push(r));return a}},function(t,n){n.f=Object.getOwnPropertySymbols},function(t,n,r){"use strict";var e=r(15),o=r(6),i=r(13);t.exports=function(t,n,r){var c=e(n);c in t?o.f(t,c,i(0,r)):t[c]=r}},,function(t,n,r){var e=r(4),o=r(60),i=r(18),c=r(6);t.exports=function(t,n){for(var r=o(n),u=c.f,f=i.f,a=0;a<r.length;a++){var s=r[a];e(t,s)||u(t,s,f(n,s))}}},function(t,n,r){var e=r(35),o=Math.max,i=Math.min;t.exports=function(t,n){var r=e(t);return r<0?o(r+n,0):i(r,n)}},function(t,n,r){var e=r(2),o=/#|\.prototype\./,i=function(t,n){var r=u[c(t)];return r==a||r!=f&&("function"==typeof n?e(n):!!n)},c=i.normalize=function(t){return String(t).replace(o,".").toLowerCase()},u=i.data={},f=i.NATIVE="N",a=i.POLYFILL="P";t.exports=i},function(t,n,r){var e=r(63);t.exports=function(t,n){return new(e(t))(0===n?0:n)}},function(t,n,r){var e=r(2),o=r(1),i=r(16),c=o("species");t.exports=function(t){return i>=51||!e((function(){var n=[];return(n.constructor={})[c]=function(){return{foo:1}},1!==n[t](Boolean).foo}))}},,,,function(t,n,r){var e=r(3),o=r(21),i=r(58),c=r(1)("toPrimitive");t.exports=function(t,n){if(!e(t)||o(t))return t;var r,u=t[c];if(void 0!==u){if(void 0===n&&(n="default"),r=u.call(t,n),!e(r)||o(r))return r;throw TypeError("Can't convert object to primitive value")}return void 0===n&&(n="number"),i(t,n)}},function(t,n,r){var e=r(3);t.exports=function(t,n){var r,o;if("string"===n&&"function"==typeof(r=t.toString)&&!e(o=r.call(t)))return o;if("function"==typeof(r=t.valueOf)&&!e(o=r.call(t)))return o;if("string"!==n&&"function"==typeof(r=t.toString)&&!e(o=r.call(t)))return o;throw TypeError("Can't convert object to primitive value")}},function(t,n,r){var e=r(0),o=r(33),i=e.WeakMap;t.exports="function"==typeof i&&/native code/.test(o(i))},function(t,n,r){var e=r(11),o=r(44),i=r(46),c=r(8);t.exports=e("Reflect","ownKeys")||function(t){var n=o.f(c(t)),r=i.f;return r?n.concat(r(t)):n}},function(t,n,r){var e=r(9),o=r(14),i=r(50),c=function(t){return function(n,r,c){var u,f=e(n),a=o(f.length),s=i(c,a);if(t&&r!=r){for(;a>s;)if((u=f[s++])!=u)return!0}else for(;a>s;s++)if((t||s in f)&&f[s]===r)return t||s||0;return!t&&-1}};t.exports={includes:c(!0),indexOf:c(!1)}},function(t,n,r){"use strict";var e=r(7),o=r(2),i=r(27),c=r(3),u=r(12),f=r(14),a=r(47),s=r(52),l=r(53),p=r(1),v=r(16),d=p("isConcatSpreadable"),y=v>=51||!o((function(){var t=[];return t[d]=!1,t.concat()[0]!==t})),b=l("concat"),g=function(t){if(!c(t))return!1;var n=t[d];return void 0!==n?!!n:i(t)};e({target:"Array",proto:!0,forced:!y||!b},{concat:function(t){var n,r,e,o,i,c=u(this),l=s(c,0),p=0;for(n=-1,e=arguments.length;n<e;n++)if(g(i=-1===n?c:arguments[n])){if(p+(o=f(i.length))>9007199254740991)throw TypeError("Maximum allowed index exceeded");for(r=0;r<o;r++,p++)r in i&&a(l,p,i[r])}else{if(p>=9007199254740991)throw TypeError("Maximum allowed index exceeded");a(l,p++,i)}return l.length=p,l}})},function(t,n,r){var e=r(3),o=r(27),i=r(1)("species");t.exports=function(t){var n;return o(t)&&("function"!=typeof(n=t.constructor)||n!==Array&&!o(n.prototype)?e(n)&&null===(n=n[i])&&(n=void 0):n=void 0),void 0===n?Array:n}},,,,,,,,,,,,,,,,,,,,,,,,,,,,function(t,n,r){t.exports=r(92)},function(t,n,r){"use strict";r.r(n);r(93),r(62);var e,o,i=!1,c=!1;function u(){var t=window.innerWidth,n=16*Math.round(window.innerWidth/16);o.innerText="".concat(t," / ").concat(n)}document.addEventListener("keydown",(function(t){var n;!0===t.altKey&&!0===t.ctrlKey&&(76===t.keyCode?!1===i?(!function(){var t=document.querySelector("body");e=document.createElement("div"),Object.assign(e.style,{position:"fixed",zIndex:"100",left:"0",right:"0",top:"0",bottom:"0",pointerEvents:"none"});var n=document.createElement("div");Object.assign(n.style,{display:"flex",maxWidth:"".concat(1344,"px"),height:"100%",marginLeft:"auto",marginRight:"auto",alignItems:"stretch",justifyContent:"center"}),e.appendChild(n);for(var r=0;r<14;r+=1){var o=document.createElement("div");Object.assign(o.style,{flex:"".concat(48,"px 0 0"),marginLeft:"".concat(24,"px"),marginRight:"".concat(24,"px"),background:"hsla(0, 0%, 66%, 0.1)"}),n.appendChild(o)}t.appendChild(e)}(),i=!0,console.log("show grid")):(e.remove(),i=!1,console.log("hide grid")):87===t.keyCode&&(!1===c?(n=document.querySelector("body"),o=document.createElement("div"),Object.assign(o.style,{position:"fixed",zIndex:"100",right:"0",bottom:"0",backgroundColor:"black",color:"white",padding:"1em"}),u(),n.appendChild(o),c=!0,console.log("show width info")):(o.remove(),c=!1,console.log("hide width info"))))})),window.addEventListener("resize",(function(){o&&u()}))},function(t,n,r){var e=r(7),o=r(94);e({target:"Object",stat:!0,forced:Object.assign!==o},{assign:o})},function(t,n,r){"use strict";var e=r(5),o=r(2),i=r(37),c=r(46),u=r(29),f=r(12),a=r(39),s=Object.assign,l=Object.defineProperty;t.exports=!s||o((function(){if(e&&1!==s({b:1},s(l({},"a",{enumerable:!0,get:function(){l(this,"b",{value:3,enumerable:!1})}}),{b:2})).b)return!0;var t={},n={},r=Symbol();return t[r]=7,"abcdefghijklmnopqrst".split("").forEach((function(t){n[t]=t})),7!=s({},t)[r]||"abcdefghijklmnopqrst"!=i(s({},n)).join("")}))?function(t,n){for(var r=f(t),o=arguments.length,s=1,l=c.f,p=u.f;o>s;)for(var v,d=a(arguments[s++]),y=l?i(d).concat(l(d)):i(d),b=y.length,g=0;b>g;)v=y[g++],e&&!p.call(d,v)||(r[v]=d[v]);return r}:s}]);