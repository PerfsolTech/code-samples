/******/ (function(modules) { // webpackBootstrap
/******/ 	// The module cache
/******/ 	var installedModules = {};
/******/
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/
/******/ 		// Check if module is in cache
/******/ 		if(installedModules[moduleId]) {
/******/ 			return installedModules[moduleId].exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = installedModules[moduleId] = {
/******/ 			i: moduleId,
/******/ 			l: false,
/******/ 			exports: {}
/******/ 		};
/******/
/******/ 		// Execute the module function
/******/ 		modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);
/******/
/******/ 		// Flag the module as loaded
/******/ 		module.l = true;
/******/
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/
/******/
/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = modules;
/******/
/******/ 	// expose the module cache
/******/ 	__webpack_require__.c = installedModules;
/******/
/******/ 	// define getter function for harmony exports
/******/ 	__webpack_require__.d = function(exports, name, getter) {
/******/ 		if(!__webpack_require__.o(exports, name)) {
/******/ 			Object.defineProperty(exports, name, {
/******/ 				configurable: false,
/******/ 				enumerable: true,
/******/ 				get: getter
/******/ 			});
/******/ 		}
/******/ 	};
/******/
/******/ 	// getDefaultExport function for compatibility with non-harmony modules
/******/ 	__webpack_require__.n = function(module) {
/******/ 		var getter = module && module.__esModule ?
/******/ 			function getDefault() { return module['default']; } :
/******/ 			function getModuleExports() { return module; };
/******/ 		__webpack_require__.d(getter, 'a', getter);
/******/ 		return getter;
/******/ 	};
/******/
/******/ 	// Object.prototype.hasOwnProperty.call
/******/ 	__webpack_require__.o = function(object, property) { return Object.prototype.hasOwnProperty.call(object, property); };
/******/
/******/ 	// __webpack_public_path__
/******/ 	__webpack_require__.p = "";
/******/
/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(__webpack_require__.s = 3);
/******/ })
/************************************************************************/
/******/ ([
/* 0 */
/***/ (function(module, exports) {

/* globals __VUE_SSR_CONTEXT__ */

// IMPORTANT: Do NOT use ES2015 features in this file.
// This module is a runtime utility for cleaner component module output and will
// be included in the final webpack user bundle.

module.exports = function normalizeComponent (
  rawScriptExports,
  compiledTemplate,
  functionalTemplate,
  injectStyles,
  scopeId,
  moduleIdentifier /* server only */
) {
  var esModule
  var scriptExports = rawScriptExports = rawScriptExports || {}

  // ES6 modules interop
  var type = typeof rawScriptExports.default
  if (type === 'object' || type === 'function') {
    esModule = rawScriptExports
    scriptExports = rawScriptExports.default
  }

  // Vue.extend constructor export interop
  var options = typeof scriptExports === 'function'
    ? scriptExports.options
    : scriptExports

  // render functions
  if (compiledTemplate) {
    options.render = compiledTemplate.render
    options.staticRenderFns = compiledTemplate.staticRenderFns
    options._compiled = true
  }

  // functional template
  if (functionalTemplate) {
    options.functional = true
  }

  // scopedId
  if (scopeId) {
    options._scopeId = scopeId
  }

  var hook
  if (moduleIdentifier) { // server build
    hook = function (context) {
      // 2.3 injection
      context =
        context || // cached call
        (this.$vnode && this.$vnode.ssrContext) || // stateful
        (this.parent && this.parent.$vnode && this.parent.$vnode.ssrContext) // functional
      // 2.2 with runInNewContext: true
      if (!context && typeof __VUE_SSR_CONTEXT__ !== 'undefined') {
        context = __VUE_SSR_CONTEXT__
      }
      // inject component styles
      if (injectStyles) {
        injectStyles.call(this, context)
      }
      // register component module identifier for async chunk inferrence
      if (context && context._registeredComponents) {
        context._registeredComponents.add(moduleIdentifier)
      }
    }
    // used by ssr in case component is cached and beforeCreate
    // never gets called
    options._ssrRegister = hook
  } else if (injectStyles) {
    hook = injectStyles
  }

  if (hook) {
    var functional = options.functional
    var existing = functional
      ? options.render
      : options.beforeCreate

    if (!functional) {
      // inject component registration as beforeCreate hook
      options.beforeCreate = existing
        ? [].concat(existing, hook)
        : [hook]
    } else {
      // for template-only hot-reload because in that case the render fn doesn't
      // go through the normalizer
      options._injectStyles = hook
      // register for functioal component in vue file
      options.render = function renderWithStyleInjection (h, context) {
        hook.call(context)
        return existing(h, context)
      }
    }
  }

  return {
    esModule: esModule,
    exports: scriptExports,
    options: options
  }
}


/***/ }),
/* 1 */
/***/ (function(module, exports) {

/*
	MIT License http://www.opensource.org/licenses/mit-license.php
	Author Tobias Koppers @sokra
*/
// css base code, injected by the css-loader
module.exports = function(useSourceMap) {
	var list = [];

	// return the list of modules as css string
	list.toString = function toString() {
		return this.map(function (item) {
			var content = cssWithMappingToString(item, useSourceMap);
			if(item[2]) {
				return "@media " + item[2] + "{" + content + "}";
			} else {
				return content;
			}
		}).join("");
	};

	// import a list of modules into the list
	list.i = function(modules, mediaQuery) {
		if(typeof modules === "string")
			modules = [[null, modules, ""]];
		var alreadyImportedModules = {};
		for(var i = 0; i < this.length; i++) {
			var id = this[i][0];
			if(typeof id === "number")
				alreadyImportedModules[id] = true;
		}
		for(i = 0; i < modules.length; i++) {
			var item = modules[i];
			// skip already imported module
			// this implementation is not 100% perfect for weird media query combinations
			//  when a module is imported multiple times with different media queries.
			//  I hope this will never occur (Hey this way we have smaller bundles)
			if(typeof item[0] !== "number" || !alreadyImportedModules[item[0]]) {
				if(mediaQuery && !item[2]) {
					item[2] = mediaQuery;
				} else if(mediaQuery) {
					item[2] = "(" + item[2] + ") and (" + mediaQuery + ")";
				}
				list.push(item);
			}
		}
	};
	return list;
};

function cssWithMappingToString(item, useSourceMap) {
	var content = item[1] || '';
	var cssMapping = item[3];
	if (!cssMapping) {
		return content;
	}

	if (useSourceMap && typeof btoa === 'function') {
		var sourceMapping = toComment(cssMapping);
		var sourceURLs = cssMapping.sources.map(function (source) {
			return '/*# sourceURL=' + cssMapping.sourceRoot + source + ' */'
		});

		return [content].concat(sourceURLs).concat([sourceMapping]).join('\n');
	}

	return [content].join('\n');
}

// Adapted from convert-source-map (MIT)
function toComment(sourceMap) {
	// eslint-disable-next-line no-undef
	var base64 = btoa(unescape(encodeURIComponent(JSON.stringify(sourceMap))));
	var data = 'sourceMappingURL=data:application/json;charset=utf-8;base64,' + base64;

	return '/*# ' + data + ' */';
}


/***/ }),
/* 2 */
/***/ (function(module, exports, __webpack_require__) {

/*
  MIT License http://www.opensource.org/licenses/mit-license.php
  Author Tobias Koppers @sokra
  Modified by Evan You @yyx990803
*/

var hasDocument = typeof document !== 'undefined'

if (typeof DEBUG !== 'undefined' && DEBUG) {
  if (!hasDocument) {
    throw new Error(
    'vue-style-loader cannot be used in a non-browser environment. ' +
    "Use { target: 'node' } in your Webpack config to indicate a server-rendering environment."
  ) }
}

var listToStyles = __webpack_require__(10)

/*
type StyleObject = {
  id: number;
  parts: Array<StyleObjectPart>
}

type StyleObjectPart = {
  css: string;
  media: string;
  sourceMap: ?string
}
*/

var stylesInDom = {/*
  [id: number]: {
    id: number,
    refs: number,
    parts: Array<(obj?: StyleObjectPart) => void>
  }
*/}

var head = hasDocument && (document.head || document.getElementsByTagName('head')[0])
var singletonElement = null
var singletonCounter = 0
var isProduction = false
var noop = function () {}
var options = null
var ssrIdKey = 'data-vue-ssr-id'

// Force single-tag solution on IE6-9, which has a hard limit on the # of <style>
// tags it will allow on a page
var isOldIE = typeof navigator !== 'undefined' && /msie [6-9]\b/.test(navigator.userAgent.toLowerCase())

module.exports = function (parentId, list, _isProduction, _options) {
  isProduction = _isProduction

  options = _options || {}

  var styles = listToStyles(parentId, list)
  addStylesToDom(styles)

  return function update (newList) {
    var mayRemove = []
    for (var i = 0; i < styles.length; i++) {
      var item = styles[i]
      var domStyle = stylesInDom[item.id]
      domStyle.refs--
      mayRemove.push(domStyle)
    }
    if (newList) {
      styles = listToStyles(parentId, newList)
      addStylesToDom(styles)
    } else {
      styles = []
    }
    for (var i = 0; i < mayRemove.length; i++) {
      var domStyle = mayRemove[i]
      if (domStyle.refs === 0) {
        for (var j = 0; j < domStyle.parts.length; j++) {
          domStyle.parts[j]()
        }
        delete stylesInDom[domStyle.id]
      }
    }
  }
}

function addStylesToDom (styles /* Array<StyleObject> */) {
  for (var i = 0; i < styles.length; i++) {
    var item = styles[i]
    var domStyle = stylesInDom[item.id]
    if (domStyle) {
      domStyle.refs++
      for (var j = 0; j < domStyle.parts.length; j++) {
        domStyle.parts[j](item.parts[j])
      }
      for (; j < item.parts.length; j++) {
        domStyle.parts.push(addStyle(item.parts[j]))
      }
      if (domStyle.parts.length > item.parts.length) {
        domStyle.parts.length = item.parts.length
      }
    } else {
      var parts = []
      for (var j = 0; j < item.parts.length; j++) {
        parts.push(addStyle(item.parts[j]))
      }
      stylesInDom[item.id] = { id: item.id, refs: 1, parts: parts }
    }
  }
}

function createStyleElement () {
  var styleElement = document.createElement('style')
  styleElement.type = 'text/css'
  head.appendChild(styleElement)
  return styleElement
}

function addStyle (obj /* StyleObjectPart */) {
  var update, remove
  var styleElement = document.querySelector('style[' + ssrIdKey + '~="' + obj.id + '"]')

  if (styleElement) {
    if (isProduction) {
      // has SSR styles and in production mode.
      // simply do nothing.
      return noop
    } else {
      // has SSR styles but in dev mode.
      // for some reason Chrome can't handle source map in server-rendered
      // style tags - source maps in <style> only works if the style tag is
      // created and inserted dynamically. So we remove the server rendered
      // styles and inject new ones.
      styleElement.parentNode.removeChild(styleElement)
    }
  }

  if (isOldIE) {
    // use singleton mode for IE9.
    var styleIndex = singletonCounter++
    styleElement = singletonElement || (singletonElement = createStyleElement())
    update = applyToSingletonTag.bind(null, styleElement, styleIndex, false)
    remove = applyToSingletonTag.bind(null, styleElement, styleIndex, true)
  } else {
    // use multi-style-tag mode in all other cases
    styleElement = createStyleElement()
    update = applyToTag.bind(null, styleElement)
    remove = function () {
      styleElement.parentNode.removeChild(styleElement)
    }
  }

  update(obj)

  return function updateStyle (newObj /* StyleObjectPart */) {
    if (newObj) {
      if (newObj.css === obj.css &&
          newObj.media === obj.media &&
          newObj.sourceMap === obj.sourceMap) {
        return
      }
      update(obj = newObj)
    } else {
      remove()
    }
  }
}

var replaceText = (function () {
  var textStore = []

  return function (index, replacement) {
    textStore[index] = replacement
    return textStore.filter(Boolean).join('\n')
  }
})()

function applyToSingletonTag (styleElement, index, remove, obj) {
  var css = remove ? '' : obj.css

  if (styleElement.styleSheet) {
    styleElement.styleSheet.cssText = replaceText(index, css)
  } else {
    var cssNode = document.createTextNode(css)
    var childNodes = styleElement.childNodes
    if (childNodes[index]) styleElement.removeChild(childNodes[index])
    if (childNodes.length) {
      styleElement.insertBefore(cssNode, childNodes[index])
    } else {
      styleElement.appendChild(cssNode)
    }
  }
}

function applyToTag (styleElement, obj) {
  var css = obj.css
  var media = obj.media
  var sourceMap = obj.sourceMap

  if (media) {
    styleElement.setAttribute('media', media)
  }
  if (options.ssrId) {
    styleElement.setAttribute(ssrIdKey, obj.id)
  }

  if (sourceMap) {
    // https://developer.chrome.com/devtools/docs/javascript-debugging
    // this makes source maps inside style tags work properly in Chrome
    css += '\n/*# sourceURL=' + sourceMap.sources[0] + ' */'
    // http://stackoverflow.com/a/26603875
    css += '\n/*# sourceMappingURL=data:application/json;base64,' + btoa(unescape(encodeURIComponent(JSON.stringify(sourceMap)))) + ' */'
  }

  if (styleElement.styleSheet) {
    styleElement.styleSheet.cssText = css
  } else {
    while (styleElement.firstChild) {
      styleElement.removeChild(styleElement.firstChild)
    }
    styleElement.appendChild(document.createTextNode(css))
  }
}


/***/ }),
/* 3 */
/***/ (function(module, exports, __webpack_require__) {

__webpack_require__(4);
module.exports = __webpack_require__(24);


/***/ }),
/* 4 */
/***/ (function(module, exports, __webpack_require__) {

Nova.booting(function (Vue, router, store) {
  router.addRoutes([{
    name: 'qr-codes',
    path: '/qr-codes',
    component: __webpack_require__(5)
  }]);
});

/***/ }),
/* 5 */
/***/ (function(module, exports, __webpack_require__) {

var disposed = false
var normalizeComponent = __webpack_require__(0)
/* script */
var __vue_script__ = __webpack_require__(6)
/* template */
var __vue_template__ = __webpack_require__(23)
/* template functional */
var __vue_template_functional__ = false
/* styles */
var __vue_styles__ = null
/* scopeId */
var __vue_scopeId__ = null
/* moduleIdentifier (server only) */
var __vue_module_identifier__ = null
var Component = normalizeComponent(
  __vue_script__,
  __vue_template__,
  __vue_template_functional__,
  __vue_styles__,
  __vue_scopeId__,
  __vue_module_identifier__
)
Component.options.__file = "resources/js/components/Tool.vue"

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-68ff5483", Component.options)
  } else {
    hotAPI.reload("data-v-68ff5483", Component.options)
  }
  module.hot.dispose(function (data) {
    disposed = true
  })
})()}

module.exports = Component.exports


/***/ }),
/* 6 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
Object.defineProperty(__webpack_exports__, "__esModule", { value: true });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__Codes__ = __webpack_require__(7);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__Codes___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_0__Codes__);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__NewCode__ = __webpack_require__(13);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__NewCode___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_1__NewCode__);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_2__ConfirmModal__ = __webpack_require__(18);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_2__ConfirmModal___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_2__ConfirmModal__);
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//






/* harmony default export */ __webpack_exports__["default"] = ({
    data: function data() {
        return {
            codes: [],
            menus: [],
            user: [],
            link: '',
            hidden_titles: [],
            modalOpen: false,
            temp_id: ''
        };
    },

    components: {
        Codes: __WEBPACK_IMPORTED_MODULE_0__Codes___default.a,
        NewCode: __WEBPACK_IMPORTED_MODULE_1__NewCode___default.a,
        ConfirmModal: __WEBPACK_IMPORTED_MODULE_2__ConfirmModal___default.a
    },

    mounted: function mounted() {
        this.getCodes();
    },

    methods: {
        getCodes: function getCodes() {
            var _this = this;

            Nova.request().get('/nova-vendor/qr-codes/codes').then(function (response) {
                _this.codes = response.data['codes'];
                _this.menus = response.data['menus'];
                _this.user = response.data['user'];
                _this.link = response.data['link'];
            });
        },
        deleteCode: function deleteCode(code_id) {
            var _this2 = this;

            Nova.request().post('/nova-vendor/qr-codes/delete-code', {
                code_id: code_id
            }).then(function (response) {
                if (response.data == 'success') {
                    _this2.$toasted.show('Code deleted!', { type: 'success' });
                    _this2.getCodes();
                } else {
                    _this2.$toasted.show('Error!', { type: 'error' });
                }
            });
        },
        openModal: function openModal() {
            this.modalOpen = true;
        },
        confirmModal: function confirmModal() {
            if (this.temp_id) {
                this.deleteCode(this.temp_id);
                this.temp_id = '';
                this.modalOpen = false;
            }
        },
        closeModal: function closeModal() {
            if (this.temp_id) {
                this.temp_id = '';
                this.modalOpen = false;
            }
        }
    }
});

/***/ }),
/* 7 */
/***/ (function(module, exports, __webpack_require__) {

var disposed = false
function injectStyle (ssrContext) {
  if (disposed) return
  __webpack_require__(8)
}
var normalizeComponent = __webpack_require__(0)
/* script */
var __vue_script__ = __webpack_require__(11)
/* template */
var __vue_template__ = __webpack_require__(12)
/* template functional */
var __vue_template_functional__ = false
/* styles */
var __vue_styles__ = injectStyle
/* scopeId */
var __vue_scopeId__ = "data-v-df95f46a"
/* moduleIdentifier (server only) */
var __vue_module_identifier__ = null
var Component = normalizeComponent(
  __vue_script__,
  __vue_template__,
  __vue_template_functional__,
  __vue_styles__,
  __vue_scopeId__,
  __vue_module_identifier__
)
Component.options.__file = "resources/js/components/Codes.vue"

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-df95f46a", Component.options)
  } else {
    hotAPI.reload("data-v-df95f46a", Component.options)
  }
  module.hot.dispose(function (data) {
    disposed = true
  })
})()}

module.exports = Component.exports


/***/ }),
/* 8 */
/***/ (function(module, exports, __webpack_require__) {

// style-loader: Adds some css to the DOM by adding a <style> tag

// load the styles
var content = __webpack_require__(9);
if(typeof content === 'string') content = [[module.i, content, '']];
if(content.locals) module.exports = content.locals;
// add the styles to the DOM
var update = __webpack_require__(2)("523537e2", content, false, {});
// Hot Module Replacement
if(false) {
 // When the styles change, update the <style> tags
 if(!content.locals) {
   module.hot.accept("!!../../../node_modules/css-loader/index.js!../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-df95f46a\",\"scoped\":true,\"hasInlineConfig\":true}!../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./Codes.vue", function() {
     var newContent = require("!!../../../node_modules/css-loader/index.js!../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-df95f46a\",\"scoped\":true,\"hasInlineConfig\":true}!../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./Codes.vue");
     if(typeof newContent === 'string') newContent = [[module.id, newContent, '']];
     update(newContent);
   });
 }
 // When the module is disposed, remove the <style> tags
 module.hot.dispose(function() { update(); });
}

/***/ }),
/* 9 */
/***/ (function(module, exports, __webpack_require__) {

exports = module.exports = __webpack_require__(1)(false);
// imports


// module
exports.push([module.i, "\n.link_on_menu[data-v-df95f46a]:link, .link_on_menu[data-v-df95f46a]:visited {\n    color: gray;\n    text-decoration: none;\n}\n.qr_code[data-v-df95f46a] {\n    border: 13px solid black;\n    border-radius: 30px;\n}\n.buttons_class[data-v-df95f46a] {\n    vertical-align: bottom;\n    display: -webkit-box;\n    display: -ms-flexbox;\n    display: flex;\n}\n.buttons_class button[data-v-df95f46a] {\n    width: 150px;\n    -webkit-box-flex: 1;\n        -ms-flex: 1;\n            flex: 1;\n}\n\n\n", ""]);

// exports


/***/ }),
/* 10 */
/***/ (function(module, exports) {

/**
 * Translates the list format produced by css-loader into something
 * easier to manipulate.
 */
module.exports = function listToStyles (parentId, list) {
  var styles = []
  var newStyles = {}
  for (var i = 0; i < list.length; i++) {
    var item = list[i]
    var id = item[0]
    var css = item[1]
    var media = item[2]
    var sourceMap = item[3]
    var part = {
      id: parentId + ':' + i,
      css: css,
      media: media,
      sourceMap: sourceMap
    }
    if (!newStyles[id]) {
      styles.push(newStyles[id] = { id: id, parts: [part] })
    } else {
      newStyles[id].parts.push(part)
    }
  }
  return styles
}


/***/ }),
/* 11 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
Object.defineProperty(__webpack_exports__, "__esModule", { value: true });
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//

/* harmony default export */ __webpack_exports__["default"] = ({
    name: "Codes",
    props: {
        codes: [],
        user: [],
        link: '',
        hidden_titles: []
    },
    methods: {
        saveCodeChanges: function saveCodeChanges(code_id, event) {
            var _this = this;

            var form_elements = event.target.parentElement.parentElement.parentElement.elements;

            var data = {
                id: form_elements[0].value,
                show_navigation: form_elements[1].checked ? 1 : 0
            };

            Nova.request().post('/nova-vendor/qr-codes/save-code-changes', data).then(function (response) {
                _this.$parent.getCodes();
                _this.$toasted.show('Code updated!', { type: 'success' });
            });
        },
        openDeleteModal: function openDeleteModal(code_id) {
            this.$parent.temp_id = code_id;
            this.$parent.openModal();
        },
        editCodeName: function editCodeName(code_id) {
            this.hidden_titles.push(code_id);
        },
        checkInArr: function checkInArr(code_id) {
            return this.hidden_titles.includes(code_id);
        },
        downloadImg: function downloadImg(url, restaurant) {
            var link = document.createElement('a');
            link.href = url;
            link.download = restaurant + '.jpg';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }
    }

});

/***/ }),
/* 12 */
/***/ (function(module, exports, __webpack_require__) {

var render = function() {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _c(
    "card",
    { staticClass: "p-6" },
    [
      _c("heading", {}, [_vm._v(_vm._s(_vm.__("Existing QR Codes")))]),
      _vm._v(" "),
      _c(
        "div",
        { staticClass: "overflow-hidden overflow-x-auto relative m-6" },
        _vm._l(_vm.codes, function(code) {
          return _c("div", { staticClass: "m-6" }, [
            _c("form", { attrs: { id: "edit_form" } }, [
              _c("input", {
                directives: [
                  {
                    name: "model",
                    rawName: "v-model",
                    value: code.id,
                    expression: "code.id"
                  }
                ],
                attrs: { type: "text", name: "hidden_id", hidden: "hidden" },
                domProps: { value: code.id },
                on: {
                  input: function($event) {
                    if ($event.target.composing) {
                      return
                    }
                    _vm.$set(code, "id", $event.target.value)
                  }
                }
              }),
              _vm._v(" "),
              _c("div", [
                _c("h2", [
                  _vm._v(
                    "\n                        " +
                      _vm._s(code.name) +
                      "\n                    "
                  )
                ]),
                _vm._v(" "),
                _c(
                  "a",
                  {
                    staticClass: "link_on_menu",
                    attrs: {
                      target: "_blank",
                      href:
                        _vm.link +
                        code.menu.id +
                        "?show_navigation=" +
                        code.show_navigation
                    }
                  },
                  [
                    _vm._v(
                      "\n                        " +
                        _vm._s(_vm.link + code.menu.id) +
                        "\n                    "
                    )
                  ]
                )
              ]),
              _vm._v(" "),
              _c("div", { staticClass: "inline-block relative w-64 mt-2" }, [
                _c("img", {
                  staticClass: "qr_code",
                  attrs: {
                    src: /qr-codes/ + code.link_name + ".png",
                    width: "150px",
                    alt: ""
                  }
                })
              ]),
              _vm._v(" "),
              _c(
                "div",
                { staticClass: "inline-block relative w-64 mt-6 ml-6" },
                [
                  _c("div", { staticStyle: { "vertical-align": "text-top" } }, [
                    _c("label", [
                      _c("input", {
                        directives: [
                          {
                            name: "model",
                            rawName: "v-model",
                            value: code.show_navigation,
                            expression: "code.show_navigation"
                          }
                        ],
                        staticClass:
                          "bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mb-11",
                        attrs: {
                          type: "checkbox",
                          "true-value": "1",
                          "false-value": "0",
                          name: "show_navigation"
                        },
                        domProps: {
                          value: code.show_navigation,
                          checked: Array.isArray(code.show_navigation)
                            ? _vm._i(
                                code.show_navigation,
                                code.show_navigation
                              ) > -1
                            : _vm._q(code.show_navigation, "1")
                        },
                        on: {
                          change: function($event) {
                            var $$a = code.show_navigation,
                              $$el = $event.target,
                              $$c = $$el.checked ? "1" : "0"
                            if (Array.isArray($$a)) {
                              var $$v = code.show_navigation,
                                $$i = _vm._i($$a, $$v)
                              if ($$el.checked) {
                                $$i < 0 &&
                                  _vm.$set(
                                    code,
                                    "show_navigation",
                                    $$a.concat([$$v])
                                  )
                              } else {
                                $$i > -1 &&
                                  _vm.$set(
                                    code,
                                    "show_navigation",
                                    $$a.slice(0, $$i).concat($$a.slice($$i + 1))
                                  )
                              }
                            } else {
                              _vm.$set(code, "show_navigation", $$c)
                            }
                          }
                        }
                      }),
                      _vm._v(
                        "\n                            " +
                          _vm._s(_vm.__("Show Navigation"))
                      )
                    ])
                  ]),
                  _vm._v(" "),
                  _c("div", { staticClass: "buttons_class" }, [
                    _c(
                      "button",
                      {
                        staticClass:
                          "bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 mr-4 px-4 rounded",
                        staticStyle: {
                          padding: "10px",
                          "background-color": "gray",
                          color: "white"
                        },
                        attrs: { type: "button" },
                        on: {
                          click: function($event) {
                            return _vm.saveCodeChanges(code.id, $event)
                          }
                        }
                      },
                      [
                        _vm._v(
                          "\n                            " +
                            _vm._s(_vm.__("Save")) +
                            "\n                        "
                        )
                      ]
                    ),
                    _vm._v(" "),
                    _c(
                      "button",
                      {
                        staticClass:
                          "bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 mr-4 px-4 rounded",
                        staticStyle: {
                          padding: "10px",
                          "background-color": "lightgray",
                          color: "white"
                        },
                        attrs: { type: "button" },
                        on: {
                          click: function($event) {
                            return _vm.downloadImg(
                              "/qr-codes/" + code.link_name + ".png",
                              code.link_name
                            )
                          }
                        }
                      },
                      [
                        _vm._v(
                          "\n                            " +
                            _vm._s(_vm.__("Download Image")) +
                            "\n                        "
                        )
                      ]
                    ),
                    _vm._v(" "),
                    _c(
                      "button",
                      {
                        staticClass:
                          "bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded",
                        staticStyle: {
                          padding: "10px",
                          "background-color": "lightgray",
                          color: "white"
                        },
                        attrs: { type: "button" },
                        on: {
                          click: function($event) {
                            return _vm.openDeleteModal(code.id)
                          }
                        }
                      },
                      [
                        _vm._v(
                          "\n                            " +
                            _vm._s(_vm.__("Delete")) +
                            "\n                        "
                        )
                      ]
                    )
                  ])
                ]
              )
            ])
          ])
        }),
        0
      )
    ],
    1
  )
}
var staticRenderFns = []
render._withStripped = true
module.exports = { render: render, staticRenderFns: staticRenderFns }
if (false) {
  module.hot.accept()
  if (module.hot.data) {
    require("vue-hot-reload-api")      .rerender("data-v-df95f46a", module.exports)
  }
}

/***/ }),
/* 13 */
/***/ (function(module, exports, __webpack_require__) {

var disposed = false
function injectStyle (ssrContext) {
  if (disposed) return
  __webpack_require__(14)
}
var normalizeComponent = __webpack_require__(0)
/* script */
var __vue_script__ = __webpack_require__(16)
/* template */
var __vue_template__ = __webpack_require__(17)
/* template functional */
var __vue_template_functional__ = false
/* styles */
var __vue_styles__ = injectStyle
/* scopeId */
var __vue_scopeId__ = "data-v-22acf2dc"
/* moduleIdentifier (server only) */
var __vue_module_identifier__ = null
var Component = normalizeComponent(
  __vue_script__,
  __vue_template__,
  __vue_template_functional__,
  __vue_styles__,
  __vue_scopeId__,
  __vue_module_identifier__
)
Component.options.__file = "resources/js/components/NewCode.vue"

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-22acf2dc", Component.options)
  } else {
    hotAPI.reload("data-v-22acf2dc", Component.options)
  }
  module.hot.dispose(function (data) {
    disposed = true
  })
})()}

module.exports = Component.exports


/***/ }),
/* 14 */
/***/ (function(module, exports, __webpack_require__) {

// style-loader: Adds some css to the DOM by adding a <style> tag

// load the styles
var content = __webpack_require__(15);
if(typeof content === 'string') content = [[module.i, content, '']];
if(content.locals) module.exports = content.locals;
// add the styles to the DOM
var update = __webpack_require__(2)("2c98534c", content, false, {});
// Hot Module Replacement
if(false) {
 // When the styles change, update the <style> tags
 if(!content.locals) {
   module.hot.accept("!!../../../node_modules/css-loader/index.js!../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-22acf2dc\",\"scoped\":true,\"hasInlineConfig\":true}!../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./NewCode.vue", function() {
     var newContent = require("!!../../../node_modules/css-loader/index.js!../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-22acf2dc\",\"scoped\":true,\"hasInlineConfig\":true}!../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./NewCode.vue");
     if(typeof newContent === 'string') newContent = [[module.id, newContent, '']];
     update(newContent);
   });
 }
 // When the module is disposed, remove the <style> tags
 module.hot.dispose(function() { update(); });
}

/***/ }),
/* 15 */
/***/ (function(module, exports, __webpack_require__) {

exports = module.exports = __webpack_require__(1)(false);
// imports


// module
exports.push([module.i, "\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n", ""]);

// exports


/***/ }),
/* 16 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
Object.defineProperty(__webpack_exports__, "__esModule", { value: true });
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//

/* harmony default export */ __webpack_exports__["default"] = ({
    name: "NewCode",
    props: {
        menus: []
    },
    methods: {
        createNewCode: function createNewCode() {
            var _this = this;

            var create_form = document.getElementById('create_form');

            var form_elements = create_form.elements;

            var data = {
                name: form_elements[0].value,
                menu_id: form_elements[1].value,
                show_navigation: form_elements[2].checked ? 1 : 0
                // console.log(data.show_navigation)
            };Nova.request().post('/nova-vendor/qr-codes/create-new-code', data).then(function (response) {
                if (response.data == 'success') {
                    _this.$toasted.show('Code saved!', { type: 'success' });
                    _this.$parent.getCodes();
                    _this.cancelButton();
                } else {
                    _this.$toasted.show('Code with that name already exist!', { type: 'error' });
                }
            });
        },
        cancelButton: function cancelButton() {
            var form_elements = document.getElementById('create_form').elements;
            for (var i = 0; i <= 3; i++) {
                form_elements[i].value = '';
            }
            this.$toasted.show('Form cleared!', { type: 'info' });
        }
    }
});

/***/ }),
/* 17 */
/***/ (function(module, exports, __webpack_require__) {

var render = function() {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _c(
    "card",
    { staticClass: "p-6" },
    [
      _c("heading", {}, [_vm._v(_vm._s(_vm.__("Add New QR Code")))]),
      _vm._v(" "),
      _c(
        "div",
        { staticClass: "overflow-hidden overflow-x-auto relative m-6" },
        [
          _c(
            "form",
            {
              attrs: { id: "create_form" },
              on: {
                submit: function($event) {
                  $event.preventDefault()
                  return _vm.createNewCode($event)
                }
              }
            },
            [
              _c("div", {}, [
                _c("input", {
                  staticClass:
                    "leading-tight bg-white border border-gray-400 hover:border-gray-500 mb-6 px-4 py-2 pr-8 rounded shadow leading-tight focus:outline-none focus:shadow-outline",
                  attrs: {
                    name: "name",
                    placeholder: "Code Name",
                    required: ""
                  }
                })
              ]),
              _vm._v(" "),
              _c("div", { staticClass: "inline-block relative w-64" }, [
                _c("div", [
                  _c(
                    "select",
                    {
                      staticClass:
                        "block appearance-none w-full bg-white border border-gray-400 hover:border-gray-500 px-4 py-2 pr-8 rounded shadow leading-tight focus:outline-none focus:shadow-outline",
                      attrs: {
                        type: "text",
                        name: "menu_id",
                        placeholder: "menu_id",
                        required: ""
                      }
                    },
                    [
                      _c("option", { attrs: { value: "", disabled: "" } }, [
                        _vm._v(_vm._s(_vm.__("Linked Menu")))
                      ]),
                      _vm._v(" "),
                      _vm._l(_vm.menus, function(menu) {
                        return _c("option", { domProps: { value: menu.id } }, [
                          _vm._v(_vm._s(menu.name))
                        ])
                      })
                    ],
                    2
                  ),
                  _vm._v(" "),
                  _c(
                    "div",
                    {
                      staticClass:
                        "pointer-events-none absolute flex items-center px-2 text-gray-700",
                      staticStyle: {
                        position: "absolute",
                        right: "0",
                        top: "0",
                        bottom: "0"
                      }
                    },
                    [
                      _c(
                        "svg",
                        {
                          staticClass: "fill-current h-4 w-4",
                          attrs: {
                            xmlns: "http://www.w3.org/2000/svg",
                            viewBox: "0 0 20 20"
                          }
                        },
                        [
                          _c("path", {
                            attrs: {
                              d:
                                "M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"
                            }
                          })
                        ]
                      )
                    ]
                  )
                ])
              ]),
              _vm._v(" "),
              _c(
                "div",
                {
                  staticClass: "inline-block relative w-64 ml-4",
                  staticStyle: { "vertical-align": "text-top" }
                },
                [
                  _c("label", [
                    _c("input", {
                      staticClass:
                        "bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mb-11",
                      attrs: { type: "checkbox", name: "show_navigation" }
                    }),
                    _vm._v(
                      "\n                    " +
                        _vm._s(_vm.__("Show Navigation")) +
                        "\n                "
                    )
                  ])
                ]
              ),
              _vm._v(" "),
              _c("div", [
                _c(
                  "button",
                  {
                    staticClass:
                      "bg-blue-500 hover:bg-blue-700 text-white mr-4 font-bold py-2 px-4 rounded",
                    staticStyle: {
                      padding: "10px",
                      "background-color": "gray",
                      color: "white"
                    },
                    attrs: { type: "submit" }
                  },
                  [
                    _vm._v(
                      "\n                    " +
                        _vm._s(_vm.__("Create QR Code")) +
                        "\n                "
                    )
                  ]
                ),
                _vm._v(" "),
                _c(
                  "button",
                  {
                    staticClass:
                      "bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded",
                    staticStyle: {
                      padding: "10px",
                      "background-color": "lightgray",
                      color: "white"
                    },
                    attrs: { type: "button" },
                    on: {
                      click: function($event) {
                        return _vm.cancelButton()
                      }
                    }
                  },
                  [
                    _vm._v(
                      "\n                    " +
                        _vm._s(_vm.__("Cancel")) +
                        "\n                "
                    )
                  ]
                )
              ])
            ]
          )
        ]
      )
    ],
    1
  )
}
var staticRenderFns = []
render._withStripped = true
module.exports = { render: render, staticRenderFns: staticRenderFns }
if (false) {
  module.hot.accept()
  if (module.hot.data) {
    require("vue-hot-reload-api")      .rerender("data-v-22acf2dc", module.exports)
  }
}

/***/ }),
/* 18 */
/***/ (function(module, exports, __webpack_require__) {

var disposed = false
function injectStyle (ssrContext) {
  if (disposed) return
  __webpack_require__(19)
}
var normalizeComponent = __webpack_require__(0)
/* script */
var __vue_script__ = __webpack_require__(21)
/* template */
var __vue_template__ = __webpack_require__(22)
/* template functional */
var __vue_template_functional__ = false
/* styles */
var __vue_styles__ = injectStyle
/* scopeId */
var __vue_scopeId__ = "data-v-23992138"
/* moduleIdentifier (server only) */
var __vue_module_identifier__ = null
var Component = normalizeComponent(
  __vue_script__,
  __vue_template__,
  __vue_template_functional__,
  __vue_styles__,
  __vue_scopeId__,
  __vue_module_identifier__
)
Component.options.__file = "resources/js/components/ConfirmModal.vue"

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-23992138", Component.options)
  } else {
    hotAPI.reload("data-v-23992138", Component.options)
  }
  module.hot.dispose(function (data) {
    disposed = true
  })
})()}

module.exports = Component.exports


/***/ }),
/* 19 */
/***/ (function(module, exports, __webpack_require__) {

// style-loader: Adds some css to the DOM by adding a <style> tag

// load the styles
var content = __webpack_require__(20);
if(typeof content === 'string') content = [[module.i, content, '']];
if(content.locals) module.exports = content.locals;
// add the styles to the DOM
var update = __webpack_require__(2)("50946536", content, false, {});
// Hot Module Replacement
if(false) {
 // When the styles change, update the <style> tags
 if(!content.locals) {
   module.hot.accept("!!../../../node_modules/css-loader/index.js!../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-23992138\",\"scoped\":true,\"hasInlineConfig\":true}!../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./ConfirmModal.vue", function() {
     var newContent = require("!!../../../node_modules/css-loader/index.js!../../../node_modules/vue-loader/lib/style-compiler/index.js?{\"vue\":true,\"id\":\"data-v-23992138\",\"scoped\":true,\"hasInlineConfig\":true}!../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./ConfirmModal.vue");
     if(typeof newContent === 'string') newContent = [[module.id, newContent, '']];
     update(newContent);
   });
 }
 // When the module is disposed, remove the <style> tags
 module.hot.dispose(function() { update(); });
}

/***/ }),
/* 20 */
/***/ (function(module, exports, __webpack_require__) {

exports = module.exports = __webpack_require__(1)(false);
// imports


// module
exports.push([module.i, "\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n", ""]);

// exports


/***/ }),
/* 21 */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
Object.defineProperty(__webpack_exports__, "__esModule", { value: true });
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//

/* harmony default export */ __webpack_exports__["default"] = ({
    name: "ConfirmModal",
    /**
     * Mount the component.
     */
    mounted: function mounted() {
        this.$refs.confirmButton.focus();
    },

    methods: {
        /**
         * Execute the selected action.
         */
        handleConfirm: function handleConfirm() {
            this.$emit('confirm');
        },

        /**
         * Close the modal.
         */
        handleClose: function handleClose() {
            this.$emit('close');
        }
    }
});

/***/ }),
/* 22 */
/***/ (function(module, exports, __webpack_require__) {

var render = function() {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _c("modal", {
    on: { "modal-close": _vm.handleClose },
    scopedSlots: _vm._u(
      [
        {
          key: "default",
          fn: function(props) {
            return _c(
              "form",
              {
                staticClass: "bg-white rounded-lg shadow-lg overflow-hidden",
                staticStyle: { width: "460px" },
                on: {
                  submit: function($event) {
                    $event.preventDefault()
                    return _vm.handleConfirm($event)
                  }
                }
              },
              [
                _vm._t(
                  "default",
                  [
                    _c(
                      "div",
                      { staticClass: "p-8" },
                      [
                        _c(
                          "heading",
                          { staticClass: "mb-6", attrs: { level: 2 } },
                          [_vm._v(_vm._s(_vm.__("Delete QR Code")))]
                        ),
                        _vm._v(" "),
                        _c("p", { staticClass: "text-80 leading-normal" }, [
                          _vm._v(
                            _vm._s(
                              _vm.__(
                                "Are you sure you want to delete this QR Code?"
                              )
                            )
                          )
                        ])
                      ],
                      1
                    )
                  ],
                  { uppercaseMode: _vm.uppercaseMode, mode: _vm.mode }
                ),
                _vm._v(" "),
                _c("div", { staticClass: "bg-30 px-6 py-3 flex" }, [
                  _c("div", { staticClass: "ml-auto" }, [
                    _c(
                      "button",
                      {
                        staticClass:
                          "btn text-80 font-normal h-9 px-3 mr-3 btn-link",
                        attrs: {
                          type: "button",
                          "data-testid": "cancel-button",
                          dusk: "cancel-general-button"
                        },
                        on: {
                          click: function($event) {
                            $event.preventDefault()
                            return _vm.handleClose($event)
                          }
                        }
                      },
                      [_vm._v(_vm._s(_vm.__("Cancel")))]
                    ),
                    _vm._v(" "),
                    _c(
                      "button",
                      {
                        ref: "confirmButton",
                        staticClass: "btn btn-default btn-danger",
                        attrs: {
                          id: "confirm-delete-button",
                          "data-testid": "confirm-button",
                          type: "submit"
                        }
                      },
                      [_vm._v(_vm._s(_vm.__("Delete")))]
                    )
                  ])
                ])
              ],
              2
            )
          }
        }
      ],
      null,
      true
    )
  })
}
var staticRenderFns = []
render._withStripped = true
module.exports = { render: render, staticRenderFns: staticRenderFns }
if (false) {
  module.hot.accept()
  if (module.hot.data) {
    require("vue-hot-reload-api")      .rerender("data-v-23992138", module.exports)
  }
}

/***/ }),
/* 23 */
/***/ (function(module, exports, __webpack_require__) {

var render = function() {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _c(
    "div",
    [
      _c("heading", { staticClass: "mb-6" }, [_vm._v("QR Codes")]),
      _vm._v(" "),
      _c("Codes", {
        attrs: { codes: _vm.codes, user: _vm.user, link: _vm.link }
      }),
      _vm._v(" "),
      _c("hr"),
      _vm._v(" "),
      _c("NewCode", { attrs: { menus: _vm.menus } }),
      _vm._v(" "),
      _c(
        "portal",
        { attrs: { to: "modals" } },
        [
          _c(
            "transition",
            { attrs: { name: "fade" } },
            [
              _vm.modalOpen
                ? _c("ConfirmModal", {
                    on: { confirm: _vm.confirmModal, close: _vm.closeModal }
                  })
                : _vm._e()
            ],
            1
          )
        ],
        1
      )
    ],
    1
  )
}
var staticRenderFns = []
render._withStripped = true
module.exports = { render: render, staticRenderFns: staticRenderFns }
if (false) {
  module.hot.accept()
  if (module.hot.data) {
    require("vue-hot-reload-api")      .rerender("data-v-68ff5483", module.exports)
  }
}

/***/ }),
/* 24 */
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ })
/******/ ]);