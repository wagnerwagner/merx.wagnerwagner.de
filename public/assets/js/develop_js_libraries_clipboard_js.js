"use strict";
(self["webpackChunkmerx_wagnerwagner_de"] = self["webpackChunkmerx_wagnerwagner_de"] || []).push([["develop_js_libraries_clipboard_js"],{

/***/ "./develop/js/libraries/clipboard.js":
/*!*******************************************!*\
  !*** ./develop/js/libraries/clipboard.js ***!
  \*******************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony import */ var core_js_modules_es_promise_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! core-js/modules/es.promise.js */ "./node_modules/core-js/modules/es.promise.js");
/* harmony import */ var core_js_modules_es_promise_js__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_promise_js__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var core_js_modules_web_dom_exception_constructor_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! core-js/modules/web.dom-exception.constructor.js */ "./node_modules/core-js/modules/web.dom-exception.constructor.js");
/* harmony import */ var core_js_modules_web_dom_exception_constructor_js__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_web_dom_exception_constructor_js__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var core_js_modules_web_dom_exception_stack_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! core-js/modules/web.dom-exception.stack.js */ "./node_modules/core-js/modules/web.dom-exception.stack.js");
/* harmony import */ var core_js_modules_web_dom_exception_stack_js__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_web_dom_exception_stack_js__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var core_js_modules_web_dom_exception_to_string_tag_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! core-js/modules/web.dom-exception.to-string-tag.js */ "./node_modules/core-js/modules/web.dom-exception.to-string-tag.js");
/* harmony import */ var core_js_modules_web_dom_exception_to_string_tag_js__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_web_dom_exception_to_string_tag_js__WEBPACK_IMPORTED_MODULE_3__);
function asyncGeneratorStep(gen, resolve, reject, _next, _throw, key, arg) { try { var info = gen[key](arg); var value = info.value; } catch (error) { reject(error); return; } if (info.done) { resolve(value); } else { Promise.resolve(value).then(_next, _throw); } }
function _asyncToGenerator(fn) { return function () { var self = this, args = arguments; return new Promise(function (resolve, reject) { var gen = fn.apply(self, args); function _next(value) { asyncGeneratorStep(gen, resolve, reject, _next, _throw, "next", value); } function _throw(err) { asyncGeneratorStep(gen, resolve, reject, _next, _throw, "throw", err); } _next(undefined); }); }; }




/*! clipboard-copy. MIT License. Feross Aboukhadijeh <https://feross.org/opensource> */
/* harmony default export */ __webpack_exports__["default"] = (clipboardCopy);
function makeError() {
  return new DOMException("The request is not allowed", "NotAllowedError");
}
function copyClipboardApi(_x) {
  return _copyClipboardApi.apply(this, arguments);
}
function _copyClipboardApi() {
  _copyClipboardApi = _asyncToGenerator(function* (e) {
    if (!navigator.clipboard) throw makeError();
    return navigator.clipboard.writeText(e);
  });
  return _copyClipboardApi.apply(this, arguments);
}
function copyExecCommand(_x2) {
  return _copyExecCommand.apply(this, arguments);
}
function _copyExecCommand() {
  _copyExecCommand = _asyncToGenerator(function* (e) {
    var o = document.createElement("span");
    o.textContent = e, o.style.whiteSpace = "pre", o.style.webkitUserSelect = "auto", o.style.userSelect = "all", document.body.appendChild(o);
    var t = window.getSelection(),
      r = window.document.createRange();
    t.removeAllRanges(), r.selectNode(o), t.addRange(r);
    var n = !1;
    try {
      n = window.document.execCommand("copy");
    } finally {
      t.removeAllRanges(), window.document.body.removeChild(o);
    }
    if (!n) throw makeError();
  });
  return _copyExecCommand.apply(this, arguments);
}
function clipboardCopy(_x3) {
  return _clipboardCopy.apply(this, arguments);
}
function _clipboardCopy() {
  _clipboardCopy = _asyncToGenerator(function* (e) {
    try {
      yield copyClipboardApi(e);
    } catch (o) {
      try {
        yield copyExecCommand(e);
      } catch (e) {
        throw e || o || makeError();
      }
    }
  });
  return _clipboardCopy.apply(this, arguments);
}

/***/ })

}]);
//# sourceMappingURL=develop_js_libraries_clipboard_js.js.map