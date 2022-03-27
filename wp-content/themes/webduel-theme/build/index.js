/******/ (function() { // webpackBootstrap
/******/ 	var __webpack_modules__ = ({

/***/ "./node_modules/axios/index.js":
/*!*************************************!*\
  !*** ./node_modules/axios/index.js ***!
  \*************************************/
/***/ (function(module, __unused_webpack_exports, __webpack_require__) {

module.exports = __webpack_require__(/*! ./lib/axios */ "./node_modules/axios/lib/axios.js");

/***/ }),

/***/ "./node_modules/axios/lib/adapters/xhr.js":
/*!************************************************!*\
  !*** ./node_modules/axios/lib/adapters/xhr.js ***!
  \************************************************/
/***/ (function(module, __unused_webpack_exports, __webpack_require__) {

"use strict";


var utils = __webpack_require__(/*! ./../utils */ "./node_modules/axios/lib/utils.js");
var settle = __webpack_require__(/*! ./../core/settle */ "./node_modules/axios/lib/core/settle.js");
var cookies = __webpack_require__(/*! ./../helpers/cookies */ "./node_modules/axios/lib/helpers/cookies.js");
var buildURL = __webpack_require__(/*! ./../helpers/buildURL */ "./node_modules/axios/lib/helpers/buildURL.js");
var buildFullPath = __webpack_require__(/*! ../core/buildFullPath */ "./node_modules/axios/lib/core/buildFullPath.js");
var parseHeaders = __webpack_require__(/*! ./../helpers/parseHeaders */ "./node_modules/axios/lib/helpers/parseHeaders.js");
var isURLSameOrigin = __webpack_require__(/*! ./../helpers/isURLSameOrigin */ "./node_modules/axios/lib/helpers/isURLSameOrigin.js");
var createError = __webpack_require__(/*! ../core/createError */ "./node_modules/axios/lib/core/createError.js");

module.exports = function xhrAdapter(config) {
  return new Promise(function dispatchXhrRequest(resolve, reject) {
    var requestData = config.data;
    var requestHeaders = config.headers;
    var responseType = config.responseType;

    if (utils.isFormData(requestData)) {
      delete requestHeaders['Content-Type']; // Let the browser set it
    }

    var request = new XMLHttpRequest();

    // HTTP basic authentication
    if (config.auth) {
      var username = config.auth.username || '';
      var password = config.auth.password ? unescape(encodeURIComponent(config.auth.password)) : '';
      requestHeaders.Authorization = 'Basic ' + btoa(username + ':' + password);
    }

    var fullPath = buildFullPath(config.baseURL, config.url);
    request.open(config.method.toUpperCase(), buildURL(fullPath, config.params, config.paramsSerializer), true);

    // Set the request timeout in MS
    request.timeout = config.timeout;

    function onloadend() {
      if (!request) {
        return;
      }
      // Prepare the response
      var responseHeaders = 'getAllResponseHeaders' in request ? parseHeaders(request.getAllResponseHeaders()) : null;
      var responseData = !responseType || responseType === 'text' ||  responseType === 'json' ?
        request.responseText : request.response;
      var response = {
        data: responseData,
        status: request.status,
        statusText: request.statusText,
        headers: responseHeaders,
        config: config,
        request: request
      };

      settle(resolve, reject, response);

      // Clean up request
      request = null;
    }

    if ('onloadend' in request) {
      // Use onloadend if available
      request.onloadend = onloadend;
    } else {
      // Listen for ready state to emulate onloadend
      request.onreadystatechange = function handleLoad() {
        if (!request || request.readyState !== 4) {
          return;
        }

        // The request errored out and we didn't get a response, this will be
        // handled by onerror instead
        // With one exception: request that using file: protocol, most browsers
        // will return status as 0 even though it's a successful request
        if (request.status === 0 && !(request.responseURL && request.responseURL.indexOf('file:') === 0)) {
          return;
        }
        // readystate handler is calling before onerror or ontimeout handlers,
        // so we should call onloadend on the next 'tick'
        setTimeout(onloadend);
      };
    }

    // Handle browser request cancellation (as opposed to a manual cancellation)
    request.onabort = function handleAbort() {
      if (!request) {
        return;
      }

      reject(createError('Request aborted', config, 'ECONNABORTED', request));

      // Clean up request
      request = null;
    };

    // Handle low level network errors
    request.onerror = function handleError() {
      // Real errors are hidden from us by the browser
      // onerror should only fire if it's a network error
      reject(createError('Network Error', config, null, request));

      // Clean up request
      request = null;
    };

    // Handle timeout
    request.ontimeout = function handleTimeout() {
      var timeoutErrorMessage = 'timeout of ' + config.timeout + 'ms exceeded';
      if (config.timeoutErrorMessage) {
        timeoutErrorMessage = config.timeoutErrorMessage;
      }
      reject(createError(
        timeoutErrorMessage,
        config,
        config.transitional && config.transitional.clarifyTimeoutError ? 'ETIMEDOUT' : 'ECONNABORTED',
        request));

      // Clean up request
      request = null;
    };

    // Add xsrf header
    // This is only done if running in a standard browser environment.
    // Specifically not if we're in a web worker, or react-native.
    if (utils.isStandardBrowserEnv()) {
      // Add xsrf header
      var xsrfValue = (config.withCredentials || isURLSameOrigin(fullPath)) && config.xsrfCookieName ?
        cookies.read(config.xsrfCookieName) :
        undefined;

      if (xsrfValue) {
        requestHeaders[config.xsrfHeaderName] = xsrfValue;
      }
    }

    // Add headers to the request
    if ('setRequestHeader' in request) {
      utils.forEach(requestHeaders, function setRequestHeader(val, key) {
        if (typeof requestData === 'undefined' && key.toLowerCase() === 'content-type') {
          // Remove Content-Type if data is undefined
          delete requestHeaders[key];
        } else {
          // Otherwise add header to the request
          request.setRequestHeader(key, val);
        }
      });
    }

    // Add withCredentials to request if needed
    if (!utils.isUndefined(config.withCredentials)) {
      request.withCredentials = !!config.withCredentials;
    }

    // Add responseType to request if needed
    if (responseType && responseType !== 'json') {
      request.responseType = config.responseType;
    }

    // Handle progress if needed
    if (typeof config.onDownloadProgress === 'function') {
      request.addEventListener('progress', config.onDownloadProgress);
    }

    // Not all browsers support upload events
    if (typeof config.onUploadProgress === 'function' && request.upload) {
      request.upload.addEventListener('progress', config.onUploadProgress);
    }

    if (config.cancelToken) {
      // Handle cancellation
      config.cancelToken.promise.then(function onCanceled(cancel) {
        if (!request) {
          return;
        }

        request.abort();
        reject(cancel);
        // Clean up request
        request = null;
      });
    }

    if (!requestData) {
      requestData = null;
    }

    // Send the request
    request.send(requestData);
  });
};


/***/ }),

/***/ "./node_modules/axios/lib/axios.js":
/*!*****************************************!*\
  !*** ./node_modules/axios/lib/axios.js ***!
  \*****************************************/
/***/ (function(module, __unused_webpack_exports, __webpack_require__) {

"use strict";


var utils = __webpack_require__(/*! ./utils */ "./node_modules/axios/lib/utils.js");
var bind = __webpack_require__(/*! ./helpers/bind */ "./node_modules/axios/lib/helpers/bind.js");
var Axios = __webpack_require__(/*! ./core/Axios */ "./node_modules/axios/lib/core/Axios.js");
var mergeConfig = __webpack_require__(/*! ./core/mergeConfig */ "./node_modules/axios/lib/core/mergeConfig.js");
var defaults = __webpack_require__(/*! ./defaults */ "./node_modules/axios/lib/defaults.js");

/**
 * Create an instance of Axios
 *
 * @param {Object} defaultConfig The default config for the instance
 * @return {Axios} A new instance of Axios
 */
function createInstance(defaultConfig) {
  var context = new Axios(defaultConfig);
  var instance = bind(Axios.prototype.request, context);

  // Copy axios.prototype to instance
  utils.extend(instance, Axios.prototype, context);

  // Copy context to instance
  utils.extend(instance, context);

  return instance;
}

// Create the default instance to be exported
var axios = createInstance(defaults);

// Expose Axios class to allow class inheritance
axios.Axios = Axios;

// Factory for creating new instances
axios.create = function create(instanceConfig) {
  return createInstance(mergeConfig(axios.defaults, instanceConfig));
};

// Expose Cancel & CancelToken
axios.Cancel = __webpack_require__(/*! ./cancel/Cancel */ "./node_modules/axios/lib/cancel/Cancel.js");
axios.CancelToken = __webpack_require__(/*! ./cancel/CancelToken */ "./node_modules/axios/lib/cancel/CancelToken.js");
axios.isCancel = __webpack_require__(/*! ./cancel/isCancel */ "./node_modules/axios/lib/cancel/isCancel.js");

// Expose all/spread
axios.all = function all(promises) {
  return Promise.all(promises);
};
axios.spread = __webpack_require__(/*! ./helpers/spread */ "./node_modules/axios/lib/helpers/spread.js");

// Expose isAxiosError
axios.isAxiosError = __webpack_require__(/*! ./helpers/isAxiosError */ "./node_modules/axios/lib/helpers/isAxiosError.js");

module.exports = axios;

// Allow use of default import syntax in TypeScript
module.exports["default"] = axios;


/***/ }),

/***/ "./node_modules/axios/lib/cancel/Cancel.js":
/*!*************************************************!*\
  !*** ./node_modules/axios/lib/cancel/Cancel.js ***!
  \*************************************************/
/***/ (function(module) {

"use strict";


/**
 * A `Cancel` is an object that is thrown when an operation is canceled.
 *
 * @class
 * @param {string=} message The message.
 */
function Cancel(message) {
  this.message = message;
}

Cancel.prototype.toString = function toString() {
  return 'Cancel' + (this.message ? ': ' + this.message : '');
};

Cancel.prototype.__CANCEL__ = true;

module.exports = Cancel;


/***/ }),

/***/ "./node_modules/axios/lib/cancel/CancelToken.js":
/*!******************************************************!*\
  !*** ./node_modules/axios/lib/cancel/CancelToken.js ***!
  \******************************************************/
/***/ (function(module, __unused_webpack_exports, __webpack_require__) {

"use strict";


var Cancel = __webpack_require__(/*! ./Cancel */ "./node_modules/axios/lib/cancel/Cancel.js");

/**
 * A `CancelToken` is an object that can be used to request cancellation of an operation.
 *
 * @class
 * @param {Function} executor The executor function.
 */
function CancelToken(executor) {
  if (typeof executor !== 'function') {
    throw new TypeError('executor must be a function.');
  }

  var resolvePromise;
  this.promise = new Promise(function promiseExecutor(resolve) {
    resolvePromise = resolve;
  });

  var token = this;
  executor(function cancel(message) {
    if (token.reason) {
      // Cancellation has already been requested
      return;
    }

    token.reason = new Cancel(message);
    resolvePromise(token.reason);
  });
}

/**
 * Throws a `Cancel` if cancellation has been requested.
 */
CancelToken.prototype.throwIfRequested = function throwIfRequested() {
  if (this.reason) {
    throw this.reason;
  }
};

/**
 * Returns an object that contains a new `CancelToken` and a function that, when called,
 * cancels the `CancelToken`.
 */
CancelToken.source = function source() {
  var cancel;
  var token = new CancelToken(function executor(c) {
    cancel = c;
  });
  return {
    token: token,
    cancel: cancel
  };
};

module.exports = CancelToken;


/***/ }),

/***/ "./node_modules/axios/lib/cancel/isCancel.js":
/*!***************************************************!*\
  !*** ./node_modules/axios/lib/cancel/isCancel.js ***!
  \***************************************************/
/***/ (function(module) {

"use strict";


module.exports = function isCancel(value) {
  return !!(value && value.__CANCEL__);
};


/***/ }),

/***/ "./node_modules/axios/lib/core/Axios.js":
/*!**********************************************!*\
  !*** ./node_modules/axios/lib/core/Axios.js ***!
  \**********************************************/
/***/ (function(module, __unused_webpack_exports, __webpack_require__) {

"use strict";


var utils = __webpack_require__(/*! ./../utils */ "./node_modules/axios/lib/utils.js");
var buildURL = __webpack_require__(/*! ../helpers/buildURL */ "./node_modules/axios/lib/helpers/buildURL.js");
var InterceptorManager = __webpack_require__(/*! ./InterceptorManager */ "./node_modules/axios/lib/core/InterceptorManager.js");
var dispatchRequest = __webpack_require__(/*! ./dispatchRequest */ "./node_modules/axios/lib/core/dispatchRequest.js");
var mergeConfig = __webpack_require__(/*! ./mergeConfig */ "./node_modules/axios/lib/core/mergeConfig.js");
var validator = __webpack_require__(/*! ../helpers/validator */ "./node_modules/axios/lib/helpers/validator.js");

var validators = validator.validators;
/**
 * Create a new instance of Axios
 *
 * @param {Object} instanceConfig The default config for the instance
 */
function Axios(instanceConfig) {
  this.defaults = instanceConfig;
  this.interceptors = {
    request: new InterceptorManager(),
    response: new InterceptorManager()
  };
}

/**
 * Dispatch a request
 *
 * @param {Object} config The config specific for this request (merged with this.defaults)
 */
Axios.prototype.request = function request(config) {
  /*eslint no-param-reassign:0*/
  // Allow for axios('example/url'[, config]) a la fetch API
  if (typeof config === 'string') {
    config = arguments[1] || {};
    config.url = arguments[0];
  } else {
    config = config || {};
  }

  config = mergeConfig(this.defaults, config);

  // Set config.method
  if (config.method) {
    config.method = config.method.toLowerCase();
  } else if (this.defaults.method) {
    config.method = this.defaults.method.toLowerCase();
  } else {
    config.method = 'get';
  }

  var transitional = config.transitional;

  if (transitional !== undefined) {
    validator.assertOptions(transitional, {
      silentJSONParsing: validators.transitional(validators.boolean, '1.0.0'),
      forcedJSONParsing: validators.transitional(validators.boolean, '1.0.0'),
      clarifyTimeoutError: validators.transitional(validators.boolean, '1.0.0')
    }, false);
  }

  // filter out skipped interceptors
  var requestInterceptorChain = [];
  var synchronousRequestInterceptors = true;
  this.interceptors.request.forEach(function unshiftRequestInterceptors(interceptor) {
    if (typeof interceptor.runWhen === 'function' && interceptor.runWhen(config) === false) {
      return;
    }

    synchronousRequestInterceptors = synchronousRequestInterceptors && interceptor.synchronous;

    requestInterceptorChain.unshift(interceptor.fulfilled, interceptor.rejected);
  });

  var responseInterceptorChain = [];
  this.interceptors.response.forEach(function pushResponseInterceptors(interceptor) {
    responseInterceptorChain.push(interceptor.fulfilled, interceptor.rejected);
  });

  var promise;

  if (!synchronousRequestInterceptors) {
    var chain = [dispatchRequest, undefined];

    Array.prototype.unshift.apply(chain, requestInterceptorChain);
    chain = chain.concat(responseInterceptorChain);

    promise = Promise.resolve(config);
    while (chain.length) {
      promise = promise.then(chain.shift(), chain.shift());
    }

    return promise;
  }


  var newConfig = config;
  while (requestInterceptorChain.length) {
    var onFulfilled = requestInterceptorChain.shift();
    var onRejected = requestInterceptorChain.shift();
    try {
      newConfig = onFulfilled(newConfig);
    } catch (error) {
      onRejected(error);
      break;
    }
  }

  try {
    promise = dispatchRequest(newConfig);
  } catch (error) {
    return Promise.reject(error);
  }

  while (responseInterceptorChain.length) {
    promise = promise.then(responseInterceptorChain.shift(), responseInterceptorChain.shift());
  }

  return promise;
};

Axios.prototype.getUri = function getUri(config) {
  config = mergeConfig(this.defaults, config);
  return buildURL(config.url, config.params, config.paramsSerializer).replace(/^\?/, '');
};

// Provide aliases for supported request methods
utils.forEach(['delete', 'get', 'head', 'options'], function forEachMethodNoData(method) {
  /*eslint func-names:0*/
  Axios.prototype[method] = function(url, config) {
    return this.request(mergeConfig(config || {}, {
      method: method,
      url: url,
      data: (config || {}).data
    }));
  };
});

utils.forEach(['post', 'put', 'patch'], function forEachMethodWithData(method) {
  /*eslint func-names:0*/
  Axios.prototype[method] = function(url, data, config) {
    return this.request(mergeConfig(config || {}, {
      method: method,
      url: url,
      data: data
    }));
  };
});

module.exports = Axios;


/***/ }),

/***/ "./node_modules/axios/lib/core/InterceptorManager.js":
/*!***********************************************************!*\
  !*** ./node_modules/axios/lib/core/InterceptorManager.js ***!
  \***********************************************************/
/***/ (function(module, __unused_webpack_exports, __webpack_require__) {

"use strict";


var utils = __webpack_require__(/*! ./../utils */ "./node_modules/axios/lib/utils.js");

function InterceptorManager() {
  this.handlers = [];
}

/**
 * Add a new interceptor to the stack
 *
 * @param {Function} fulfilled The function to handle `then` for a `Promise`
 * @param {Function} rejected The function to handle `reject` for a `Promise`
 *
 * @return {Number} An ID used to remove interceptor later
 */
InterceptorManager.prototype.use = function use(fulfilled, rejected, options) {
  this.handlers.push({
    fulfilled: fulfilled,
    rejected: rejected,
    synchronous: options ? options.synchronous : false,
    runWhen: options ? options.runWhen : null
  });
  return this.handlers.length - 1;
};

/**
 * Remove an interceptor from the stack
 *
 * @param {Number} id The ID that was returned by `use`
 */
InterceptorManager.prototype.eject = function eject(id) {
  if (this.handlers[id]) {
    this.handlers[id] = null;
  }
};

/**
 * Iterate over all the registered interceptors
 *
 * This method is particularly useful for skipping over any
 * interceptors that may have become `null` calling `eject`.
 *
 * @param {Function} fn The function to call for each interceptor
 */
InterceptorManager.prototype.forEach = function forEach(fn) {
  utils.forEach(this.handlers, function forEachHandler(h) {
    if (h !== null) {
      fn(h);
    }
  });
};

module.exports = InterceptorManager;


/***/ }),

/***/ "./node_modules/axios/lib/core/buildFullPath.js":
/*!******************************************************!*\
  !*** ./node_modules/axios/lib/core/buildFullPath.js ***!
  \******************************************************/
/***/ (function(module, __unused_webpack_exports, __webpack_require__) {

"use strict";


var isAbsoluteURL = __webpack_require__(/*! ../helpers/isAbsoluteURL */ "./node_modules/axios/lib/helpers/isAbsoluteURL.js");
var combineURLs = __webpack_require__(/*! ../helpers/combineURLs */ "./node_modules/axios/lib/helpers/combineURLs.js");

/**
 * Creates a new URL by combining the baseURL with the requestedURL,
 * only when the requestedURL is not already an absolute URL.
 * If the requestURL is absolute, this function returns the requestedURL untouched.
 *
 * @param {string} baseURL The base URL
 * @param {string} requestedURL Absolute or relative URL to combine
 * @returns {string} The combined full path
 */
module.exports = function buildFullPath(baseURL, requestedURL) {
  if (baseURL && !isAbsoluteURL(requestedURL)) {
    return combineURLs(baseURL, requestedURL);
  }
  return requestedURL;
};


/***/ }),

/***/ "./node_modules/axios/lib/core/createError.js":
/*!****************************************************!*\
  !*** ./node_modules/axios/lib/core/createError.js ***!
  \****************************************************/
/***/ (function(module, __unused_webpack_exports, __webpack_require__) {

"use strict";


var enhanceError = __webpack_require__(/*! ./enhanceError */ "./node_modules/axios/lib/core/enhanceError.js");

/**
 * Create an Error with the specified message, config, error code, request and response.
 *
 * @param {string} message The error message.
 * @param {Object} config The config.
 * @param {string} [code] The error code (for example, 'ECONNABORTED').
 * @param {Object} [request] The request.
 * @param {Object} [response] The response.
 * @returns {Error} The created error.
 */
module.exports = function createError(message, config, code, request, response) {
  var error = new Error(message);
  return enhanceError(error, config, code, request, response);
};


/***/ }),

/***/ "./node_modules/axios/lib/core/dispatchRequest.js":
/*!********************************************************!*\
  !*** ./node_modules/axios/lib/core/dispatchRequest.js ***!
  \********************************************************/
/***/ (function(module, __unused_webpack_exports, __webpack_require__) {

"use strict";


var utils = __webpack_require__(/*! ./../utils */ "./node_modules/axios/lib/utils.js");
var transformData = __webpack_require__(/*! ./transformData */ "./node_modules/axios/lib/core/transformData.js");
var isCancel = __webpack_require__(/*! ../cancel/isCancel */ "./node_modules/axios/lib/cancel/isCancel.js");
var defaults = __webpack_require__(/*! ../defaults */ "./node_modules/axios/lib/defaults.js");

/**
 * Throws a `Cancel` if cancellation has been requested.
 */
function throwIfCancellationRequested(config) {
  if (config.cancelToken) {
    config.cancelToken.throwIfRequested();
  }
}

/**
 * Dispatch a request to the server using the configured adapter.
 *
 * @param {object} config The config that is to be used for the request
 * @returns {Promise} The Promise to be fulfilled
 */
module.exports = function dispatchRequest(config) {
  throwIfCancellationRequested(config);

  // Ensure headers exist
  config.headers = config.headers || {};

  // Transform request data
  config.data = transformData.call(
    config,
    config.data,
    config.headers,
    config.transformRequest
  );

  // Flatten headers
  config.headers = utils.merge(
    config.headers.common || {},
    config.headers[config.method] || {},
    config.headers
  );

  utils.forEach(
    ['delete', 'get', 'head', 'post', 'put', 'patch', 'common'],
    function cleanHeaderConfig(method) {
      delete config.headers[method];
    }
  );

  var adapter = config.adapter || defaults.adapter;

  return adapter(config).then(function onAdapterResolution(response) {
    throwIfCancellationRequested(config);

    // Transform response data
    response.data = transformData.call(
      config,
      response.data,
      response.headers,
      config.transformResponse
    );

    return response;
  }, function onAdapterRejection(reason) {
    if (!isCancel(reason)) {
      throwIfCancellationRequested(config);

      // Transform response data
      if (reason && reason.response) {
        reason.response.data = transformData.call(
          config,
          reason.response.data,
          reason.response.headers,
          config.transformResponse
        );
      }
    }

    return Promise.reject(reason);
  });
};


/***/ }),

/***/ "./node_modules/axios/lib/core/enhanceError.js":
/*!*****************************************************!*\
  !*** ./node_modules/axios/lib/core/enhanceError.js ***!
  \*****************************************************/
/***/ (function(module) {

"use strict";


/**
 * Update an Error with the specified config, error code, and response.
 *
 * @param {Error} error The error to update.
 * @param {Object} config The config.
 * @param {string} [code] The error code (for example, 'ECONNABORTED').
 * @param {Object} [request] The request.
 * @param {Object} [response] The response.
 * @returns {Error} The error.
 */
module.exports = function enhanceError(error, config, code, request, response) {
  error.config = config;
  if (code) {
    error.code = code;
  }

  error.request = request;
  error.response = response;
  error.isAxiosError = true;

  error.toJSON = function toJSON() {
    return {
      // Standard
      message: this.message,
      name: this.name,
      // Microsoft
      description: this.description,
      number: this.number,
      // Mozilla
      fileName: this.fileName,
      lineNumber: this.lineNumber,
      columnNumber: this.columnNumber,
      stack: this.stack,
      // Axios
      config: this.config,
      code: this.code
    };
  };
  return error;
};


/***/ }),

/***/ "./node_modules/axios/lib/core/mergeConfig.js":
/*!****************************************************!*\
  !*** ./node_modules/axios/lib/core/mergeConfig.js ***!
  \****************************************************/
/***/ (function(module, __unused_webpack_exports, __webpack_require__) {

"use strict";


var utils = __webpack_require__(/*! ../utils */ "./node_modules/axios/lib/utils.js");

/**
 * Config-specific merge-function which creates a new config-object
 * by merging two configuration objects together.
 *
 * @param {Object} config1
 * @param {Object} config2
 * @returns {Object} New object resulting from merging config2 to config1
 */
module.exports = function mergeConfig(config1, config2) {
  // eslint-disable-next-line no-param-reassign
  config2 = config2 || {};
  var config = {};

  var valueFromConfig2Keys = ['url', 'method', 'data'];
  var mergeDeepPropertiesKeys = ['headers', 'auth', 'proxy', 'params'];
  var defaultToConfig2Keys = [
    'baseURL', 'transformRequest', 'transformResponse', 'paramsSerializer',
    'timeout', 'timeoutMessage', 'withCredentials', 'adapter', 'responseType', 'xsrfCookieName',
    'xsrfHeaderName', 'onUploadProgress', 'onDownloadProgress', 'decompress',
    'maxContentLength', 'maxBodyLength', 'maxRedirects', 'transport', 'httpAgent',
    'httpsAgent', 'cancelToken', 'socketPath', 'responseEncoding'
  ];
  var directMergeKeys = ['validateStatus'];

  function getMergedValue(target, source) {
    if (utils.isPlainObject(target) && utils.isPlainObject(source)) {
      return utils.merge(target, source);
    } else if (utils.isPlainObject(source)) {
      return utils.merge({}, source);
    } else if (utils.isArray(source)) {
      return source.slice();
    }
    return source;
  }

  function mergeDeepProperties(prop) {
    if (!utils.isUndefined(config2[prop])) {
      config[prop] = getMergedValue(config1[prop], config2[prop]);
    } else if (!utils.isUndefined(config1[prop])) {
      config[prop] = getMergedValue(undefined, config1[prop]);
    }
  }

  utils.forEach(valueFromConfig2Keys, function valueFromConfig2(prop) {
    if (!utils.isUndefined(config2[prop])) {
      config[prop] = getMergedValue(undefined, config2[prop]);
    }
  });

  utils.forEach(mergeDeepPropertiesKeys, mergeDeepProperties);

  utils.forEach(defaultToConfig2Keys, function defaultToConfig2(prop) {
    if (!utils.isUndefined(config2[prop])) {
      config[prop] = getMergedValue(undefined, config2[prop]);
    } else if (!utils.isUndefined(config1[prop])) {
      config[prop] = getMergedValue(undefined, config1[prop]);
    }
  });

  utils.forEach(directMergeKeys, function merge(prop) {
    if (prop in config2) {
      config[prop] = getMergedValue(config1[prop], config2[prop]);
    } else if (prop in config1) {
      config[prop] = getMergedValue(undefined, config1[prop]);
    }
  });

  var axiosKeys = valueFromConfig2Keys
    .concat(mergeDeepPropertiesKeys)
    .concat(defaultToConfig2Keys)
    .concat(directMergeKeys);

  var otherKeys = Object
    .keys(config1)
    .concat(Object.keys(config2))
    .filter(function filterAxiosKeys(key) {
      return axiosKeys.indexOf(key) === -1;
    });

  utils.forEach(otherKeys, mergeDeepProperties);

  return config;
};


/***/ }),

/***/ "./node_modules/axios/lib/core/settle.js":
/*!***********************************************!*\
  !*** ./node_modules/axios/lib/core/settle.js ***!
  \***********************************************/
/***/ (function(module, __unused_webpack_exports, __webpack_require__) {

"use strict";


var createError = __webpack_require__(/*! ./createError */ "./node_modules/axios/lib/core/createError.js");

/**
 * Resolve or reject a Promise based on response status.
 *
 * @param {Function} resolve A function that resolves the promise.
 * @param {Function} reject A function that rejects the promise.
 * @param {object} response The response.
 */
module.exports = function settle(resolve, reject, response) {
  var validateStatus = response.config.validateStatus;
  if (!response.status || !validateStatus || validateStatus(response.status)) {
    resolve(response);
  } else {
    reject(createError(
      'Request failed with status code ' + response.status,
      response.config,
      null,
      response.request,
      response
    ));
  }
};


/***/ }),

/***/ "./node_modules/axios/lib/core/transformData.js":
/*!******************************************************!*\
  !*** ./node_modules/axios/lib/core/transformData.js ***!
  \******************************************************/
/***/ (function(module, __unused_webpack_exports, __webpack_require__) {

"use strict";


var utils = __webpack_require__(/*! ./../utils */ "./node_modules/axios/lib/utils.js");
var defaults = __webpack_require__(/*! ./../defaults */ "./node_modules/axios/lib/defaults.js");

/**
 * Transform the data for a request or a response
 *
 * @param {Object|String} data The data to be transformed
 * @param {Array} headers The headers for the request or response
 * @param {Array|Function} fns A single function or Array of functions
 * @returns {*} The resulting transformed data
 */
module.exports = function transformData(data, headers, fns) {
  var context = this || defaults;
  /*eslint no-param-reassign:0*/
  utils.forEach(fns, function transform(fn) {
    data = fn.call(context, data, headers);
  });

  return data;
};


/***/ }),

/***/ "./node_modules/axios/lib/defaults.js":
/*!********************************************!*\
  !*** ./node_modules/axios/lib/defaults.js ***!
  \********************************************/
/***/ (function(module, __unused_webpack_exports, __webpack_require__) {

"use strict";


var utils = __webpack_require__(/*! ./utils */ "./node_modules/axios/lib/utils.js");
var normalizeHeaderName = __webpack_require__(/*! ./helpers/normalizeHeaderName */ "./node_modules/axios/lib/helpers/normalizeHeaderName.js");
var enhanceError = __webpack_require__(/*! ./core/enhanceError */ "./node_modules/axios/lib/core/enhanceError.js");

var DEFAULT_CONTENT_TYPE = {
  'Content-Type': 'application/x-www-form-urlencoded'
};

function setContentTypeIfUnset(headers, value) {
  if (!utils.isUndefined(headers) && utils.isUndefined(headers['Content-Type'])) {
    headers['Content-Type'] = value;
  }
}

function getDefaultAdapter() {
  var adapter;
  if (typeof XMLHttpRequest !== 'undefined') {
    // For browsers use XHR adapter
    adapter = __webpack_require__(/*! ./adapters/xhr */ "./node_modules/axios/lib/adapters/xhr.js");
  } else if (typeof process !== 'undefined' && Object.prototype.toString.call(process) === '[object process]') {
    // For node use HTTP adapter
    adapter = __webpack_require__(/*! ./adapters/http */ "./node_modules/axios/lib/adapters/xhr.js");
  }
  return adapter;
}

function stringifySafely(rawValue, parser, encoder) {
  if (utils.isString(rawValue)) {
    try {
      (parser || JSON.parse)(rawValue);
      return utils.trim(rawValue);
    } catch (e) {
      if (e.name !== 'SyntaxError') {
        throw e;
      }
    }
  }

  return (encoder || JSON.stringify)(rawValue);
}

var defaults = {

  transitional: {
    silentJSONParsing: true,
    forcedJSONParsing: true,
    clarifyTimeoutError: false
  },

  adapter: getDefaultAdapter(),

  transformRequest: [function transformRequest(data, headers) {
    normalizeHeaderName(headers, 'Accept');
    normalizeHeaderName(headers, 'Content-Type');

    if (utils.isFormData(data) ||
      utils.isArrayBuffer(data) ||
      utils.isBuffer(data) ||
      utils.isStream(data) ||
      utils.isFile(data) ||
      utils.isBlob(data)
    ) {
      return data;
    }
    if (utils.isArrayBufferView(data)) {
      return data.buffer;
    }
    if (utils.isURLSearchParams(data)) {
      setContentTypeIfUnset(headers, 'application/x-www-form-urlencoded;charset=utf-8');
      return data.toString();
    }
    if (utils.isObject(data) || (headers && headers['Content-Type'] === 'application/json')) {
      setContentTypeIfUnset(headers, 'application/json');
      return stringifySafely(data);
    }
    return data;
  }],

  transformResponse: [function transformResponse(data) {
    var transitional = this.transitional;
    var silentJSONParsing = transitional && transitional.silentJSONParsing;
    var forcedJSONParsing = transitional && transitional.forcedJSONParsing;
    var strictJSONParsing = !silentJSONParsing && this.responseType === 'json';

    if (strictJSONParsing || (forcedJSONParsing && utils.isString(data) && data.length)) {
      try {
        return JSON.parse(data);
      } catch (e) {
        if (strictJSONParsing) {
          if (e.name === 'SyntaxError') {
            throw enhanceError(e, this, 'E_JSON_PARSE');
          }
          throw e;
        }
      }
    }

    return data;
  }],

  /**
   * A timeout in milliseconds to abort a request. If set to 0 (default) a
   * timeout is not created.
   */
  timeout: 0,

  xsrfCookieName: 'XSRF-TOKEN',
  xsrfHeaderName: 'X-XSRF-TOKEN',

  maxContentLength: -1,
  maxBodyLength: -1,

  validateStatus: function validateStatus(status) {
    return status >= 200 && status < 300;
  }
};

defaults.headers = {
  common: {
    'Accept': 'application/json, text/plain, */*'
  }
};

utils.forEach(['delete', 'get', 'head'], function forEachMethodNoData(method) {
  defaults.headers[method] = {};
});

utils.forEach(['post', 'put', 'patch'], function forEachMethodWithData(method) {
  defaults.headers[method] = utils.merge(DEFAULT_CONTENT_TYPE);
});

module.exports = defaults;


/***/ }),

/***/ "./node_modules/axios/lib/helpers/bind.js":
/*!************************************************!*\
  !*** ./node_modules/axios/lib/helpers/bind.js ***!
  \************************************************/
/***/ (function(module) {

"use strict";


module.exports = function bind(fn, thisArg) {
  return function wrap() {
    var args = new Array(arguments.length);
    for (var i = 0; i < args.length; i++) {
      args[i] = arguments[i];
    }
    return fn.apply(thisArg, args);
  };
};


/***/ }),

/***/ "./node_modules/axios/lib/helpers/buildURL.js":
/*!****************************************************!*\
  !*** ./node_modules/axios/lib/helpers/buildURL.js ***!
  \****************************************************/
/***/ (function(module, __unused_webpack_exports, __webpack_require__) {

"use strict";


var utils = __webpack_require__(/*! ./../utils */ "./node_modules/axios/lib/utils.js");

function encode(val) {
  return encodeURIComponent(val).
    replace(/%3A/gi, ':').
    replace(/%24/g, '$').
    replace(/%2C/gi, ',').
    replace(/%20/g, '+').
    replace(/%5B/gi, '[').
    replace(/%5D/gi, ']');
}

/**
 * Build a URL by appending params to the end
 *
 * @param {string} url The base of the url (e.g., http://www.google.com)
 * @param {object} [params] The params to be appended
 * @returns {string} The formatted url
 */
module.exports = function buildURL(url, params, paramsSerializer) {
  /*eslint no-param-reassign:0*/
  if (!params) {
    return url;
  }

  var serializedParams;
  if (paramsSerializer) {
    serializedParams = paramsSerializer(params);
  } else if (utils.isURLSearchParams(params)) {
    serializedParams = params.toString();
  } else {
    var parts = [];

    utils.forEach(params, function serialize(val, key) {
      if (val === null || typeof val === 'undefined') {
        return;
      }

      if (utils.isArray(val)) {
        key = key + '[]';
      } else {
        val = [val];
      }

      utils.forEach(val, function parseValue(v) {
        if (utils.isDate(v)) {
          v = v.toISOString();
        } else if (utils.isObject(v)) {
          v = JSON.stringify(v);
        }
        parts.push(encode(key) + '=' + encode(v));
      });
    });

    serializedParams = parts.join('&');
  }

  if (serializedParams) {
    var hashmarkIndex = url.indexOf('#');
    if (hashmarkIndex !== -1) {
      url = url.slice(0, hashmarkIndex);
    }

    url += (url.indexOf('?') === -1 ? '?' : '&') + serializedParams;
  }

  return url;
};


/***/ }),

/***/ "./node_modules/axios/lib/helpers/combineURLs.js":
/*!*******************************************************!*\
  !*** ./node_modules/axios/lib/helpers/combineURLs.js ***!
  \*******************************************************/
/***/ (function(module) {

"use strict";


/**
 * Creates a new URL by combining the specified URLs
 *
 * @param {string} baseURL The base URL
 * @param {string} relativeURL The relative URL
 * @returns {string} The combined URL
 */
module.exports = function combineURLs(baseURL, relativeURL) {
  return relativeURL
    ? baseURL.replace(/\/+$/, '') + '/' + relativeURL.replace(/^\/+/, '')
    : baseURL;
};


/***/ }),

/***/ "./node_modules/axios/lib/helpers/cookies.js":
/*!***************************************************!*\
  !*** ./node_modules/axios/lib/helpers/cookies.js ***!
  \***************************************************/
/***/ (function(module, __unused_webpack_exports, __webpack_require__) {

"use strict";


var utils = __webpack_require__(/*! ./../utils */ "./node_modules/axios/lib/utils.js");

module.exports = (
  utils.isStandardBrowserEnv() ?

  // Standard browser envs support document.cookie
    (function standardBrowserEnv() {
      return {
        write: function write(name, value, expires, path, domain, secure) {
          var cookie = [];
          cookie.push(name + '=' + encodeURIComponent(value));

          if (utils.isNumber(expires)) {
            cookie.push('expires=' + new Date(expires).toGMTString());
          }

          if (utils.isString(path)) {
            cookie.push('path=' + path);
          }

          if (utils.isString(domain)) {
            cookie.push('domain=' + domain);
          }

          if (secure === true) {
            cookie.push('secure');
          }

          document.cookie = cookie.join('; ');
        },

        read: function read(name) {
          var match = document.cookie.match(new RegExp('(^|;\\s*)(' + name + ')=([^;]*)'));
          return (match ? decodeURIComponent(match[3]) : null);
        },

        remove: function remove(name) {
          this.write(name, '', Date.now() - 86400000);
        }
      };
    })() :

  // Non standard browser env (web workers, react-native) lack needed support.
    (function nonStandardBrowserEnv() {
      return {
        write: function write() {},
        read: function read() { return null; },
        remove: function remove() {}
      };
    })()
);


/***/ }),

/***/ "./node_modules/axios/lib/helpers/isAbsoluteURL.js":
/*!*********************************************************!*\
  !*** ./node_modules/axios/lib/helpers/isAbsoluteURL.js ***!
  \*********************************************************/
/***/ (function(module) {

"use strict";


/**
 * Determines whether the specified URL is absolute
 *
 * @param {string} url The URL to test
 * @returns {boolean} True if the specified URL is absolute, otherwise false
 */
module.exports = function isAbsoluteURL(url) {
  // A URL is considered absolute if it begins with "<scheme>://" or "//" (protocol-relative URL).
  // RFC 3986 defines scheme name as a sequence of characters beginning with a letter and followed
  // by any combination of letters, digits, plus, period, or hyphen.
  return /^([a-z][a-z\d\+\-\.]*:)?\/\//i.test(url);
};


/***/ }),

/***/ "./node_modules/axios/lib/helpers/isAxiosError.js":
/*!********************************************************!*\
  !*** ./node_modules/axios/lib/helpers/isAxiosError.js ***!
  \********************************************************/
/***/ (function(module) {

"use strict";


/**
 * Determines whether the payload is an error thrown by Axios
 *
 * @param {*} payload The value to test
 * @returns {boolean} True if the payload is an error thrown by Axios, otherwise false
 */
module.exports = function isAxiosError(payload) {
  return (typeof payload === 'object') && (payload.isAxiosError === true);
};


/***/ }),

/***/ "./node_modules/axios/lib/helpers/isURLSameOrigin.js":
/*!***********************************************************!*\
  !*** ./node_modules/axios/lib/helpers/isURLSameOrigin.js ***!
  \***********************************************************/
/***/ (function(module, __unused_webpack_exports, __webpack_require__) {

"use strict";


var utils = __webpack_require__(/*! ./../utils */ "./node_modules/axios/lib/utils.js");

module.exports = (
  utils.isStandardBrowserEnv() ?

  // Standard browser envs have full support of the APIs needed to test
  // whether the request URL is of the same origin as current location.
    (function standardBrowserEnv() {
      var msie = /(msie|trident)/i.test(navigator.userAgent);
      var urlParsingNode = document.createElement('a');
      var originURL;

      /**
    * Parse a URL to discover it's components
    *
    * @param {String} url The URL to be parsed
    * @returns {Object}
    */
      function resolveURL(url) {
        var href = url;

        if (msie) {
        // IE needs attribute set twice to normalize properties
          urlParsingNode.setAttribute('href', href);
          href = urlParsingNode.href;
        }

        urlParsingNode.setAttribute('href', href);

        // urlParsingNode provides the UrlUtils interface - http://url.spec.whatwg.org/#urlutils
        return {
          href: urlParsingNode.href,
          protocol: urlParsingNode.protocol ? urlParsingNode.protocol.replace(/:$/, '') : '',
          host: urlParsingNode.host,
          search: urlParsingNode.search ? urlParsingNode.search.replace(/^\?/, '') : '',
          hash: urlParsingNode.hash ? urlParsingNode.hash.replace(/^#/, '') : '',
          hostname: urlParsingNode.hostname,
          port: urlParsingNode.port,
          pathname: (urlParsingNode.pathname.charAt(0) === '/') ?
            urlParsingNode.pathname :
            '/' + urlParsingNode.pathname
        };
      }

      originURL = resolveURL(window.location.href);

      /**
    * Determine if a URL shares the same origin as the current location
    *
    * @param {String} requestURL The URL to test
    * @returns {boolean} True if URL shares the same origin, otherwise false
    */
      return function isURLSameOrigin(requestURL) {
        var parsed = (utils.isString(requestURL)) ? resolveURL(requestURL) : requestURL;
        return (parsed.protocol === originURL.protocol &&
            parsed.host === originURL.host);
      };
    })() :

  // Non standard browser envs (web workers, react-native) lack needed support.
    (function nonStandardBrowserEnv() {
      return function isURLSameOrigin() {
        return true;
      };
    })()
);


/***/ }),

/***/ "./node_modules/axios/lib/helpers/normalizeHeaderName.js":
/*!***************************************************************!*\
  !*** ./node_modules/axios/lib/helpers/normalizeHeaderName.js ***!
  \***************************************************************/
/***/ (function(module, __unused_webpack_exports, __webpack_require__) {

"use strict";


var utils = __webpack_require__(/*! ../utils */ "./node_modules/axios/lib/utils.js");

module.exports = function normalizeHeaderName(headers, normalizedName) {
  utils.forEach(headers, function processHeader(value, name) {
    if (name !== normalizedName && name.toUpperCase() === normalizedName.toUpperCase()) {
      headers[normalizedName] = value;
      delete headers[name];
    }
  });
};


/***/ }),

/***/ "./node_modules/axios/lib/helpers/parseHeaders.js":
/*!********************************************************!*\
  !*** ./node_modules/axios/lib/helpers/parseHeaders.js ***!
  \********************************************************/
/***/ (function(module, __unused_webpack_exports, __webpack_require__) {

"use strict";


var utils = __webpack_require__(/*! ./../utils */ "./node_modules/axios/lib/utils.js");

// Headers whose duplicates are ignored by node
// c.f. https://nodejs.org/api/http.html#http_message_headers
var ignoreDuplicateOf = [
  'age', 'authorization', 'content-length', 'content-type', 'etag',
  'expires', 'from', 'host', 'if-modified-since', 'if-unmodified-since',
  'last-modified', 'location', 'max-forwards', 'proxy-authorization',
  'referer', 'retry-after', 'user-agent'
];

/**
 * Parse headers into an object
 *
 * ```
 * Date: Wed, 27 Aug 2014 08:58:49 GMT
 * Content-Type: application/json
 * Connection: keep-alive
 * Transfer-Encoding: chunked
 * ```
 *
 * @param {String} headers Headers needing to be parsed
 * @returns {Object} Headers parsed into an object
 */
module.exports = function parseHeaders(headers) {
  var parsed = {};
  var key;
  var val;
  var i;

  if (!headers) { return parsed; }

  utils.forEach(headers.split('\n'), function parser(line) {
    i = line.indexOf(':');
    key = utils.trim(line.substr(0, i)).toLowerCase();
    val = utils.trim(line.substr(i + 1));

    if (key) {
      if (parsed[key] && ignoreDuplicateOf.indexOf(key) >= 0) {
        return;
      }
      if (key === 'set-cookie') {
        parsed[key] = (parsed[key] ? parsed[key] : []).concat([val]);
      } else {
        parsed[key] = parsed[key] ? parsed[key] + ', ' + val : val;
      }
    }
  });

  return parsed;
};


/***/ }),

/***/ "./node_modules/axios/lib/helpers/spread.js":
/*!**************************************************!*\
  !*** ./node_modules/axios/lib/helpers/spread.js ***!
  \**************************************************/
/***/ (function(module) {

"use strict";


/**
 * Syntactic sugar for invoking a function and expanding an array for arguments.
 *
 * Common use case would be to use `Function.prototype.apply`.
 *
 *  ```js
 *  function f(x, y, z) {}
 *  var args = [1, 2, 3];
 *  f.apply(null, args);
 *  ```
 *
 * With `spread` this example can be re-written.
 *
 *  ```js
 *  spread(function(x, y, z) {})([1, 2, 3]);
 *  ```
 *
 * @param {Function} callback
 * @returns {Function}
 */
module.exports = function spread(callback) {
  return function wrap(arr) {
    return callback.apply(null, arr);
  };
};


/***/ }),

/***/ "./node_modules/axios/lib/helpers/validator.js":
/*!*****************************************************!*\
  !*** ./node_modules/axios/lib/helpers/validator.js ***!
  \*****************************************************/
/***/ (function(module, __unused_webpack_exports, __webpack_require__) {

"use strict";


var pkg = __webpack_require__(/*! ./../../package.json */ "./node_modules/axios/package.json");

var validators = {};

// eslint-disable-next-line func-names
['object', 'boolean', 'number', 'function', 'string', 'symbol'].forEach(function(type, i) {
  validators[type] = function validator(thing) {
    return typeof thing === type || 'a' + (i < 1 ? 'n ' : ' ') + type;
  };
});

var deprecatedWarnings = {};
var currentVerArr = pkg.version.split('.');

/**
 * Compare package versions
 * @param {string} version
 * @param {string?} thanVersion
 * @returns {boolean}
 */
function isOlderVersion(version, thanVersion) {
  var pkgVersionArr = thanVersion ? thanVersion.split('.') : currentVerArr;
  var destVer = version.split('.');
  for (var i = 0; i < 3; i++) {
    if (pkgVersionArr[i] > destVer[i]) {
      return true;
    } else if (pkgVersionArr[i] < destVer[i]) {
      return false;
    }
  }
  return false;
}

/**
 * Transitional option validator
 * @param {function|boolean?} validator
 * @param {string?} version
 * @param {string} message
 * @returns {function}
 */
validators.transitional = function transitional(validator, version, message) {
  var isDeprecated = version && isOlderVersion(version);

  function formatMessage(opt, desc) {
    return '[Axios v' + pkg.version + '] Transitional option \'' + opt + '\'' + desc + (message ? '. ' + message : '');
  }

  // eslint-disable-next-line func-names
  return function(value, opt, opts) {
    if (validator === false) {
      throw new Error(formatMessage(opt, ' has been removed in ' + version));
    }

    if (isDeprecated && !deprecatedWarnings[opt]) {
      deprecatedWarnings[opt] = true;
      // eslint-disable-next-line no-console
      console.warn(
        formatMessage(
          opt,
          ' has been deprecated since v' + version + ' and will be removed in the near future'
        )
      );
    }

    return validator ? validator(value, opt, opts) : true;
  };
};

/**
 * Assert object's properties type
 * @param {object} options
 * @param {object} schema
 * @param {boolean?} allowUnknown
 */

function assertOptions(options, schema, allowUnknown) {
  if (typeof options !== 'object') {
    throw new TypeError('options must be an object');
  }
  var keys = Object.keys(options);
  var i = keys.length;
  while (i-- > 0) {
    var opt = keys[i];
    var validator = schema[opt];
    if (validator) {
      var value = options[opt];
      var result = value === undefined || validator(value, opt, options);
      if (result !== true) {
        throw new TypeError('option ' + opt + ' must be ' + result);
      }
      continue;
    }
    if (allowUnknown !== true) {
      throw Error('Unknown option ' + opt);
    }
  }
}

module.exports = {
  isOlderVersion: isOlderVersion,
  assertOptions: assertOptions,
  validators: validators
};


/***/ }),

/***/ "./node_modules/axios/lib/utils.js":
/*!*****************************************!*\
  !*** ./node_modules/axios/lib/utils.js ***!
  \*****************************************/
/***/ (function(module, __unused_webpack_exports, __webpack_require__) {

"use strict";


var bind = __webpack_require__(/*! ./helpers/bind */ "./node_modules/axios/lib/helpers/bind.js");

// utils is a library of generic helper functions non-specific to axios

var toString = Object.prototype.toString;

/**
 * Determine if a value is an Array
 *
 * @param {Object} val The value to test
 * @returns {boolean} True if value is an Array, otherwise false
 */
function isArray(val) {
  return toString.call(val) === '[object Array]';
}

/**
 * Determine if a value is undefined
 *
 * @param {Object} val The value to test
 * @returns {boolean} True if the value is undefined, otherwise false
 */
function isUndefined(val) {
  return typeof val === 'undefined';
}

/**
 * Determine if a value is a Buffer
 *
 * @param {Object} val The value to test
 * @returns {boolean} True if value is a Buffer, otherwise false
 */
function isBuffer(val) {
  return val !== null && !isUndefined(val) && val.constructor !== null && !isUndefined(val.constructor)
    && typeof val.constructor.isBuffer === 'function' && val.constructor.isBuffer(val);
}

/**
 * Determine if a value is an ArrayBuffer
 *
 * @param {Object} val The value to test
 * @returns {boolean} True if value is an ArrayBuffer, otherwise false
 */
function isArrayBuffer(val) {
  return toString.call(val) === '[object ArrayBuffer]';
}

/**
 * Determine if a value is a FormData
 *
 * @param {Object} val The value to test
 * @returns {boolean} True if value is an FormData, otherwise false
 */
function isFormData(val) {
  return (typeof FormData !== 'undefined') && (val instanceof FormData);
}

/**
 * Determine if a value is a view on an ArrayBuffer
 *
 * @param {Object} val The value to test
 * @returns {boolean} True if value is a view on an ArrayBuffer, otherwise false
 */
function isArrayBufferView(val) {
  var result;
  if ((typeof ArrayBuffer !== 'undefined') && (ArrayBuffer.isView)) {
    result = ArrayBuffer.isView(val);
  } else {
    result = (val) && (val.buffer) && (val.buffer instanceof ArrayBuffer);
  }
  return result;
}

/**
 * Determine if a value is a String
 *
 * @param {Object} val The value to test
 * @returns {boolean} True if value is a String, otherwise false
 */
function isString(val) {
  return typeof val === 'string';
}

/**
 * Determine if a value is a Number
 *
 * @param {Object} val The value to test
 * @returns {boolean} True if value is a Number, otherwise false
 */
function isNumber(val) {
  return typeof val === 'number';
}

/**
 * Determine if a value is an Object
 *
 * @param {Object} val The value to test
 * @returns {boolean} True if value is an Object, otherwise false
 */
function isObject(val) {
  return val !== null && typeof val === 'object';
}

/**
 * Determine if a value is a plain Object
 *
 * @param {Object} val The value to test
 * @return {boolean} True if value is a plain Object, otherwise false
 */
function isPlainObject(val) {
  if (toString.call(val) !== '[object Object]') {
    return false;
  }

  var prototype = Object.getPrototypeOf(val);
  return prototype === null || prototype === Object.prototype;
}

/**
 * Determine if a value is a Date
 *
 * @param {Object} val The value to test
 * @returns {boolean} True if value is a Date, otherwise false
 */
function isDate(val) {
  return toString.call(val) === '[object Date]';
}

/**
 * Determine if a value is a File
 *
 * @param {Object} val The value to test
 * @returns {boolean} True if value is a File, otherwise false
 */
function isFile(val) {
  return toString.call(val) === '[object File]';
}

/**
 * Determine if a value is a Blob
 *
 * @param {Object} val The value to test
 * @returns {boolean} True if value is a Blob, otherwise false
 */
function isBlob(val) {
  return toString.call(val) === '[object Blob]';
}

/**
 * Determine if a value is a Function
 *
 * @param {Object} val The value to test
 * @returns {boolean} True if value is a Function, otherwise false
 */
function isFunction(val) {
  return toString.call(val) === '[object Function]';
}

/**
 * Determine if a value is a Stream
 *
 * @param {Object} val The value to test
 * @returns {boolean} True if value is a Stream, otherwise false
 */
function isStream(val) {
  return isObject(val) && isFunction(val.pipe);
}

/**
 * Determine if a value is a URLSearchParams object
 *
 * @param {Object} val The value to test
 * @returns {boolean} True if value is a URLSearchParams object, otherwise false
 */
function isURLSearchParams(val) {
  return typeof URLSearchParams !== 'undefined' && val instanceof URLSearchParams;
}

/**
 * Trim excess whitespace off the beginning and end of a string
 *
 * @param {String} str The String to trim
 * @returns {String} The String freed of excess whitespace
 */
function trim(str) {
  return str.trim ? str.trim() : str.replace(/^\s+|\s+$/g, '');
}

/**
 * Determine if we're running in a standard browser environment
 *
 * This allows axios to run in a web worker, and react-native.
 * Both environments support XMLHttpRequest, but not fully standard globals.
 *
 * web workers:
 *  typeof window -> undefined
 *  typeof document -> undefined
 *
 * react-native:
 *  navigator.product -> 'ReactNative'
 * nativescript
 *  navigator.product -> 'NativeScript' or 'NS'
 */
function isStandardBrowserEnv() {
  if (typeof navigator !== 'undefined' && (navigator.product === 'ReactNative' ||
                                           navigator.product === 'NativeScript' ||
                                           navigator.product === 'NS')) {
    return false;
  }
  return (
    typeof window !== 'undefined' &&
    typeof document !== 'undefined'
  );
}

/**
 * Iterate over an Array or an Object invoking a function for each item.
 *
 * If `obj` is an Array callback will be called passing
 * the value, index, and complete array for each item.
 *
 * If 'obj' is an Object callback will be called passing
 * the value, key, and complete object for each property.
 *
 * @param {Object|Array} obj The object to iterate
 * @param {Function} fn The callback to invoke for each item
 */
function forEach(obj, fn) {
  // Don't bother if no value provided
  if (obj === null || typeof obj === 'undefined') {
    return;
  }

  // Force an array if not already something iterable
  if (typeof obj !== 'object') {
    /*eslint no-param-reassign:0*/
    obj = [obj];
  }

  if (isArray(obj)) {
    // Iterate over array values
    for (var i = 0, l = obj.length; i < l; i++) {
      fn.call(null, obj[i], i, obj);
    }
  } else {
    // Iterate over object keys
    for (var key in obj) {
      if (Object.prototype.hasOwnProperty.call(obj, key)) {
        fn.call(null, obj[key], key, obj);
      }
    }
  }
}

/**
 * Accepts varargs expecting each argument to be an object, then
 * immutably merges the properties of each object and returns result.
 *
 * When multiple objects contain the same key the later object in
 * the arguments list will take precedence.
 *
 * Example:
 *
 * ```js
 * var result = merge({foo: 123}, {foo: 456});
 * console.log(result.foo); // outputs 456
 * ```
 *
 * @param {Object} obj1 Object to merge
 * @returns {Object} Result of all merge properties
 */
function merge(/* obj1, obj2, obj3, ... */) {
  var result = {};
  function assignValue(val, key) {
    if (isPlainObject(result[key]) && isPlainObject(val)) {
      result[key] = merge(result[key], val);
    } else if (isPlainObject(val)) {
      result[key] = merge({}, val);
    } else if (isArray(val)) {
      result[key] = val.slice();
    } else {
      result[key] = val;
    }
  }

  for (var i = 0, l = arguments.length; i < l; i++) {
    forEach(arguments[i], assignValue);
  }
  return result;
}

/**
 * Extends object a by mutably adding to it the properties of object b.
 *
 * @param {Object} a The object to be extended
 * @param {Object} b The object to copy properties from
 * @param {Object} thisArg The object to bind function to
 * @return {Object} The resulting value of object a
 */
function extend(a, b, thisArg) {
  forEach(b, function assignValue(val, key) {
    if (thisArg && typeof val === 'function') {
      a[key] = bind(val, thisArg);
    } else {
      a[key] = val;
    }
  });
  return a;
}

/**
 * Remove byte order marker. This catches EF BB BF (the UTF-8 BOM)
 *
 * @param {string} content with BOM
 * @return {string} content value without BOM
 */
function stripBOM(content) {
  if (content.charCodeAt(0) === 0xFEFF) {
    content = content.slice(1);
  }
  return content;
}

module.exports = {
  isArray: isArray,
  isArrayBuffer: isArrayBuffer,
  isBuffer: isBuffer,
  isFormData: isFormData,
  isArrayBufferView: isArrayBufferView,
  isString: isString,
  isNumber: isNumber,
  isObject: isObject,
  isPlainObject: isPlainObject,
  isUndefined: isUndefined,
  isDate: isDate,
  isFile: isFile,
  isBlob: isBlob,
  isFunction: isFunction,
  isStream: isStream,
  isURLSearchParams: isURLSearchParams,
  isStandardBrowserEnv: isStandardBrowserEnv,
  forEach: forEach,
  merge: merge,
  extend: extend,
  trim: trim,
  stripBOM: stripBOM
};


/***/ }),

/***/ "./src/index.js":
/*!**********************!*\
  !*** ./src/index.js ***!
  \**********************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _css_style_scss__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../css/style.scss */ "./css/style.scss");
/* harmony import */ var _modules_Form_Form__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./modules/Form/Form */ "./src/modules/Form/Form.js");
/* harmony import */ var _modules_OwlCarousel_EveryOwlCarousel__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./modules/OwlCarousel/EveryOwlCarousel */ "./src/modules/OwlCarousel/EveryOwlCarousel.js");
/* harmony import */ var _modules_Warranty__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./modules/Warranty */ "./src/modules/Warranty.js");
/* harmony import */ var _modules_WallpaperCalc__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./modules/WallpaperCalc */ "./src/modules/WallpaperCalc.js");
/* harmony import */ var _modules_DesignBoardSaveBtn__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ./modules/DesignBoardSaveBtn */ "./src/modules/DesignBoardSaveBtn.js");
/* harmony import */ var _modules_overlay__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ./modules/overlay */ "./src/modules/overlay.js");
/* harmony import */ var _modules_TopNav__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ./modules/TopNav */ "./src/modules/TopNav.js");
/* harmony import */ var _modules_ShopFav__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! ./modules/ShopFav */ "./src/modules/ShopFav.js");
/* harmony import */ var _modules_ToolTip__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! ./modules/ToolTip */ "./src/modules/ToolTip.js");
/* harmony import */ var _modules_PopUpCart__WEBPACK_IMPORTED_MODULE_10__ = __webpack_require__(/*! ./modules/PopUpCart */ "./src/modules/PopUpCart.js");
/* harmony import */ var _modules_EnquiryModal_EnquiryModal__WEBPACK_IMPORTED_MODULE_11__ = __webpack_require__(/*! ./modules/EnquiryModal/EnquiryModal */ "./src/modules/EnquiryModal/EnquiryModal.js");
/* harmony import */ var _modules_CartModal_CartModal__WEBPACK_IMPORTED_MODULE_12__ = __webpack_require__(/*! ./modules/CartModal/CartModal */ "./src/modules/CartModal/CartModal.js");
/* harmony import */ var _modules_Auth_Login__WEBPACK_IMPORTED_MODULE_13__ = __webpack_require__(/*! ./modules/Auth/Login */ "./src/modules/Auth/Login.js");
/* harmony import */ var _modules_Search__WEBPACK_IMPORTED_MODULE_14__ = __webpack_require__(/*! ./modules/Search */ "./src/modules/Search.js");
/* harmony import */ var _modules_MobileSearch__WEBPACK_IMPORTED_MODULE_15__ = __webpack_require__(/*! ./modules/MobileSearch */ "./src/modules/MobileSearch.js");
/* harmony import */ var _modules_FacetFilter_FacetFilter__WEBPACK_IMPORTED_MODULE_16__ = __webpack_require__(/*! ./modules/FacetFilter/FacetFilter */ "./src/modules/FacetFilter/FacetFilter.js");
/* harmony import */ var _modules_CustomerService_CustomerServiceMenu__WEBPACK_IMPORTED_MODULE_17__ = __webpack_require__(/*! ./modules/CustomerService/CustomerServiceMenu */ "./src/modules/CustomerService/CustomerServiceMenu.js");
/* harmony import */ var _modules_CustomerService_ContactForm__WEBPACK_IMPORTED_MODULE_18__ = __webpack_require__(/*! ./modules/CustomerService/ContactForm */ "./src/modules/CustomerService/ContactForm.js");
/* harmony import */ var _modules_CustomerService_FeedbackForm__WEBPACK_IMPORTED_MODULE_19__ = __webpack_require__(/*! ./modules/CustomerService/FeedbackForm */ "./src/modules/CustomerService/FeedbackForm.js");
/* harmony import */ var _modules_Woocommerce_WooGallery__WEBPACK_IMPORTED_MODULE_20__ = __webpack_require__(/*! ./modules/Woocommerce/WooGallery */ "./src/modules/Woocommerce/WooGallery.js");
/* harmony import */ var _modules_Woocommerce_singleProductAccordion__WEBPACK_IMPORTED_MODULE_21__ = __webpack_require__(/*! ./modules/Woocommerce/singleProductAccordion */ "./src/modules/Woocommerce/singleProductAccordion.js");
/* harmony import */ var _modules_Woocommerce_ProductArchive__WEBPACK_IMPORTED_MODULE_22__ = __webpack_require__(/*! ./modules/Woocommerce/ProductArchive */ "./src/modules/Woocommerce/ProductArchive.js");
/* harmony import */ var _modules_Woocommerce_SingleProduct__WEBPACK_IMPORTED_MODULE_23__ = __webpack_require__(/*! ./modules/Woocommerce/SingleProduct */ "./src/modules/Woocommerce/SingleProduct.js");
/* harmony import */ var _modules_Woocommerce_Cart_Cart__WEBPACK_IMPORTED_MODULE_24__ = __webpack_require__(/*! ./modules/Woocommerce/Cart/Cart */ "./src/modules/Woocommerce/Cart/Cart.js");
/* harmony import */ var _modules_Woocommerce_Cart_Coupon__WEBPACK_IMPORTED_MODULE_25__ = __webpack_require__(/*! ./modules/Woocommerce/Cart/Coupon */ "./src/modules/Woocommerce/Cart/Coupon.js");
/* harmony import */ var _modules_ErrorModal_ErrorModal__WEBPACK_IMPORTED_MODULE_26__ = __webpack_require__(/*! ./modules/ErrorModal/ErrorModal */ "./src/modules/ErrorModal/ErrorModal.js");
/* harmony import */ var _modules_Woocommerce_Checkout_Checkout__WEBPACK_IMPORTED_MODULE_27__ = __webpack_require__(/*! ./modules/Woocommerce/Checkout/Checkout */ "./src/modules/Woocommerce/Checkout/Checkout.js");
/* harmony import */ var _modules_Header__WEBPACK_IMPORTED_MODULE_28__ = __webpack_require__(/*! ./modules/Header */ "./src/modules/Header.js");
 // form 

 // owl carousel 

 // warranty 







 //pop up cart

 // Enquire Modal 

 // cart modal 

 // auth

 // search 


 // facet filter

 // customer service 



 // woocommerce 






 // import Windcave from "./modules/Woocommerce/Checkout/Windcave";
// modals 


 // header 


let $ = jQuery; // add to cart and remove from cart class 

const popUpCart = new _modules_PopUpCart__WEBPACK_IMPORTED_MODULE_10__["default"](); // woo Gallery 

const wooGallery = new _modules_Woocommerce_WooGallery__WEBPACK_IMPORTED_MODULE_20__["default"](); // single product page accordion 

const singleProductAccordion = new _modules_Woocommerce_singleProductAccordion__WEBPACK_IMPORTED_MODULE_21__["default"](); // single product 

const singleProduct = new _modules_Woocommerce_SingleProduct__WEBPACK_IMPORTED_MODULE_23__["default"](); // every owl carousel

const everyOwlCarousel = new _modules_OwlCarousel_EveryOwlCarousel__WEBPACK_IMPORTED_MODULE_2__["default"](); // product archive

const productArchive = new _modules_Woocommerce_ProductArchive__WEBPACK_IMPORTED_MODULE_22__["default"](); // cart 

const cart = new _modules_Woocommerce_Cart_Cart__WEBPACK_IMPORTED_MODULE_24__["default"]();
const coupon = new _modules_Woocommerce_Cart_Coupon__WEBPACK_IMPORTED_MODULE_25__["default"](); // modals 

const errorModal = new _modules_ErrorModal_ErrorModal__WEBPACK_IMPORTED_MODULE_26__["default"](); // design board save button 

const designBoardSaveBtn = new _modules_DesignBoardSaveBtn__WEBPACK_IMPORTED_MODULE_5__["default"](); // header 

const header = new _modules_Header__WEBPACK_IMPORTED_MODULE_28__["default"]();

window.onload = function () {
  // checkout 
  const checkout = new _modules_Woocommerce_Checkout_Checkout__WEBPACK_IMPORTED_MODULE_27__["default"](); // enquiry modal 

  const enquiryModal = new _modules_EnquiryModal_EnquiryModal__WEBPACK_IMPORTED_MODULE_11__["default"](); // cart modal 

  const cartModal = new _modules_CartModal_CartModal__WEBPACK_IMPORTED_MODULE_12__["default"](); // form data processing 

  const form = new _modules_Form_Form__WEBPACK_IMPORTED_MODULE_1__["default"]();
  const shopFav = new _modules_ShopFav__WEBPACK_IMPORTED_MODULE_8__["default"]();
  const topnav = new _modules_TopNav__WEBPACK_IMPORTED_MODULE_7__["default"]();
  const overlay = new _modules_overlay__WEBPACK_IMPORTED_MODULE_6__["default"](); //Tool tip 

  const toolTip = new _modules_ToolTip__WEBPACK_IMPORTED_MODULE_9__["default"](); // login 

  const login = new _modules_Auth_Login__WEBPACK_IMPORTED_MODULE_13__["default"](); // search 

  const search = new _modules_Search__WEBPACK_IMPORTED_MODULE_14__["default"]();
  const mobileSearch = new _modules_MobileSearch__WEBPACK_IMPORTED_MODULE_15__["default"](); // facet filter 

  const facetFilter = new _modules_FacetFilter_FacetFilter__WEBPACK_IMPORTED_MODULE_16__["default"](); // customer service 

  const customerServiceMenu = new _modules_CustomerService_CustomerServiceMenu__WEBPACK_IMPORTED_MODULE_17__["default"]();
  const contactForm = new _modules_CustomerService_ContactForm__WEBPACK_IMPORTED_MODULE_18__["default"]();
  const feedbackForm = new _modules_CustomerService_FeedbackForm__WEBPACK_IMPORTED_MODULE_19__["default"](); // const windcave = new Windcave()
  //price 

  let pricevalue = document.getElementsByClassName('bc-show-current-price'); // console.log($('.bc-show-current-price').text);
  //slogan 

  $('.logo-container .slogan').css('opacity', '1');
}; //log in 
//const logIn = new LogIn();


const warranty = new _modules_Warranty__WEBPACK_IMPORTED_MODULE_3__["default"]();
const wallpaperCalc = new _modules_WallpaperCalc__WEBPACK_IMPORTED_MODULE_4__["default"](); // typewriter effect

document.addEventListener('DOMContentLoaded', function (event) {
  // array with texts to type in typewriter
  // get json array from a title on a web page
  let jsonArray = $('.typewriter-query-container div').attr('data-title');

  if (jsonArray) {
    let dataText = JSON.parse(jsonArray); // type one text in the typwriter
    // keeps calling itself until the text is finished

    function typeWriter(text, i, fnCallback) {
      // chekc if text isn't finished yet
      if (i < text.length) {
        // add next character to h1
        document.querySelector(".typewriter-title").innerHTML = text.substring(0, i + 1) + '<span aria-hidden="true"></span>'; // wait for a while and call this function again for next character

        setTimeout(function () {
          typeWriter(text, i + 1, fnCallback);
        }, 100);
      } // text finished, call callback if there is a callback function
      else if (typeof fnCallback == 'function') {
        // call callback after timeout
        setTimeout(fnCallback, 700);
      }
    } // start a typewriter animation for a text in the dataText array


    function StartTextAnimation(i) {
      if (typeof dataText[i] == 'undefined') {
        setTimeout(function () {
          StartTextAnimation(0);
        }, 1000);
      }

      if (dataText) {
        // check if dataText[i] exists
        if (i < dataText[i].length) {
          // text exists! start typewriter animation
          typeWriter(dataText[i], 0, function () {
            // after callback (and whole text has been animated), start next text
            StartTextAnimation(i + 1);
          });
        }
      }
    } // start the text animation


    StartTextAnimation(0);
  }
}); // scroll arrow 

let myID = document.getElementById("go-to-header");

var myScrollFunc = function () {
  var y = window.scrollY;

  if (y >= 1200) {
    myID.classList.add("show");
  } else if (y <= 1200) {
    myID.classList.remove("show");
  }
};

window.addEventListener("scroll", myScrollFunc); // hide facet if no value 

(function ($) {
  document.addEventListener('facetwp-loaded', function () {
    $.each(FWP.settings.num_choices, function (key, val) {
      var $facet = $('.facetwp-facet-' + key);
      var $parent = $facet.closest('.facet-wrap');
      var $flyout = $facet.closest('.flyout-row');

      if ($parent.length || $flyout.length) {
        var $which = $parent.length ? $parent : $flyout;
        0 === val ? $which.hide() : $which.show();
      }
    });
  });
})(jQuery);

/***/ }),

/***/ "./src/modules/Auth/AuthToken.js":
/*!***************************************!*\
  !*** ./src/modules/Auth/AuthToken.js ***!
  \***************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
let $ = jQuery;

class AuthToken {
  constructor(redirectLink, username, password, email) {
    this.username = username;
    this.password = password;
    this.email = email;
    this.redirectLink = redirectLink;
    this.events();
  }

  events() {
    let formData = {
      username: this.username,
      email: this.email,
      password: this.password
    }; // erase existing cookies 

    this.eraseCookie('inpiryAuthToken');
    let url = `${inspiryData.root_url}/wp-json/jwt-auth/v1/token`; // set auth cookies 

    fetch(url, {
      method: "POST",
      body: JSON.stringify(formData),
      headers: {
        'Content-Type': 'application/json'
      }
    }).then(res => res.json()).then(res => {
      // document.forms["login-form"].submit();
      if (res.data) {
        console.log(res.data.status);
      } else {
        this.setCookie('inpiryAuthToken', res.token, 3);

        if (this.redirectLink) {
          window.location.replace(this.redirectLink);
        } else {
          window.location.replace("/");
        }
      }
    }).catch(err => console.log(err));
  }

  setCookie(name, value, days) {
    var expires = "";

    if (days) {
      var date = new Date();
      date.setTime(date.getTime() + days * 24 * 60 * 60 * 1000);
      expires = "; expires=" + date.toUTCString();
    }

    document.cookie = name + "=" + (value || "") + expires + "; path=/";
  }

  eraseCookie(name) {
    document.cookie = name + '=; Path=/; Expires=Thu, 01 Jan 1970 00:00:01 GMT;';
  }

}

/* harmony default export */ __webpack_exports__["default"] = (AuthToken);

/***/ }),

/***/ "./src/modules/Auth/Login.js":
/*!***********************************!*\
  !*** ./src/modules/Auth/Login.js ***!
  \***********************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _AuthToken__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./AuthToken */ "./src/modules/Auth/AuthToken.js");

let $ = jQuery;

class Login {
  constructor() {
    this.events();
  }

  events() {
    // submit login form
    $('form#login').on('submit', this.submitLogin);
  }

  submitLogin(e) {
    e.preventDefault(); // get redirect link from url parameters 

    const queryString = window.location.search;
    const urlParams = new URLSearchParams(queryString);
    const redirectLink = urlParams.get('redirect-link');
    $('form#login p.status').show().text(inspiryData.loadingmessage);
    $('.login-page #login .primary-button').html('<div class="loader-icon loader--visible"></div>');
    console.log(inspiryData.ajaxurl);
    $.ajax({
      type: 'POST',
      dataType: 'json',
      url: inspiryData.ajaxurl,
      data: {
        'action': 'ajaxlogin',
        //calls wp_ajax_nopriv_ajaxlogin
        'username': $('form#login #username').val(),
        'password': $('form#login #password').val(),
        'security': $('form#login #security').val()
      },
      success: function (data) {
        $('form#login p.status').text(data.message);

        if (data.loggedin == true) {
          $('.login-page #login .primary-button').html('SIGNED IN"></div>'); // set auth token 

          const authToken = new _AuthToken__WEBPACK_IMPORTED_MODULE_0__["default"](redirectLink, $('form#login #username').val(), $('form#login #password').val());
        }

        $('.login-page #login .primary-button').html('SIGN IN');
        const authToken = new _AuthToken__WEBPACK_IMPORTED_MODULE_0__["default"](redirectLink, $('form#login #username').val(), $('form#login #password').val());
      }
    });
  }

}

/* harmony default export */ __webpack_exports__["default"] = (Login);

/***/ }),

/***/ "./src/modules/CartModal/CartModal.js":
/*!********************************************!*\
  !*** ./src/modules/CartModal/CartModal.js ***!
  \********************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
let $ = jQuery;

class CartModal {
  constructor() {
    this.events();
  }

  events() {
    this.showModal(); // hide modal 

    $('.modal-section .fa-times').on('click', this.hideModal);
  }

  showModal(e) {
    setTimeout(() => {
      $('.modal-section').show(200);

      if ($('.modal-section').data('overlay') === true) {}
    }, 3000);
  }

  hideModal() {
    $('.modal-section').hide(200);
    $('.overlay').hide();
  }

}

/* harmony default export */ __webpack_exports__["default"] = (CartModal);

/***/ }),

/***/ "./src/modules/CustomerService/ContactForm.js":
/*!****************************************************!*\
  !*** ./src/modules/CustomerService/ContactForm.js ***!
  \****************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _Form_GeneralFormProcessor__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../Form/GeneralFormProcessor */ "./src/modules/Form/GeneralFormProcessor.js");
const $ = jQuery;


class ContactForm {
  constructor() {
    this.events();
  }

  events() {
    $('#contact-form').on('submit', this.contactFormSubmission);
  }

  contactFormSubmission(e) {
    e.preventDefault();
    let formID = '#contact-form';
    let url = window.location.hostname;
    let apiRoute;

    if (url === 'localhost') {
      apiRoute = `/inspirynew/wp-json/inspiry/v1/contact`;
    } else {
      apiRoute = `https://inspiry.co.nz/wp-json/inspiry/v1/contact`;
    }

    let dataObj = {};
    dataObj.firstName = $('#contact-form #first-name').val();
    dataObj.lastName = $('#contact-form #last-name').val();
    dataObj.email = $('#contact-form #email').val();
    dataObj.phone = $('#contact-form #phone-number').val();
    dataObj.enquiry = $('#contact-form #enquiry-term').val();
    dataObj.message = $('#contact-form #message').val();
    dataObj.emailTo = 'support@inspiry.co.nz'; // send data to form processor 

    const generalFormProcessor = new _Form_GeneralFormProcessor__WEBPACK_IMPORTED_MODULE_0__["default"](apiRoute, dataObj, formID);
  }

}

/* harmony default export */ __webpack_exports__["default"] = (ContactForm);

/***/ }),

/***/ "./src/modules/CustomerService/CustomerServiceMenu.js":
/*!************************************************************!*\
  !*** ./src/modules/CustomerService/CustomerServiceMenu.js ***!
  \************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
const $ = jQuery;

class CustomerServiceMenu {
  constructor() {
    this.events();
  }

  events() {
    // add toggle icon in menu link 
    $('#menu-customer-service-sidebar-menu .menu-item-has-children>a').append('<span>+</span>'); // toggle submenu on click 

    $('#menu-customer-service-sidebar-menu .menu-item-has-children>a').on('click', this.toggleSubmenu); // select aria current attribute

    $('#menu-customer-service-sidebar-menu a[aria-current="page"]').closest('.sub-menu').show(); // find if the submenu is open and add "-" in span

    $('#menu-customer-service-sidebar-menu a[aria-current="page"]').closest('.current-menu-parent').find('a span').html("–"); // show mobile menu

    $('.customer-service-page .sidebar-mobile-menu .secondary-button').on('click', this.showMobileNavbar);
  } // toggle submenu 


  toggleSubmenu(e) {
    e.preventDefault();
    $(this).siblings('.sub-menu').slideToggle("fast", function () {
      // toggle the icon by check the current icon of span
      if ($(this).siblings('a').find('span').html() === "+") {
        $(this).siblings('a').find('span').html("–");
      } else {
        $(this).siblings('a').find('span').html("+");
      }
    });
  } // show mobile navbar


  showMobileNavbar() {
    $('.customer-service-page .sidebar-mobile-menu i').toggleClass('arrow-up');
    $('.customer-service-page .sidebar').slideToggle();
  }

}

/* harmony default export */ __webpack_exports__["default"] = (CustomerServiceMenu);

/***/ }),

/***/ "./src/modules/CustomerService/FeedbackForm.js":
/*!*****************************************************!*\
  !*** ./src/modules/CustomerService/FeedbackForm.js ***!
  \*****************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _Form_GeneralFormProcessor__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../Form/GeneralFormProcessor */ "./src/modules/Form/GeneralFormProcessor.js");
const $ = jQuery;


class FeedbackForm {
  constructor() {
    this.events();
  }

  events() {
    $('#feedback-form').on('submit', this.feedbackFormSubmission);
  }

  feedbackFormSubmission(e) {
    e.preventDefault();
    let formID = '#feedback-form';
    let url = window.location.hostname;
    let apiRoute;

    if (url === 'localhost') {
      apiRoute = `/inspirynew/wp-json/inspiry/v1/feedback-email`;
    } else {
      apiRoute = `https://inspiry.co.nz/wp-json/inspiry/v1/feedback-email`;
    }

    let dataObj = {};
    dataObj.firstName = $('#feedback-form #first-name').val();
    dataObj.lastName = $('#feedback-form #last-name').val();
    dataObj.email = $('#feedback-form #email').val();
    dataObj.phone = $('#feedback-form #phone-number').val();
    dataObj.feedback = $('#feedback-form #feedback').val();
    dataObj.emailTo = 'support@inspiry.co.nz';
    console.log(dataObj); // send data to form processor 

    const generalFormProcessor = new _Form_GeneralFormProcessor__WEBPACK_IMPORTED_MODULE_0__["default"](apiRoute, dataObj, formID);
  }

}

/* harmony default export */ __webpack_exports__["default"] = (FeedbackForm);

/***/ }),

/***/ "./src/modules/DesignBoardSaveBtn.js":
/*!*******************************************!*\
  !*** ./src/modules/DesignBoardSaveBtn.js ***!
  \*******************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
let $ = jQuery; //Design board save button

window.productID = 0;
window.productName = 0;

class DesignBoardSaveBtn {
  constructor() {
    this.heartBtn = document.querySelectorAll('.design-board-save-btn-container');
    this.events();
  } //events


  events() {
    // show design board modal 
    $(this.heartBtn).on('click', this.showDesignBoardModal); // hide design board modal 

    $(document).on('click', '.design-board-selection-modal .footer-container .cancel', this.hideDesignBoardModal); // hide design board modal 

    $(document).on('click', '.design-board-selection-modal .fa-xmark', this.hideDesignBoardModal); // hide design board modal when clicked on black overlay 

    $(document).on('click', '.dark-overlay', this.hideDesignBoardModal); // add to board

    $(document).on('click', '.design-board-selection-modal .board-list .list-item .save-btn', this.addToBoard); // show create modal 

    $(document).on('click', '.create-board-container', this.showCreateBoardModal);
  } // show design board list modal 


  showDesignBoardModal(e) {
    $('.design-board-selection-modal').show();
    $('.dark-overlay').show();
    window.productID = $(this).attr('data-id');
    window.productName = $(this).attr('data-name');
  } // hide design board modal 


  hideDesignBoardModal() {
    $('.design-board-selection-modal').hide();
    $('.dark-overlay').hide();
    $('.create-board-modal').hide();
  } // add to board 


  addToBoard(e) {
    const boardID = $(e.target).attr('data-boardid');
    const boardPostStatus = $(e.target).attr('data-poststatus');
    $(e.target).html('<i class="fa-duotone fa-loader fa-spin"></i>'); //add to board

    $.ajax({
      beforeSend: xhr => {
        xhr.setRequestHeader('X-WP-NONCE', inspiryData.nonce);
      },
      url: inspiryData.root_url + '/wp-json/inspiry/v1/add-to-board',
      type: 'POST',
      data: {
        'boardID': boardID,
        'productID': window.productID,
        'postTitle': window.productName,
        'status': boardPostStatus
      },
      complete: () => {
        console.log('saved');
      },
      success: response => {
        console.log('this is a success area');

        if (response) {
          console.log(response); // $('.design-board-save-btn-container i').attr('data-exists', 'yes');
          // //fill heart
          // $('.design-board-save-btn-container i').addClass('fas fa-heart');

          $(e.target).html('Saved');
        }
      },
      error: response => {
        console.log('this is an error');
        console.log(response);
        $(e.target).html('Error');
      }
    });
  }

  showCreateBoardModal(e) {
    $('.create-board-modal').show();
    $('.design-board-selection-modal').hide(); // submit form 

    let boardName;
    let boardStatus;
    $('#create-board-form').submit(e => {
      e.preventDefault();
      boardName = $('#board-name').val();
      boardStatus = $('#board-checkbox').is(":checked") ? 'private' : 'publish'; // create board

      $(".create-board-modal form button").text('Creating');
      $.ajax({
        beforeSend: xhr => {
          xhr.setRequestHeader('X-WP-NONCE', inspiryData.nonce);
        },
        url: inspiryData.root_url + '/wp-json/inspiry/v1/manage-board',
        type: 'POST',
        data: {
          'boardName': boardName,
          'status': boardStatus,
          'boardDescription': 'description is here'
        },
        complete: () => {
          console.log('completed');
        },
        success: response => {
          console.log(response);

          if (response) {
            let boardID = response;
            addToBoard(boardID, boardStatus);
          }
        },
        error: response => {
          console.log('this is an error');
          console.log(response);
          $('.create-board-modal form .error').text(response.responseText);
          $(".create-board-modal form button").text('Create');
        }
      });
    }); // add product to board after board is created 

    const addToBoard = (boardID, boardPostStatus) => {
      console.log(boardID);
      console.log(boardPostStatus);
      console.log(window.productID);
      console.log(window.productName); //add to board

      $.ajax({
        beforeSend: xhr => {
          xhr.setRequestHeader('X-WP-NONCE', inspiryData.nonce);
        },
        url: inspiryData.root_url + '/wp-json/inspiry/v1/add-to-board',
        type: 'POST',
        data: {
          'boardID': boardID,
          'productID': window.productID,
          'postTitle': window.productName,
          'status': boardPostStatus
        },
        complete: () => {
          console.log('saved');
        },
        success: response => {
          console.log('this is a success area');

          if (response) {
            console.log(response);
            $(".create-board-modal form button").text('Created');
            location.reload();
          }
        },
        error: response => {
          console.log('this is an error');
          console.log(response);
          $(".create-board-modal form button").text('Create');
          $('.create-board-modal form .error').text('Something went wrong');
        }
      });
    };
  }

}

/* harmony default export */ __webpack_exports__["default"] = (DesignBoardSaveBtn);

/***/ }),

/***/ "./src/modules/EnquiryModal/EnquiryModal.js":
/*!**************************************************!*\
  !*** ./src/modules/EnquiryModal/EnquiryModal.js ***!
  \**************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
let $ = jQuery;

class EnquiryModal {
  constructor() {
    this.events();
  }

  events() {
    $('#enquire-button').on('click', this.showEnquiryModal); // hide modal 

    $('.enquiry-form-section .fa-times').on('click', this.hideEnquiryModal);
  }

  showEnquiryModal(e) {
    e.preventDefault();
    $('.enquiry-form-section').show(200);
    $('.overlay').show();
  }

  hideEnquiryModal() {
    $('.enquiry-form-section').hide(200);
    $('.overlay').hide();
  }

}

/* harmony default export */ __webpack_exports__["default"] = (EnquiryModal);

/***/ }),

/***/ "./src/modules/ErrorModal/ErrorModal.js":
/*!**********************************************!*\
  !*** ./src/modules/ErrorModal/ErrorModal.js ***!
  \**********************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
const $ = jQuery;

class ErrorModal {
  constructor() {
    this.dismissBtn = $('.error-modal button');
    this.events();
  }

  events() {
    this.dismissBtn.on('click', this.hideModal);
    $('.error-modal').on('click', this.hideModal);
  }

  hideModal() {
    $('.error-modal').hide();
  }

}

/* harmony default export */ __webpack_exports__["default"] = (ErrorModal);

/***/ }),

/***/ "./src/modules/FacetFilter/FacetFilter.js":
/*!************************************************!*\
  !*** ./src/modules/FacetFilter/FacetFilter.js ***!
  \************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
const $ = jQuery;

class FacetFilter {
  constructor() {
    // mobile and desktop filter show/hide
    this.closeButton = $('.mobile-filter-container .close-button');
    this.closeIcon = $('.mobile-filter-container .close-icon');
    this.showResultsButton = $('.mobile-filter-container .primary-button'); // desktop filter show 

    this.filterButton = $('.filter-sort-container .filter-button'); // facet label button

    this.labelButton = $('.facet-label-button');
    this.events();
  }

  events() {
    $(window).scroll(function (event) {
      var scroll = $(window).scrollTop(); // Do something

      if (scroll > 300) {
        $('.fixed-filter-button').slideDown();
      } else {
        $('.fixed-filter-button').slideUp();
      }
    }); // show filter container

    this.filterButton.on('click', this.showDesktopContainer); // hide filter container

    this.closeIcon.on('click', this.hideDesktopContainer);
    this.showResultsButton.on('click', this.hideDesktopContainer); // show filter when clicked on label desktop 

    this.labelButton.on('click', this.showFilter);
  } // show desktop filter container on button click


  showDesktopContainer() {
    if (window.matchMedia("(max-width: 1100px)").matches) {
      $('.facet-wp-container').slideDown('slow');
    } else {
      $('.facet-wp-container').slideToggle('slow');
    }

    if ($('.filter-sort-container .filter-button span').text() === 'Show Filters') {
      $('.filter-sort-container .filter-button span').text('Hide Filters');
    } else {
      $('.filter-sort-container .filter-button span').text('Show Filters');
    }
  }

  hideDesktopContainer() {
    console.log('clicked close button');
    $('.filter-sort-container .filter-button span').text('Show Filters');
    $('.facet-wp-container').hide('slow');
  }

  showFilter(e) {
    console.log(e);
    $(this).siblings('.facetwp-facet').slideToggle('fast');
    $(this).find('i').toggleClass('fa-plus');
    $(this).find('i').toggleClass('fa-minus');
  }

}

/* harmony default export */ __webpack_exports__["default"] = (FacetFilter);

/***/ }),

/***/ "./src/modules/Form/Form.js":
/*!**********************************!*\
  !*** ./src/modules/Form/Form.js ***!
  \**********************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var jquery__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! jquery */ "jquery");
/* harmony import */ var jquery__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(jquery__WEBPACK_IMPORTED_MODULE_0__);

const $ = jQuery;

class Form {
  constructor() {
    this.enquiryForm = $('#enquiry-form');
    this.events();
  }

  events() {
    this.enquiryForm.on('submit', this.enquiryFormProcessor.bind(this));
  }

  enquiryFormProcessor(e) {
    let dataObj = this.getFormData(e, '#enquiry-form'); // this.sendMailchimpReq(dataObj, 'wp-json/inspiry/v1/enquiry-mailchimp')

    this.sendRequest(dataObj, 'wp-json/inspiry/v1/enquiry-email', '#enquiry-form');
  } // send data to mailchimp 


  sendMailchimpReq(dataObj, fileName, formID) {
    const jsonData = JSON.stringify(dataObj);
    let xhr = new XMLHttpRequest();
    let url = window.location.hostname;
    let filePath;

    if (url === 'localhost') {
      filePath = `http://localhost/${fileName}`;
    } else {
      filePath = `https://inspiry.co.nz/${fileName}`;
    }

    xhr.open('POST', filePath);
    xhr.setRequestHeader('Content-Type', 'application/json');

    xhr.onload = function () {
      $(`${formID} p`).html('');
      console.log("mailchimp response");
      console.log(xhr.response);
    };

    xhr.send(jsonData);
  } // send request function


  sendRequest(dataObj, fileName, formID) {
    // change button to loading icon
    $('#enquiry-form button').html('<div class="loader-icon loader--visible"></div>');
    let filePath;
    let url = window.location.hostname;

    if (url === 'localhost') {
      filePath = `http://localhost/${fileName}`;
    } else {
      filePath = `https://inspiry.co.nz/${fileName}`;
    }

    const jsonData = JSON.stringify(dataObj);
    let xhr = new XMLHttpRequest();
    xhr.open('POST', filePath);
    xhr.setRequestHeader('Content-Type', 'application/json');

    xhr.onload = function () {
      // remove loader icon
      // $('.loader-icon').remove()
      // show button
      $('#enquiry-form button').html("Sent");
      $(`${formID} p`).html('');

      if (xhr.status == 200) {
        console.log(xhr);
        $($(formID).prop('elements')).each(function (i) {
          if (this.value !== 'Submit') {
            this.value = ""; // uncheck the checked box 

            $('#newsletter').prop('checked', false);
          }
        });
        $(formID).append('<p class="success-msg paragraph regular success">Thanks for contacting us!</p>');
        setTimeout(() => {
          $('.enquiry-form-section').hide();
          $('.overlay').hide();
        }, 4000);
      } else {
        console.log('this is an error');
        $(formID).append('<p class="error-msg paragraph regular error">Something went wrong. Please try again!</p>');
      }
    };

    xhr.send(jsonData);
  }

  getFormData(e, formID) {
    e.preventDefault();
    var dataObj = {};
    $($(formID).prop('elements')).each(function (i) {
      dataObj[$(this).attr('name')] = this.value;
      dataObj[$(this).attr('last-name')] = this.value;
    }); // check if the checkbox is checked 

    if ($('#enquiry-form #newsletter:checked').length > 0) {
      dataObj.newsletter = 'Yes';
    } else {
      dataObj.newsletter = 'No';
    } // send custom data


    let productID = $(this.enquiryForm).data('id');
    let productName = $(this.enquiryForm).data('name');

    if (productID && productName) {
      dataObj.productID = productID;
      dataObj.productName = productName;
    }

    dataObj.emailTo = "hello@inspiry.co.nz";
    return dataObj;
  }

}

/* harmony default export */ __webpack_exports__["default"] = (Form);

/***/ }),

/***/ "./src/modules/Form/GeneralFormProcessor.js":
/*!**************************************************!*\
  !*** ./src/modules/Form/GeneralFormProcessor.js ***!
  \**************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
// use this to send data for any kind of form 
const $ = jQuery;

class GeneralFormProcessor {
  constructor(apiRoute, dataObj, formID) {
    this.apiRoute = apiRoute;
    this.dataObj = dataObj;
    this.formID = formID;
    this.events();
  }

  events() {
    console.log(this.formID); // send data to rest email api 

    const jsonData = JSON.stringify(this.dataObj);
    let xhr = new XMLHttpRequest(); // add a loader in a button

    $(`${this.formID} .primary-button`).html('<div class="loader-icon loader--visible"></div>');
    const formID = this.formID;
    xhr.open('POST', this.apiRoute);
    xhr.setRequestHeader('Content-Type', 'application/json');

    xhr.onload = function () {
      console.log(xhr);

      if (xhr.status === 200) {
        $(`${formID} .primary-button`).html('SENT');
        $(formID).append('<p class="success-msg paragraph regular success right-align">Thanks for contacting us!</p>');
      } else {
        console.log('this is an error');
        $(`${formID} .primary-button`).html('SEND');
        $(formID).append('<p class="error-msg paragraph regular error">Something went wrong. Please try again!</p>');
      }
    };

    xhr.send(jsonData);
  }

}

/* harmony default export */ __webpack_exports__["default"] = (GeneralFormProcessor);

/***/ }),

/***/ "./src/modules/Header.js":
/*!*******************************!*\
  !*** ./src/modules/Header.js ***!
  \*******************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
const $ = jQuery;

class Header {
  constructor() {
    this.events();
  }

  events() {
    // show sign in modal 
    $('.useful-links-container .sign-in-container').hover(this.showSignInModal, this.hideSignInModal); // show design boards header modal 

    $('.useful-links-container .design-board-icon-container').hover(this.showDesignBoardModal, this.hideDesignBoardModal);
  }

  showSignInModal() {
    $('.useful-links-container .sign-in-modal').show();
  }

  hideSignInModal() {
    $('.useful-links-container .sign-in-modal').hide();
  } // design board modal 


  showDesignBoardModal() {
    $('.useful-links-container .design-board-header-modal').show();
  }

  hideDesignBoardModal() {
    $('.useful-links-container .design-board-header-modal').hide();
  }

}

/* harmony default export */ __webpack_exports__["default"] = (Header);

/***/ }),

/***/ "./src/modules/MobileSearch.js":
/*!*************************************!*\
  !*** ./src/modules/MobileSearch.js ***!
  \*************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
let $ = jQuery;

class MobileSearch {
  // describe and create/initiate our object
  constructor() {
    this.url = window.location.hostname === "localhost" ? "http://localhost/inspirynew/wp-json/inspiry/v1/search?term=" : "https://inspiry.co.nz/wp-json/inspiry/v1/search?term=";
    this.allProductsURL = window.location.hostname === "localhost" ? "http://localhost/inspirynew/wp-json/inspiry/v1/all-products-search?term=" : "https://inspiry.co.nz/wp-json/inspiry/v1/all-products-search?term=";
    this.loading = $('.fa-spinner');
    this.searchIcon = $('.search-code .fa-search');
    this.resultDiv = $('.search-code .result-div');
    this.searchField = $('#mobile-search-term');
    this.typingTimer;
    this.searchBar = $('.search-bar');
    this.events();
    this.isSpinnerVisible = false;
    this.previousValue;
  } // events 


  events() {
    this.searchField.on("keyup", this.typingLogic.bind(this));
    this.searchField.on("click", this.searchFieldClickHandler.bind(this));
    $(document).on("click", this.documentClickHandler.bind(this));
  } // document click handler


  documentClickHandler(e) {
    if (!this.searchBar.is(e.target) && this.searchBar.has(e.target).length === 0) {
      this.resultDiv.hide();
    }
  } // search field click


  searchFieldClickHandler() {
    this.resultDiv.show();
  } // methods


  typingLogic() {
    if (this.searchField.val() != this.previousValue) {
      clearTimeout(this.typingTimer); // check if the value is not empty

      if (this.searchField.val()) {
        if (!this.isSpinnerVisible) {
          // show loading spinner
          this.loading.show();
          this.isSpinnerVisible = true;
        }

        this.typingTimer = setTimeout(this.getResults.bind(this), 2000);
      } else {
        // hide loading
        this.loading.hide();
        this.isSpinnerVisible = false;
      }
    }

    this.previousValue = this.searchField.val();
  } // get result method


  async getResults() {
    // send request 
    $.getJSON(`${this.url}${this.searchField.val()}`, data => {
      this.resultDiv.show();

      if (data.length) {
        this.resultDiv.html(`<ul class="search-list">
                ${data.map(item => {
          return `<li>
                    <a href="${item.link}"> 
                    <img src="${item.image}" alt=${item.title}/>
                    <span>${item.title}</span>
                    </a>
                    </li>`;
        }).join('')}
                </ul>`); // get rest of the query projects

        $.getJSON(`${this.allProductsURL}${this.searchField.val()}`, allProducts => {
          if (allProducts.length) {
            $('.search-list').append(` ${allProducts.map(item => {
              return `<li>
                            <a href="${item.link}"> 
                            <img src="${item.image}" alt=${item.title}/>
                            <span>${item.title}</span>
                            </a>
                            </li>`;
            }).join('')}`);
          }
        });
      } else {
        this.resultDiv.html(`<p class="center-align medium">Nothing found</p>`);
      } // hide loading spinner 


      if (this.isSpinnerVisible) {
        this.loading.hide();
        this.isSpinnerVisible = false;
      }
    });
  }

}

/* harmony default export */ __webpack_exports__["default"] = (MobileSearch);

/***/ }),

/***/ "./src/modules/OwlCarousel/EveryOwlCarousel.js":
/*!*****************************************************!*\
  !*** ./src/modules/OwlCarousel/EveryOwlCarousel.js ***!
  \*****************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _OwlCarousel__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./OwlCarousel */ "./src/modules/OwlCarousel/OwlCarousel.js");
/* harmony import */ var owl_carousel_dist_assets_owl_carousel_css__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! owl.carousel/dist/assets/owl.carousel.css */ "./node_modules/owl.carousel/dist/assets/owl.carousel.css");
/* harmony import */ var owl_carousel__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! owl.carousel */ "./node_modules/owl.carousel/dist/owl.carousel.js");
/* harmony import */ var owl_carousel__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(owl_carousel__WEBPACK_IMPORTED_MODULE_2__);



let $ = jQuery;

class EveryOwlCarousel {
  constructor() {
    this.events();
  }

  events() {
    //trending section carousel 
    this.trendingCarousel(); // this.brandLogoHomePageCarousel();
    // product gallery on single product page

    this.productGallery(); // // banner carousel 

    this.banner(); // recently viewed carousel 

    this.recentlyViewedCarousel(); // home page category cards 

    this.homeCategoryCards(); // be inspired home page

    this.beInspiredHome();
  } // banner carousel 


  banner() {
    // // owl carousel 
    let className = '.banner-container .owl-carousel';
    let args = {
      lazyLoad: true,
      autoplay: true,
      autoplayTimeout: 5000,
      autoplayHoverPause: true,
      responsiveBaseElement: ".row-container",
      responsiveClass: true,
      rewind: true,
      dots: false,
      loop: true,
      responsive: {
        0: {
          items: 1,
          dots: false
        }
      }
    };
    const banner = new _OwlCarousel__WEBPACK_IMPORTED_MODULE_0__["default"](args, className);
  }

  productGallery() {
    // // owl carousel 
    // $('.single-product .flex-control-thumbs').addClass('splide');
    let className = '.woocommerce-product-gallery .owl-carousel';
    let args = {
      margin: 20,
      autoplay: true,
      autoplayTimeout: 2000,
      autoplayHoverPause: true,
      responsiveBaseElement: ".row-container",
      responsiveClass: true,
      rewind: true,
      responsive: {
        0: {
          items: 4,
          dots: true
        },
        600: {
          items: 4,
          dots: true
        }
      }
    }; // const trendingNow = new OwlCarousel(args, className);
  }

  brandLogoHomePageCarousel() {
    // owl carousel 
    let className = '.brand-logo-section .owl-carousel';
    let args = {
      loop: true,
      navText: "G",
      margin: 20,
      lazyLoad: true,
      autoplay: true,
      autoplayTimeout: 2000,
      autoplayHoverPause: true,
      responsiveBaseElement: ".row-container",
      responsiveClass: true,
      rewind: true,
      responsive: {
        0: {
          items: 1,
          dots: true
        },
        600: {
          items: 2,
          dots: true
        },
        900: {
          items: 3,
          dots: true
        },
        1200: {
          items: 3,
          dots: true
        },
        1500: {
          items: 4,
          dots: true
        }
      }
    };
    const trendingNow = new _OwlCarousel__WEBPACK_IMPORTED_MODULE_0__["default"](args, className);
  }

  trendingCarousel() {
    // owl carousel 
    let className = '.trending-section .owl-carousel';
    let args = {
      loop: true,
      navText: ['<i class="fa-thin fa-arrow-left-long"></i>', '<i class="fa-thin fa-arrow-right-long"></i>'],
      margin: 20,
      center: true,
      lazyLoad: true,
      responsiveBaseElement: ".row-container",
      responsiveClass: true,
      rewind: true,
      mouseDrag: true,
      touchDrag: true,
      nav: true,
      responsive: {
        0: {
          items: 1,
          dots: false
        },
        600: {
          items: 2,
          dots: false
        },
        700: {
          items: 3,
          dots: false
        },
        1440: {
          items: 3,
          dots: false
        }
      }
    };
    const trendingNow = new _OwlCarousel__WEBPACK_IMPORTED_MODULE_0__["default"](args, className);
  }

  recentlyViewedCarousel() {
    // owl carousel 
    let className = '.recently-viewed-section .owl-carousel';
    let args = {
      loop: true,
      navText: ['<i class="fa-thin fa-arrow-left-long"></i>', '<i class="fa-thin fa-arrow-right-long"></i>'],
      margin: 20,
      center: true,
      lazyLoad: true,
      responsiveBaseElement: ".row-container",
      responsiveClass: true,
      rewind: true,
      mouseDrag: true,
      touchDrag: true,
      nav: true,
      responsive: {
        0: {
          navText: ['<i class="fa-thin fa-arrow-left-long"></i>', '<i class="fa-thin fa-arrow-right-long"></i>'],
          items: 1,
          dots: false
        },
        600: {
          navText: ['<i class="fa-thin fa-arrow-left-long"></i>', '<i class="fa-thin fa-arrow-right-long"></i>'],
          items: 2,
          dots: false
        },
        900: {
          items: 3,
          dots: false
        },
        1440: {
          items: 3,
          dots: false
        }
      }
    };
    const recentlyViewed = new _OwlCarousel__WEBPACK_IMPORTED_MODULE_0__["default"](args, className);
  } // home page category cards


  homeCategoryCards() {
    // owl carousel 
    let className = '.home .category-cards-section .owl-carousel';
    let args = {
      mouseDrag: true,
      touchDrag: true,
      nav: true,
      lazyLoad: true,
      loop: true,
      autoplay: true,
      autoplayTimeout: 5000,
      autoplayHoverPause: true,
      navText: ['<i class="fa-thin fa-arrow-left-long"></i>', '<i class="fa-thin fa-arrow-right-long"></i>'],
      margin: 20,
      responsive: {
        0: {
          items: 1,
          dots: false
        },
        600: {
          items: 2,
          dots: false
        },
        900: {
          items: 3,
          dots: false
        },
        1350: {
          loop: false,
          autoplay: false,
          items: 4,
          dots: false
        }
      }
    };
    const homeCategoryCards = new _OwlCarousel__WEBPACK_IMPORTED_MODULE_0__["default"](args, className);
  } // home page be inspired


  beInspiredHome() {
    // owl carousel 
    let className = '.home .be-inspired-section .owl-carousel';
    let args = {
      mouseDrag: true,
      touchDrag: true,
      nav: true,
      lazyLoad: true,
      loop: true,
      autoplay: true,
      autoplayTimeout: 5000,
      autoplayHoverPause: true,
      navText: ['<i class="fa-thin fa-arrow-left-long"></i>', '<i class="fa-thin fa-arrow-right-long"></i>'],
      responsive: {
        0: {
          items: 1,
          dots: false
        },
        600: {
          items: 2,
          dots: false
        },
        900: {
          loop: false,
          autoplay: false,
          items: 3,
          dots: false
        }
      }
    };
    const homeCategoryCards = new _OwlCarousel__WEBPACK_IMPORTED_MODULE_0__["default"](args, className);
  }

}

/* harmony default export */ __webpack_exports__["default"] = (EveryOwlCarousel);

/***/ }),

/***/ "./src/modules/OwlCarousel/OwlCarousel.js":
/*!************************************************!*\
  !*** ./src/modules/OwlCarousel/OwlCarousel.js ***!
  \************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var owl_carousel_dist_assets_owl_carousel_css__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! owl.carousel/dist/assets/owl.carousel.css */ "./node_modules/owl.carousel/dist/assets/owl.carousel.css");
/* harmony import */ var owl_carousel__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! owl.carousel */ "./node_modules/owl.carousel/dist/owl.carousel.js");
/* harmony import */ var owl_carousel__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(owl_carousel__WEBPACK_IMPORTED_MODULE_1__);
let $ = jQuery;



class OwlCarousel {
  constructor(args, className) {
    this.events(args, className);
  }

  events(args, className) {
    $(className).owlCarousel(args);
  }

}

/* harmony default export */ __webpack_exports__["default"] = (OwlCarousel);

/***/ }),

/***/ "./src/modules/PopUpCart.js":
/*!**********************************!*\
  !*** ./src/modules/PopUpCart.js ***!
  \**********************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
const $ = jQuery;

class PopUpCart {
  constructor() {
    this.events();
  }

  events() {
    $('.variable-item').on('click', () => {
      let formData = $('form.cart').data('product_variations');
    });
    $('.header .shopping-cart').on('click', this.openCart);
    $(document).on('click', '.cart-box .cont-shopping a', this.closeCart);
    $(document).on('click', '.dark-overlay', this.closeCart);
    $(document).on('click', '.cart-popup-container .title-section i', this.closeCart); // $('.cart-popup-container .fa-times').on('click', this.closeCart)

    $(document).on('click', '.single_add_to_cart_button', this.ajaxAddToCart); // remove item from cart ajax 

    $(document).on('click', '.cart-popup-container .fa-times', this.removeItem); // plus minus quantity button 

    $('form.cart').on('click', ' .plus, .minus', this.plusMinusButtons);
  } //remove item from cart function 


  removeItem(e) {
    e.preventDefault();
    var productId = $(this).attr("data-productid"),
        cart_item_key = $(this).attr("data-cart_item_key"),
        product_container = $(this).parents('.product-card');
    console.log(productId);
    console.log(cart_item_key); // Add loader

    product_container.block({
      message: null,
      overlayCSS: {
        cursor: 'none'
      }
    });
    $.ajax({
      type: 'POST',
      dataType: 'json',
      url: wc_add_to_cart_params.ajax_url,
      data: {
        action: "product_remove",
        product_id: productId,
        cart_item_key: cart_item_key
      },
      success: function (response) {
        console.log(response);
        if (!response || response.error) return;
        var fragments = response.fragments; // Replace fragments

        if (fragments) {
          $.each(fragments, function (key, value) {
            $(key).replaceWith(value);
          });
        }
      }
    });
  } //close cart
  // open cart


  openCart(event) {
    event.preventDefault();
    console.log('slide down cart');
    $('.cart-popup-container').slideToggle('slow');
    $('.header .shopping-cart a i').toggleClass('fa-chevron-up');
    $('.dark-overlay').show();
  }

  closeCart() {
    $('.cart-popup-container').slideUp('slow');
    $('.header .shopping-cart a i').removeClass('fa-chevron-up');
    $('.dark-overlay').hide();
  }

  ajaxAddToCart(e) {
    console.log(wc_add_to_cart_params.ajax_url);
    e.preventDefault();
    let thisbutton = $(this),
        $form = thisbutton.closest('form.cart'),
        id = thisbutton.val(),
        product_qty = $form.find('input[name=quantity]').val() || 1,
        product_id = $form.find('input[name=product_id]').val() || id,
        variation_id = $form.find('input[name=variation_id]').val() || 0;
    var data = {
      action: 'woocommerce_ajax_add_to_cart',
      product_id: product_id,
      product_sku: '',
      quantity: product_qty,
      variation_id: variation_id
    };
    $(document.body).trigger('adding_to_cart', [thisbutton, data]);
    $.ajax({
      type: 'post',
      url: '/wp-admin/admin-ajax.php',
      data: data,
      beforeSend: function (response) {
        thisbutton.removeClass('added').addClass('loading');
      },
      complete: function (response) {
        thisbutton.addClass('added').removeClass('loading');
      },
      success: function (response) {
        $('.cart-popup-container').slideDown();
        $('.dark-overlay').show(); // setTimeout(function () { $('.cart-popup-container').slideUp('slow'); }, 3000);

        if (response.error & response.product_url) {
          window.location = response.product_url;
          return;
        } else {
          $(document.body).trigger('added_to_cart', [response.fragments, response.cart_hash, thisbutton]);
        }
      }
    });
  }

  plusMinusButtons() {
    // Get current quantity values
    var qty = $(this).closest('form.cart').find('.qty');
    var val = parseFloat(qty.val());
    var max = parseFloat(qty.attr('max'));
    var min = parseFloat(qty.attr('min'));
    var step = parseFloat(qty.attr('step')); // Change the value if plus or minus

    if ($(this).is('.plus')) {
      if (max && max <= val) {
        qty.val(max);
      } else {
        qty.val(val + step);
      }
    } else {
      if (min && min >= val) {
        qty.val(min);
      } else if (val > 1) {
        qty.val(val - step);
      }
    }
  }

}

/* harmony default export */ __webpack_exports__["default"] = (PopUpCart);

/***/ }),

/***/ "./src/modules/Search.js":
/*!*******************************!*\
  !*** ./src/modules/Search.js ***!
  \*******************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
let $ = jQuery;

class Search {
  // describe and create/initiate our object
  constructor() {
    this.url = window.location.hostname === "localhost" ? "http://localhost/inspirynew/wp-json/inspiry/v1/search?term=" : "https://inspiry.co.nz/wp-json/inspiry/v1/search?term=";
    this.allProductsURL = window.location.hostname === "localhost" ? "http://localhost/inspirynew/wp-json/inspiry/v1/all-products-search?term=" : "https://inspiry.co.nz/wp-json/inspiry/v1/all-products-search?term=";
    this.loading = $('.fa-spinner');
    this.searchIcon = $('.search-code .fa-search');
    this.resultDiv = $('.search-code .result-div');
    this.searchField = $('#search-term');
    this.typingTimer;
    this.searchBar = $('.search-bar');
    this.events();
    this.isSpinnerVisible = false;
    this.previousValue;
  } // events 


  events() {
    this.searchField.on("keyup", this.typingLogic.bind(this));
    this.searchField.on("click", this.searchFieldClickHandler.bind(this));
    $(document).on("click", this.documentClickHandler.bind(this));
  } // document click handler


  documentClickHandler(e) {
    if (!this.searchBar.is(e.target) && this.searchBar.has(e.target).length === 0) {
      this.resultDiv.hide();
    }
  } // search field click


  searchFieldClickHandler() {
    this.resultDiv.show();
  } // methods


  typingLogic() {
    if (this.searchField.val() != this.previousValue) {
      clearTimeout(this.typingTimer); // check if the value is not empty

      if (this.searchField.val()) {
        if (!this.isSpinnerVisible) {
          // show loading spinner
          this.loading.show();
          this.isSpinnerVisible = true;
        }

        this.typingTimer = setTimeout(this.getResults.bind(this), 2000);
      } else {
        // hide loading
        this.loading.hide();
        this.isSpinnerVisible = false;
      }
    }

    this.previousValue = this.searchField.val();
  } // get result method


  async getResults() {
    // send request 
    $.getJSON(`${this.url}${this.searchField.val()}`, data => {
      this.resultDiv.show();

      if (data.length) {
        this.resultDiv.html(`<ul class="search-list">
                ${data.map(item => {
          return `<li>
                    <a href="${item.link}"> 
                    <img src="${item.image}" alt=${item.title}/>
                    <span>${item.title}</span>
                    </a>
                    </li>`;
        }).join('')}
                </ul>`); // get rest of the query projects

        $.getJSON(`${this.allProductsURL}${this.searchField.val()}`, allProducts => {
          if (allProducts.length) {
            $('.search-list').append(` ${allProducts.map(item => {
              return `<li>
                            <a href="${item.link}"> 
                            <img src="${item.image}" alt=${item.title}/>
                            <span>${item.title}</span>
                            </a>
                            </li>`;
            }).join('')}`);
          }
        });
      } else {
        this.resultDiv.html(`<p class="center-align medium">Nothing found</p>`);
      } // hide loading spinner 


      if (this.isSpinnerVisible) {
        this.loading.hide();
        this.isSpinnerVisible = false;
      }
    });
  }

}

/* harmony default export */ __webpack_exports__["default"] = (Search);

/***/ }),

/***/ "./src/modules/ShopFav.js":
/*!********************************!*\
  !*** ./src/modules/ShopFav.js ***!
  \********************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
let $ = jQuery;

class ShopFav {
  constructor() {
    this.button = $('.inspiry-blogs .fourth-section .nav-buttons button');
    this.events();
  }

  events() {
    this.button.on('click', this.showProducts);
  }

  showProducts(e) {
    let targetVal = $(e.target).html();
    console.log(targetVal);

    if (targetVal == 'Furniture') {
      $(e.target).siblings().removeClass('button-border');
      $(e.target).addClass('button-border');
      $(e.target).closest('.flex-container').find('.flex').removeClass('--visible-flex');
      $(e.target).closest('.flex-container').find('.furniture').addClass('--visible-flex');
    } else if (targetVal == 'Wallpaper') {
      $(e.target).siblings().removeClass('button-border');
      $(e.target).addClass('button-border');
      $(e.target).closest('.flex-container').find('.flex').removeClass('--visible-flex');
      $(e.target).closest('.flex-container').find('.wallpaper').addClass('--visible-flex');
    } else if (targetVal == 'Homeware') {
      $(e.target).siblings().removeClass('button-border');
      $(e.target).addClass('button-border');
      $(e.target).closest('.flex-container').find('.flex').removeClass('--visible-flex');
      $(e.target).closest('.flex-container').find('.homeware').addClass('--visible-flex');
    }
  }

}

/* harmony default export */ __webpack_exports__["default"] = (ShopFav);

/***/ }),

/***/ "./src/modules/ToolTip.js":
/*!********************************!*\
  !*** ./src/modules/ToolTip.js ***!
  \********************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
let $ = jQuery;

class ToolTip {
  constructor() {
    $('.be-inspired-section').append(`
                <div class="tooltips poppins-font paragraph-font-size box-shadow">
                    Save to design board
                </div>`);
    $('.design-board-save-btn-container').append(`
                <div class="tooltips poppins-font paragraph-font-size box-shadow">
                    Save to design board
                </div>`);
    this.events();
  }

  events() {
    //show tooltip for be inspired section 
    $('.be-inspired-section').hover(this.showTooltip, this.hideTooltip); // show tool tip for design boards

    $('.design-board-save-btn-container i').hover(this.showTooltip, this.hideTooltip);
  }

  showTooltip(e) {
    console.log('tooltop ');
    $(e.target).siblings('.tooltips').slideDown('200');
  }

  hideTooltip(e) {
    $('.tooltips').hide();
  }

}

/* harmony default export */ __webpack_exports__["default"] = (ToolTip);

/***/ }),

/***/ "./src/modules/TopNav.js":
/*!*******************************!*\
  !*** ./src/modules/TopNav.js ***!
  \*******************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
let $ = jQuery;

class TopNav {
  constructor() {
    this.events();
  }

  events() {
    $('#top-navbar a').mouseover(this.showSubNav);
  }

  showSubNav(e) {
    let linkHTML = $(e.target).html();

    if (linkHTML == 'Design Services') {
      $('.design-services').show(300);
      $("body > *").not(e.target).closest('.top-navbar').mouseout(() => {
        $('.design-services').hide(1000);
      });
    }
  }

  hideSubnav(e) {
    $('.design-services').hide(1000);
  }

}

/* harmony default export */ __webpack_exports__["default"] = (TopNav);

/***/ }),

/***/ "./src/modules/WallpaperCalc.js":
/*!**************************************!*\
  !*** ./src/modules/WallpaperCalc.js ***!
  \**************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
class WallpaperCalc {
  constructor() {
    this.show();
    this.calc();
  }

  show() {
    //Wallpaper Calculator click event
    const calculatorButton = document.querySelector('.sizing-calculator-button');
    const calculatorOverlay = document.querySelector('.calculator-overlay');
    const overlayBackground = document.querySelector('.overlay-background');
    const closeIcon = document.querySelector('.close');

    if (calculatorButton) {
      calculatorButton.addEventListener('click', () => {
        console.log('button clicked');
        overlayBackground.classList.add('overlay-background--visible');
        calculatorOverlay.classList.add("calculator-overlay--visible");
      });
    }

    if (closeIcon) {
      closeIcon.addEventListener('click', () => {
        overlayBackground.classList.remove('overlay-background--visible');
        calculatorOverlay.classList.remove("calculator-overlay--visible");
      });
    }
  }

  calc() {
    var $j = jQuery.noConflict();
    var WAL = WAL || {};
    var WV = WV || {}; //TODO: Move to unit.

    WV.CALCULATORMODULE = function (current) {
      // public api
      return {
        calculateNumberOfRolls: function (widthMeter, heightMeter, rollWidthCentiMeter, rollHeightMeter, rollPatternRepeatCentiMeter) {
          var rollHeightCm = rollHeightMeter * 100;
          var heightCm = heightMeter * 100;
          var widthCm = widthMeter * 100;
          console.log("calculateNumberOfRolls widthMeter", widthMeter);
          console.log("calculateNumberOfRolls heightMeter", heightMeter);
          console.log("calculateNumberOfRolls rollWidthCentiMeter", rollWidthCentiMeter);
          console.log("calculateNumberOfRolls rollHeightMeter", rollHeightMeter);
          console.log("calculateNumberOfRolls rollPatternRepeatCentiMeter", rollPatternRepeatCentiMeter);
          var stripsRaw = rollHeightCm / (heightCm + rollPatternRepeatCentiMeter);
          var strips = stripsRaw < 0 ? Math.ceil(stripsRaw) : Math.floor(stripsRaw);
          var stripWidth = strips * rollWidthCentiMeter;
          var numRolls = Math.round(widthCm / stripWidth * 10000) / 10000;
          console.log("strips", strips);
          console.log("stripWidth", stripWidth);
          console.log("numRolls", numRolls);
          var numRollsRoundedUp = Math.ceil(numRolls);
          console.log("numRolls", numRolls);
          var result = {
            numberOfRolls: numRolls,
            numberOfRollsRoundedUp: Math.ceil(numRolls)
          };
          console.log("WV.MODULES.calculateNumberOfRolls result", result);
          return result;
        }
      };
    }();

    $j(document).ready(function ($) {
      $j("#estimate-roll").click(function (event) {
        event.preventDefault();

        var parseAndValidate = function (selector) {
          var $element = $j(selector);
          console.log($element);

          if ($element.val() == '') {
            return 0;
          } else {
            var int_val = $element.val();
            var maybeFloat = parseFloat(int_val.replace(",", "."));

            if ($.isNumeric(maybeFloat)) {
              $element.parent().addClass("has-success");
              $element.parent().removeClass("has-error");
            } else {
              $element.parent().removeClass("has-success");
              $element.parent().addClass("has-error");
            }

            return maybeFloat;
          }
        };

        let rollWidth = parseAndValidate("#calc-roll-width");
        let rollHeight = parseAndValidate("#calc-roll-height");
        let patternRepeat = parseAndValidate("#calc-pattern-repeat");
        let wallCount = 4;
        let rollTotal = 0;

        for (let i = 1; i <= wallCount; i++) {
          let wallWidth = parseAndValidate("#calc-wall-width" + i);
          let wallHeight = parseAndValidate("#calc-wall-height" + i);
          let calculatedResult = WV.CALCULATORMODULE.calculateNumberOfRolls(wallWidth, wallHeight, rollWidth, rollHeight, patternRepeat);
          console.log("wall" + i + " " + calculatedResult.numberOfRolls);
          rollTotal += calculatedResult.numberOfRolls;
          console.log("roll total " + i + " - " + rollTotal);
        }

        console.log("roll total " + rollTotal);

        if (rollTotal.numberOfRollsRoundedUp <= 1) {
          $j(".suffix-singular").show();
          $j(".suffix-plural").hide();
        } else {
          $j(".suffix-singular").hide();
          $j(".suffix-plural").show();
        } //$j(".calc-result").html(rollTotal.numberOfRolls);


        $j(".calc-round").html(Math.ceil(rollTotal)); // var calculatorParams = {
        //     wallWidth: wallWidth,
        //     wallHeight: wallHeight,
        //     rollWidth: rollWidth,
        //     rollHeight: rollHeight,
        //     patternRepeat: patternRepeat
        // };
        // console.log(calculatedResult);
        // console.log("calculator parameters", calculatorParams);
        // console.log(' calculator-button ');
      });
    }); //pop up overlay control
    //fabric calculator

    /*
    let fabricType = document.getElementById('fabric-type'); 
    let fabricWidth = document.getElementById('fabric-width'); 
    let trackLength = document.getElementById('track-length');
    let pattern = document.getElementById('pattern'); 
    let patternInputHorizontal = document.getElementById('pattern-value-hr'); 
    let patternInputVertical = document.getElementById('pattern-value-vr'); 
    let formHiddenFields = document.querySelector('.form-hidden-field'); 
       let calcDataField = document.getElementById('calculated-data'); 
    let fButton = document.getElementById('f-button'); 
       let calForm = document.getElementById('cal-form')
    calForm.addEventListener('submit', (e)=>{
      e.preventDefault(); 
          console.log(fabricWidth.value)
     fabricWidth = parseFloat(fabricWidth.value); 
     trackLength = parseFloat(trackLength.value); 
    console.log("after parse " + fabricWidth); 
     
           let calcData; 
     
     if(fabricType.value == 'inverted' || fabricType.value == 'pencil'){ 
         let a = trackLength * 2; 
         calcData = a/fabricWidth; 
     }
     else { 
         calcData = 20; 
     }
           if(pattern.value == 'yes'){ 
         console.log(pattern.value); 
         
     }
           
           calcDataField.innerHTML = calcData;
     calcData = 0 ; 
     console.log('worked')
       
    })*/
  }

}

/* harmony default export */ __webpack_exports__["default"] = (WallpaperCalc);

/***/ }),

/***/ "./src/modules/Warranty.js":
/*!*********************************!*\
  !*** ./src/modules/Warranty.js ***!
  \*********************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
let $ = jQuery;

class Warranty {
  constructor() {
    this.events();
  }

  events() {
    $('.bc-single-product__warranty h1').append('<i class="fal fa-plus"></i>');
    $(document).on('click', '.bc-single-product__warranty i', this.showContentIcon);
    $(document).on('click', '.bc-single-product__warranty h1', this.showContent);
  }

  showContent(e) {
    $(e.target).closest('h1').next().slideToggle(300);
    $(e.target).closest('h1').siblings('ul').slideToggle(300);
    $(e.target).find('i').toggleClass('fa-plus');
    $(e.target).find('i').toggleClass('fa-minus');
  }

  showContentIcon(e) {
    console.log('worked !');
    $(e.target).toggleClass('fa-plus');
    $(e.target).toggleClass('fa-minus');
  }

}

/* harmony default export */ __webpack_exports__["default"] = (Warranty);

/***/ }),

/***/ "./src/modules/Woocommerce/Cart/Cart.js":
/*!**********************************************!*\
  !*** ./src/modules/Woocommerce/Cart/Cart.js ***!
  \**********************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _RemoveCartItem__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./RemoveCartItem */ "./src/modules/Woocommerce/Cart/RemoveCartItem.js");
/* harmony import */ var _UpdateCart__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./UpdateCart */ "./src/modules/Woocommerce/Cart/UpdateCart.js");


const $ = jQuery;

class Cart {
  constructor() {
    this.plusBtn = $('.woocommerce-cart .quantity-container .plus');
    this.minusBtn = $('.woocommerce-cart .quantity-container .minus');
    this.qtyInputField = $('.woocommerce-cart .quantity-container #cart-quantity');
    this.removeIcon = $('.remove-product i');
    this.events();
  }

  events() {
    this.plusBtn.on('click', this.incrementValue);
    this.minusBtn.on('click', this.decrementValue);
    this.qtyInputField.on('change', this.onQtyChange);
    $(document).on('click', '.remove-product i', this.removeCartItemOnClick);
  }

  incrementValue(e) {
    let qty = $(this).siblings('#cart-quantity');
    let val = parseFloat(qty.val());
    var max = parseFloat(qty.attr('max'));
    var min = parseFloat(qty.attr('min'));
    var cart_item_key = qty.attr('data-cart_item_key');
    var step = 1;

    if (max && max <= val) {
      qty.val(max);
    } else {
      qty.val(val + step);
      let timer = setTimeout(() => {
        const updateCart = new _UpdateCart__WEBPACK_IMPORTED_MODULE_1__["default"](qty.val(), cart_item_key);
      }, 1000);
    }
  }

  decrementValue() {
    let qty = $(this).siblings('#cart-quantity');
    let val = parseFloat(qty.val());
    var max = parseFloat(qty.attr('max'));
    var min = parseFloat(qty.attr('min'));
    var cart_item_key = qty.attr('data-cart_item_key');
    var step = 1;

    if (min && min >= val) {
      qty.val(min);
    } else if (val > 1) {
      qty.val(val - step);
      let timer = setTimeout(() => {
        const updateCart = new _UpdateCart__WEBPACK_IMPORTED_MODULE_1__["default"](qty.val(), cart_item_key);
      }, 1000);
    }
  }

  onQtyChange() {
    let qty = $(this);
    var cart_item_key = qty.attr('data-cart_item_key');
    const updateCart = new _UpdateCart__WEBPACK_IMPORTED_MODULE_1__["default"](qty.val(), cart_item_key);
  }

  removeCartItemOnClick() {
    console.log('remove clicked');
    var cart_item_key = $(this).attr('data-cart_item_key');
    const removeCartItem = new _RemoveCartItem__WEBPACK_IMPORTED_MODULE_0__["default"](0, cart_item_key);
  }

}

/* harmony default export */ __webpack_exports__["default"] = (Cart);

/***/ }),

/***/ "./src/modules/Woocommerce/Cart/Coupon.js":
/*!************************************************!*\
  !*** ./src/modules/Woocommerce/Cart/Coupon.js ***!
  \************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
const $ = jQuery;

class Coupon {
  constructor() {
    this.events();
  }

  events() {
    $('.total-summary .coupon-code-input-container button').on('click', this.applyCoupon);
    $(document).on('click', '.total-summary .coupon-row button', this.removeCoupon);
  }

  applyCoupon(e) {
    const couponCode = $('.total-summary .coupon-code-input-container #coupon').val();
    $.ajax({
      beforeSend: xhr => {
        $('.overlay').show();
        xhr.setRequestHeader('X-WP-NONCE', inspiryData.nonce);
      },
      url: '/wp-admin/admin-ajax.php',
      type: 'POST',
      data: {
        couponCode: couponCode,
        action: 'woocommerce_ajax_add_coupon'
      },
      complete: () => {
        console.log('completed ajax request ');
        $('.overlay').hide();
      },
      success: response => {
        if (response.code === 200) {
          console.log(response);
          $('.overlay').hide(); // refresh cart data 

          $('.total-summary .subtotal-row .amount span').text(response.subtotal);
          $('.total-summary .shipping-row .amount span').text(response.shipping);
          $('.total-summary .tax-row .amount span').text(response.tax);
          $('.total-summary .total-row .amount').html(response.total);
          $(` <ul class="flex-box coupon-row">
                    <li class="title">Coupon: give10</li>
                    <li class="amount">-$<span>10 <button>[Remove]</button></span></li>
                    </ul>`).insertAfter('.subtotal-row'); // hide coupon input field 

          $('.coupon-code-input-container').hide();
        } else {
          console.log(response);
          $('.overlay').hide();
          $('.error-modal .content').text('Coupon does not exist.');
          $('.error-modal').show();
          e.stopPropagation();
        }
      },
      error: response => {
        $('.overlay').hide();
        console.log('this is an error');
        console.log(response);
        $('.error-modal').show();
        $('.error-modal .content').text('An error has occurred while applying coupon. Please try again.');
      }
    });
  }

  removeCoupon() {
    const couponCode = $('.total-summary .coupon-code-input-container #coupon').val();
    $.ajax({
      beforeSend: xhr => {
        $('.overlay').show();
        xhr.setRequestHeader('X-WP-NONCE', inspiryData.nonce);
      },
      url: '/wp-admin/admin-ajax.php',
      type: 'POST',
      data: {
        action: 'woocommerce_ajax_add_coupon',
        couponCode: 'remove'
      },
      complete: () => {
        console.log('completed ajax request ');
      },
      success: response => {
        if (response.code === 202) {
          console.log(response);
          $('.overlay').hide();
          location.reload();
        } else {
          $('.error-modal .content').text('An error has occurred while removing coupon. Please try again.');
          $('.error-modal').show();
        }
      },
      error: response => {
        $('.overlay').hide();
        console.log('this is an error');
        console.log(response);
        $('.error-modal').show();
        $('.error-modal .content').text('An error has occurred while removing coupon. Please try again.');
      }
    });
  }

}

/* harmony default export */ __webpack_exports__["default"] = (Coupon);

/***/ }),

/***/ "./src/modules/Woocommerce/Cart/RemoveCartItem.js":
/*!********************************************************!*\
  !*** ./src/modules/Woocommerce/Cart/RemoveCartItem.js ***!
  \********************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
const $ = jQuery;

class RemoveCartItem {
  constructor(qty, cartItemKey) {
    this.qty = qty;
    this.cartItemKey = cartItemKey;
    this.removeItem();
  }

  removeItem() {
    $.ajax({
      beforeSend: xhr => {
        $('.overlay').show();
        xhr.setRequestHeader('X-WP-NONCE', inspiryData.nonce);
      },
      url: '/wp-admin/admin-ajax.php',
      type: 'POST',
      data: {
        qty: this.qty,
        cartItemKey: this.cartItemKey,
        action: 'woocommerce_ajax_update_cart'
      },
      complete: () => {
        console.log('completed ajax request ');
      },
      success: response => {
        if (response.code === 200) {
          console.log(response);
          $('.overlay').hide();
          $(`.${this.cartItemKey}`).hide();
          $('.total-summary .subtotal-row .amount span').text(response.subtotal);
          $('.total-summary .shipping-row .amount span').text(response.shipping);
          $('.total-summary .tax-row .amount span').text(response.tax);
          $('.total-summary .total-row .amount').html(response.total);
          location.reload();
        } else {
          $('.overlay').hide();
          $('.error-modal .content').text('An error has occurred while removing item. Please try again.');
          $('.error-modal').show();
        }
      },
      error: response => {
        $('.error-modal .content').text('An error has occurred while removing item. Please try again.');
        $('.error-modal').show();
        console.log('this is an error');
        $('.overlay').hide();
        console.log(response);
      }
    });
  }

}

/* harmony default export */ __webpack_exports__["default"] = (RemoveCartItem);

/***/ }),

/***/ "./src/modules/Woocommerce/Cart/UpdateCart.js":
/*!****************************************************!*\
  !*** ./src/modules/Woocommerce/Cart/UpdateCart.js ***!
  \****************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
const $ = jQuery;

class UpdateCart {
  constructor(qty, cartItemKey) {
    this.qty = qty;
    this.cartItemKey = cartItemKey;
    this.events();
  }

  events() {
    $.ajax({
      beforeSend: xhr => {
        $('.overlay').show();
        xhr.setRequestHeader('X-WP-NONCE', inspiryData.nonce);
      },
      url: '/wp-admin/admin-ajax.php',
      type: 'POST',
      data: {
        qty: this.qty,
        cartItemKey: this.cartItemKey,
        action: 'woocommerce_ajax_update_cart'
      },
      complete: () => {
        console.log('completed ajax request ');
      },
      success: response => {
        if (response.code === 200) {
          console.log(response);
          $('.overlay').hide(); // refresh cart data 

          $('.total-summary .subtotal-row .amount span').text(response.subtotal);
          $('.total-summary .shipping-row .amount span').text(response.shipping);
          $('.total-summary .tax-row .amount span').text(response.tax);
          $('.total-summary .total-row .amount').html(response.total);
          $('.cart-items-table .item-subtotal-column .subtotal').html(response.productSubtotal);
          location.reload(); // check if the sale price exist
          // if (response.salePrice && response.salePrice !== response.productPrice) {
          //     location.reload();
          // }
        } else {
          $('.overlay').hide();
          $('.error-modal .content').text('An error has occurred while updating cart. Please try again.');
          $('.error-modal').show();
        }
      },
      error: response => {
        $('.overlay').hide();
        console.log('this is an error');
        console.log(response);
      }
    });
  }

}

/* harmony default export */ __webpack_exports__["default"] = (UpdateCart);

/***/ }),

/***/ "./src/modules/Woocommerce/Checkout/Checkout.js":
/*!******************************************************!*\
  !*** ./src/modules/Woocommerce/Checkout/Checkout.js ***!
  \******************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _Windcave__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./Windcave */ "./src/modules/Woocommerce/Checkout/Windcave.js");

const $ = jQuery;

class Checkout {
  constructor() {
    $(":submit").removeAttr("disabled");
    this.onPaymentSelectionChange;
    this.windcavePaymentSelected = $("input[type='radio'][name='payment_method']:checked").val();
    this.events();
  }

  events() {
    $('#pay-button').on('click', this.showPaymentOptions);
  }

  showPaymentOptions(e) {
    e.preventDefault();

    const validateInputField = (selector, errorText, selectorID, validationFormat) => {
      if (selector.val().length < 1 && !validationFormat) {
        selector.closest('.woocommerce-input-wrapper').append(`<div class="error">${errorText}</div>`);
        $('html, body').animate({
          scrollTop: $(selectorID).offset().top
        }, 100);
        return false;
      } else if (validationFormat === 'email' && !selector.val().match(/^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/)) {
        selector.closest('.woocommerce-input-wrapper').append(`<div class="error">${errorText}</div>`);
        $('html, body').animate({
          scrollTop: $(selectorID).offset().top
        }, 100);
        return false;
      } else if (validationFormat === 'phone' && !selector.val().match(/^[\+]?[(]?[0-9]{3}[)]?[-\s\.]?[0-9]{3}[-\s\.]?[0-9]{1,6}$/im)) {
        selector.closest('.woocommerce-input-wrapper').append(`<div class="error">${errorText}</div>`);
        $('html, body').animate({
          scrollTop: $(selectorID).offset().top
        }, 100);
        return false;
      } else {
        return true;
      }
    }; // validate shipping form 


    const validateShippingForm = () => {
      $('.error').remove();
      let firstName = $('.woocommerce-checkout #shipping_first_name');
      let lastName = $('.woocommerce-checkout #shipping_last_name');
      let address1 = $('.woocommerce-checkout #shipping_address_1');
      let city = $('.woocommerce-checkout #shipping_city');
      let postCode = $('.woocommerce-checkout #shipping_postcode'); // validate first name

      const isFirstNameValid = validateInputField(firstName, 'Please enter your first name', '#billing_first_name'); // validate last name

      const isLastNameValid = validateInputField(lastName, 'Please enter your last name', '#billing_last_name'); // validate address1

      const isAddress1Valid = validateInputField(address1, 'Please enter your street address', '#billing_address_1'); // validate city

      const isCityValid = validateInputField(city, 'Please enter your city', '#billing_city'); // validate post code

      const isPostCodeValid = validateInputField(postCode, 'Please enter your post Code', '#billing_postcode'); // validate phone

      if (isFirstNameValid && isLastNameValid && isAddress1Valid && isCityValid && isPostCodeValid) {
        return true;
      }
    };

    const validateBillingForm = () => {
      $('.error').remove(); // check the required values 

      let firstName = $('.woocommerce-checkout #billing_first_name');
      let lastName = $('.woocommerce-checkout #billing_last_name');
      let address1 = $('.woocommerce-checkout #billing_address_1');
      let city = $('.woocommerce-checkout #billing_city');
      let postCode = $('.woocommerce-checkout #billing_postcode');
      let phone = $('.woocommerce-checkout #billing_phone');
      let emailAddress = $('.woocommerce-checkout #billing_email'); // validate first name

      const isFirstNameValid = validateInputField(firstName, 'Please enter your first name', '#billing_first_name'); // validate last name

      const isLastNameValid = validateInputField(lastName, 'Please enter your last name', '#billing_last_name'); // validate address1

      const isAddress1Valid = validateInputField(address1, 'Please enter your street address', '#billing_address_1'); // validate city

      const isCityValid = validateInputField(city, 'Please enter your city', '#billing_city'); // validate post code

      const isPostCodeValid = validateInputField(postCode, 'Please enter your post Code', '#billing_postcode'); // validate phone

      const isPhoneValid = validateInputField(phone, 'Please enter your phone number', '#billing_phone', 'phone'); // validate email address

      const isEmailAddressValid = validateInputField(emailAddress, 'Please enter your email address', '#billing_email', 'email'); // ship to different address validation

      let shipToDifferentAddress = $("input[type='checkbox'][name='ship_to_different_address']:checked").val();

      if (shipToDifferentAddress) {
        if (isFirstNameValid && isLastNameValid && isAddress1Valid && isCityValid && isPostCodeValid && isPhoneValid && isEmailAddressValid && validateShippingForm()) {
          return true;
        }
      } else {
        if (isFirstNameValid && isLastNameValid && isAddress1Valid && isCityValid && isPostCodeValid && isPhoneValid && isEmailAddressValid) {
          return true;
        }
      }
    };

    if (validateBillingForm()) {
      $('#payment').show(); // hide the pay now button 

      $(this).hide();
    }
  }

}

/* harmony default export */ __webpack_exports__["default"] = (Checkout);

/***/ }),

/***/ "./src/modules/Woocommerce/Checkout/Windcave.js":
/*!******************************************************!*\
  !*** ./src/modules/Woocommerce/Checkout/Windcave.js ***!
  \******************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var axios__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! axios */ "./node_modules/axios/index.js");
/* harmony import */ var axios__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(axios__WEBPACK_IMPORTED_MODULE_0__);

const $ = jQuery;
window.sessionID = 10;

class Windcave {
  constructor() {
    this.events();
  }

  events() {
    this.createSession();
    $('.windcave-submit-button').on('click', this.validateWindcave);
  }

  createSession() {
    // customer data 
    let firstName = $('.woocommerce-checkout #billing_first_name');
    let lastName = $('.woocommerce-checkout #billing_last_name');
    let phone = $('.woocommerce-checkout #billing_phone');
    let emailAddress = $('.woocommerce-checkout #billing_email');
    let cartTotal = $('.payment-gateway-container').attr('data-carttotal');
    $.ajax({
      beforeSend: xhr => {
        $('.payment-gateway-container .foreground-loader').show();
        xhr.setRequestHeader('X-WP-NONCE', inspiryData.nonce);
      },
      url: inspiryData.root_url + '/wp-json/inspiry/v1/windcave-session',
      type: 'POST',
      data: {
        cartTotal: cartTotal,
        firstName: firstName.val(),
        lastName: lastName.val(),
        emailAddress: emailAddress.val(),
        phone: phone.val()
      },
      complete: () => {
        $('.payment-gateway-container .foreground-loader').hide();
      },
      success: response => {
        if (response.state === 'init') {
          window.sessionID = response.id;
          console.log(`session id ${window.sessionID}`);
          let sessionLink = response.links.filter(item => item.rel === 'seamless_hpp'); // load windcave iframe 

          WindcavePayments.Seamless.prepareIframe({
            url: sessionLink[0].href,
            containerId: "payment-iframe-container",
            loadTimeout: 30,
            width: 400,
            height: 500,
            onProcessed: function () {
              console.log('iframes is loaded properly ');
            },
            onError: function (error) {
              console.log(error);
              console.log('this is an error event after loading ');
            }
          });
        }
      },
      error: response => {
        console.log('something went wrong.');
        console.log(response);
        $('.payment-gateway-container .foreground-loader').hide();
      }
    });
  }

  validateWindcave(e) {
    // validate windcave credit card form 
    WindcavePayments.Seamless.validate({
      onProcessed: function (isValid) {
        if (isValid) {
          $('.payment-gateway-container .foreground-loader').show(); // if the credit card is valid, submit the form 

          WindcavePayments.Seamless.submit({
            onProcessed: function (response) {
              console.log(response);
              console.log('wincave submitted');
              $('.payment-gateway-container .foreground-loader').hide();
              getTransactionStatus(window.sessionID);
            },
            onError: function (error) {
              console.log(error);
            }
          });
        }
      },
      onError: function (error) {
        console.log(error);
        $('.payment-gateway-container .foreground-loader').hide();
      }
    });

    const getTransactionStatus = sessionID => {
      $.ajax({
        beforeSend: xhr => {
          $('.payment-gateway-container .foreground-loader').show();
          xhr.setRequestHeader('X-WP-NONCE', inspiryData.nonce);
        },
        url: inspiryData.root_url + '/wp-json/inspiry/v1/windcave-session-status',
        type: 'POST',
        data: {
          sessionID: sessionID
        },
        complete: () => {
          $('.payment-gateway-container .foreground-loader').hide();
          console.log('request completed');
        },
        success: response => {
          if (response.transactions[0].authorised) {
            console.log('transaction successful');
            $(".woocommerce-checkout").trigger("submit");
            $('#payment-iframe-container .button-container').append(`<p class="success center-align">Successful</p>`);
            WindcavePayments.Seamless.cleanup();
          } else {
            console.log(response);
            $('.error-modal').show();
            $('.error-modal .content').text(response.transactions[0].responseText);
            $('.error-modal button').text("Try Again");
            $('.payment-gateway-container').hide();
            $('.overlay').hide();
          }
        },
        error: response => {
          console.log('this is a board error');
          console.log(response);
        }
      });
    };
  }

}

/* harmony default export */ __webpack_exports__["default"] = (Windcave);

/***/ }),

/***/ "./src/modules/Woocommerce/ProductArchive.js":
/*!***************************************************!*\
  !*** ./src/modules/Woocommerce/ProductArchive.js ***!
  \***************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
const $ = jQuery;

class ProductArchive {
  constructor() {
    this.events();
  }

  events() {
    // prevent default behaviour 
    $('.wvs-archive-variation-wrapper').on('click', e => {
      e.preventDefault();
    });
  }

}

/* harmony default export */ __webpack_exports__["default"] = (ProductArchive);

/***/ }),

/***/ "./src/modules/Woocommerce/SingleProduct.js":
/*!**************************************************!*\
  !*** ./src/modules/Woocommerce/SingleProduct.js ***!
  \**************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
const $ = jQuery;

class SingleProduct {
  constructor() {
    this.variationProduct = $('.single-product .variations_form .variation_id');
    this.events();
  }

  events() {
    this.variationProduct.on('change', this.getVariationValue);
  }

  getVariationValue(e) {
    const variationID = $(this).val();
    const variationData = JSON.parse($('.single-product .variations_form .variation-availability-data').attr('data-variation_availability'));

    if (variationID > 0) {
      console.log(variationData);
      const selectedVariation = variationData.filter(item => item.variation_id === Number(variationID));
      console.log(selectedVariation[0].availability);

      if (selectedVariation[0].availability === "in-stock") {
        $('.single-product .availability .title span').text('In Stock');
        $('.single-product .availability .title span').css({
          'color': '#1fac75'
        });
        $('.single-product .availability .title .fa-circle-check').css({
          'color': '#1fac75'
        });
      } else {
        $('.single-product .availability .title span').text('Pre Order');
        $('.single-product .availability .title span').css({
          'color': '#d69400'
        });
        $('.single-product .availability .title .fa-circle-check').css({
          'color': '#d69400'
        });
      }
    } else {
      console.log('id is zero ');
    }
  }

}

/* harmony default export */ __webpack_exports__["default"] = (SingleProduct);

/***/ }),

/***/ "./src/modules/Woocommerce/WooGallery.js":
/*!***********************************************!*\
  !*** ./src/modules/Woocommerce/WooGallery.js ***!
  \***********************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var owl_carousel_dist_assets_owl_carousel_css__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! owl.carousel/dist/assets/owl.carousel.css */ "./node_modules/owl.carousel/dist/assets/owl.carousel.css");
/* harmony import */ var owl_carousel__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! owl.carousel */ "./node_modules/owl.carousel/dist/owl.carousel.js");
/* harmony import */ var owl_carousel__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(owl_carousel__WEBPACK_IMPORTED_MODULE_1__);
const $ = jQuery;



class WooGallery {
  constructor() {
    this.events();
  }

  events() {
    // owl  carousel for single product page
    this.slideShow();
  }

  slideShow() {
    var x = window.matchMedia("(max-width: 800px)");

    if (x.matches) {
      $('.woocommerce-product-gallery__wrapper').addClass('owl-carousel');
      $('.owl-carousel').owlCarousel({
        loop: true,
        margin: 10,
        nav: true,
        responsive: {
          0: {
            items: 1
          },
          600: {
            items: 1
          },
          1000: {
            items: 1
          }
        }
      });
    }
  }

}

/* harmony default export */ __webpack_exports__["default"] = (WooGallery);

/***/ }),

/***/ "./src/modules/Woocommerce/singleProductAccordion.js":
/*!***********************************************************!*\
  !*** ./src/modules/Woocommerce/singleProductAccordion.js ***!
  \***********************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
const $ = jQuery;

class SingleProductAccordion {
  constructor() {
    this.firstItem = $('.single-product .accordion-container .item')[0];
    this.events();
  }

  events() {
    $('.single-product .accordion-container .item .title').on('click', this.toggleAccordion);
    $('.single-product .accordion-container .item .title span').on('click', this.toggleIcon);
    this.showFirstItem();
  }

  toggleAccordion(e) {
    // console.log($(e.target).closest('.title').siblings('.content'))
    $(e.target).closest('.title').siblings('.content').slideToggle();
    let currentIcon = $(e.target).find('span').html();
    $(e.target).find('span').html(currentIcon === "+" ? "–" : "+");
  }

  toggleIcon(e) {
    console.log('icon function');
    let currentIcon = $(e.target).html();
    $(e.target).html(currentIcon === "+" ? "–" : "+");
  }

  showFirstItem() {
    $(this.firstItem).find('.content').show();
    $(this.firstItem).find('span').html('–');
  }

}

/* harmony default export */ __webpack_exports__["default"] = (SingleProductAccordion);

/***/ }),

/***/ "./src/modules/overlay.js":
/*!********************************!*\
  !*** ./src/modules/overlay.js ***!
  \********************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
let $ = jQuery;

class Overlay {
  constructor() {
    this.events();
  }

  events() {
    $('.featured-project-section .flex .card').hover(e => {
      console.log('hover');
      console.log(e.target);
      $(e.target).css('opacity', '60%');
      $(e.target).siblings('.featured-project-section .flex .column-font-size').show(300);
    }, e => {
      $(e.target).css('opacity', '0');
      $(e.target).siblings('.featured-project-section .flex .column-font-size').hide(300);
    });
  }

}

/* harmony default export */ __webpack_exports__["default"] = (Overlay);

/***/ }),

/***/ "./node_modules/owl.carousel/dist/assets/owl.carousel.css":
/*!****************************************************************!*\
  !*** ./node_modules/owl.carousel/dist/assets/owl.carousel.css ***!
  \****************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
// extracted by mini-css-extract-plugin


/***/ }),

/***/ "./css/style.scss":
/*!************************!*\
  !*** ./css/style.scss ***!
  \************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
// extracted by mini-css-extract-plugin


/***/ }),

/***/ "./node_modules/owl.carousel/dist/owl.carousel.js":
/*!********************************************************!*\
  !*** ./node_modules/owl.carousel/dist/owl.carousel.js ***!
  \********************************************************/
/***/ (function() {

/**
 * Owl Carousel v2.3.4
 * Copyright 2013-2018 David Deutsch
 * Licensed under: SEE LICENSE IN https://github.com/OwlCarousel2/OwlCarousel2/blob/master/LICENSE
 */
/**
 * Owl carousel
 * @version 2.3.4
 * @author Bartosz Wojciechowski
 * @author David Deutsch
 * @license The MIT License (MIT)
 * @todo Lazy Load Icon
 * @todo prevent animationend bubling
 * @todo itemsScaleUp
 * @todo Test Zepto
 * @todo stagePadding calculate wrong active classes
 */
;(function($, window, document, undefined) {

	/**
	 * Creates a carousel.
	 * @class The Owl Carousel.
	 * @public
	 * @param {HTMLElement|jQuery} element - The element to create the carousel for.
	 * @param {Object} [options] - The options
	 */
	function Owl(element, options) {

		/**
		 * Current settings for the carousel.
		 * @public
		 */
		this.settings = null;

		/**
		 * Current options set by the caller including defaults.
		 * @public
		 */
		this.options = $.extend({}, Owl.Defaults, options);

		/**
		 * Plugin element.
		 * @public
		 */
		this.$element = $(element);

		/**
		 * Proxied event handlers.
		 * @protected
		 */
		this._handlers = {};

		/**
		 * References to the running plugins of this carousel.
		 * @protected
		 */
		this._plugins = {};

		/**
		 * Currently suppressed events to prevent them from being retriggered.
		 * @protected
		 */
		this._supress = {};

		/**
		 * Absolute current position.
		 * @protected
		 */
		this._current = null;

		/**
		 * Animation speed in milliseconds.
		 * @protected
		 */
		this._speed = null;

		/**
		 * Coordinates of all items in pixel.
		 * @todo The name of this member is missleading.
		 * @protected
		 */
		this._coordinates = [];

		/**
		 * Current breakpoint.
		 * @todo Real media queries would be nice.
		 * @protected
		 */
		this._breakpoint = null;

		/**
		 * Current width of the plugin element.
		 */
		this._width = null;

		/**
		 * All real items.
		 * @protected
		 */
		this._items = [];

		/**
		 * All cloned items.
		 * @protected
		 */
		this._clones = [];

		/**
		 * Merge values of all items.
		 * @todo Maybe this could be part of a plugin.
		 * @protected
		 */
		this._mergers = [];

		/**
		 * Widths of all items.
		 */
		this._widths = [];

		/**
		 * Invalidated parts within the update process.
		 * @protected
		 */
		this._invalidated = {};

		/**
		 * Ordered list of workers for the update process.
		 * @protected
		 */
		this._pipe = [];

		/**
		 * Current state information for the drag operation.
		 * @todo #261
		 * @protected
		 */
		this._drag = {
			time: null,
			target: null,
			pointer: null,
			stage: {
				start: null,
				current: null
			},
			direction: null
		};

		/**
		 * Current state information and their tags.
		 * @type {Object}
		 * @protected
		 */
		this._states = {
			current: {},
			tags: {
				'initializing': [ 'busy' ],
				'animating': [ 'busy' ],
				'dragging': [ 'interacting' ]
			}
		};

		$.each([ 'onResize', 'onThrottledResize' ], $.proxy(function(i, handler) {
			this._handlers[handler] = $.proxy(this[handler], this);
		}, this));

		$.each(Owl.Plugins, $.proxy(function(key, plugin) {
			this._plugins[key.charAt(0).toLowerCase() + key.slice(1)]
				= new plugin(this);
		}, this));

		$.each(Owl.Workers, $.proxy(function(priority, worker) {
			this._pipe.push({
				'filter': worker.filter,
				'run': $.proxy(worker.run, this)
			});
		}, this));

		this.setup();
		this.initialize();
	}

	/**
	 * Default options for the carousel.
	 * @public
	 */
	Owl.Defaults = {
		items: 3,
		loop: false,
		center: false,
		rewind: false,
		checkVisibility: true,

		mouseDrag: true,
		touchDrag: true,
		pullDrag: true,
		freeDrag: false,

		margin: 0,
		stagePadding: 0,

		merge: false,
		mergeFit: true,
		autoWidth: false,

		startPosition: 0,
		rtl: false,

		smartSpeed: 250,
		fluidSpeed: false,
		dragEndSpeed: false,

		responsive: {},
		responsiveRefreshRate: 200,
		responsiveBaseElement: window,

		fallbackEasing: 'swing',
		slideTransition: '',

		info: false,

		nestedItemSelector: false,
		itemElement: 'div',
		stageElement: 'div',

		refreshClass: 'owl-refresh',
		loadedClass: 'owl-loaded',
		loadingClass: 'owl-loading',
		rtlClass: 'owl-rtl',
		responsiveClass: 'owl-responsive',
		dragClass: 'owl-drag',
		itemClass: 'owl-item',
		stageClass: 'owl-stage',
		stageOuterClass: 'owl-stage-outer',
		grabClass: 'owl-grab'
	};

	/**
	 * Enumeration for width.
	 * @public
	 * @readonly
	 * @enum {String}
	 */
	Owl.Width = {
		Default: 'default',
		Inner: 'inner',
		Outer: 'outer'
	};

	/**
	 * Enumeration for types.
	 * @public
	 * @readonly
	 * @enum {String}
	 */
	Owl.Type = {
		Event: 'event',
		State: 'state'
	};

	/**
	 * Contains all registered plugins.
	 * @public
	 */
	Owl.Plugins = {};

	/**
	 * List of workers involved in the update process.
	 */
	Owl.Workers = [ {
		filter: [ 'width', 'settings' ],
		run: function() {
			this._width = this.$element.width();
		}
	}, {
		filter: [ 'width', 'items', 'settings' ],
		run: function(cache) {
			cache.current = this._items && this._items[this.relative(this._current)];
		}
	}, {
		filter: [ 'items', 'settings' ],
		run: function() {
			this.$stage.children('.cloned').remove();
		}
	}, {
		filter: [ 'width', 'items', 'settings' ],
		run: function(cache) {
			var margin = this.settings.margin || '',
				grid = !this.settings.autoWidth,
				rtl = this.settings.rtl,
				css = {
					'width': 'auto',
					'margin-left': rtl ? margin : '',
					'margin-right': rtl ? '' : margin
				};

			!grid && this.$stage.children().css(css);

			cache.css = css;
		}
	}, {
		filter: [ 'width', 'items', 'settings' ],
		run: function(cache) {
			var width = (this.width() / this.settings.items).toFixed(3) - this.settings.margin,
				merge = null,
				iterator = this._items.length,
				grid = !this.settings.autoWidth,
				widths = [];

			cache.items = {
				merge: false,
				width: width
			};

			while (iterator--) {
				merge = this._mergers[iterator];
				merge = this.settings.mergeFit && Math.min(merge, this.settings.items) || merge;

				cache.items.merge = merge > 1 || cache.items.merge;

				widths[iterator] = !grid ? this._items[iterator].width() : width * merge;
			}

			this._widths = widths;
		}
	}, {
		filter: [ 'items', 'settings' ],
		run: function() {
			var clones = [],
				items = this._items,
				settings = this.settings,
				// TODO: Should be computed from number of min width items in stage
				view = Math.max(settings.items * 2, 4),
				size = Math.ceil(items.length / 2) * 2,
				repeat = settings.loop && items.length ? settings.rewind ? view : Math.max(view, size) : 0,
				append = '',
				prepend = '';

			repeat /= 2;

			while (repeat > 0) {
				// Switch to only using appended clones
				clones.push(this.normalize(clones.length / 2, true));
				append = append + items[clones[clones.length - 1]][0].outerHTML;
				clones.push(this.normalize(items.length - 1 - (clones.length - 1) / 2, true));
				prepend = items[clones[clones.length - 1]][0].outerHTML + prepend;
				repeat -= 1;
			}

			this._clones = clones;

			$(append).addClass('cloned').appendTo(this.$stage);
			$(prepend).addClass('cloned').prependTo(this.$stage);
		}
	}, {
		filter: [ 'width', 'items', 'settings' ],
		run: function() {
			var rtl = this.settings.rtl ? 1 : -1,
				size = this._clones.length + this._items.length,
				iterator = -1,
				previous = 0,
				current = 0,
				coordinates = [];

			while (++iterator < size) {
				previous = coordinates[iterator - 1] || 0;
				current = this._widths[this.relative(iterator)] + this.settings.margin;
				coordinates.push(previous + current * rtl);
			}

			this._coordinates = coordinates;
		}
	}, {
		filter: [ 'width', 'items', 'settings' ],
		run: function() {
			var padding = this.settings.stagePadding,
				coordinates = this._coordinates,
				css = {
					'width': Math.ceil(Math.abs(coordinates[coordinates.length - 1])) + padding * 2,
					'padding-left': padding || '',
					'padding-right': padding || ''
				};

			this.$stage.css(css);
		}
	}, {
		filter: [ 'width', 'items', 'settings' ],
		run: function(cache) {
			var iterator = this._coordinates.length,
				grid = !this.settings.autoWidth,
				items = this.$stage.children();

			if (grid && cache.items.merge) {
				while (iterator--) {
					cache.css.width = this._widths[this.relative(iterator)];
					items.eq(iterator).css(cache.css);
				}
			} else if (grid) {
				cache.css.width = cache.items.width;
				items.css(cache.css);
			}
		}
	}, {
		filter: [ 'items' ],
		run: function() {
			this._coordinates.length < 1 && this.$stage.removeAttr('style');
		}
	}, {
		filter: [ 'width', 'items', 'settings' ],
		run: function(cache) {
			cache.current = cache.current ? this.$stage.children().index(cache.current) : 0;
			cache.current = Math.max(this.minimum(), Math.min(this.maximum(), cache.current));
			this.reset(cache.current);
		}
	}, {
		filter: [ 'position' ],
		run: function() {
			this.animate(this.coordinates(this._current));
		}
	}, {
		filter: [ 'width', 'position', 'items', 'settings' ],
		run: function() {
			var rtl = this.settings.rtl ? 1 : -1,
				padding = this.settings.stagePadding * 2,
				begin = this.coordinates(this.current()) + padding,
				end = begin + this.width() * rtl,
				inner, outer, matches = [], i, n;

			for (i = 0, n = this._coordinates.length; i < n; i++) {
				inner = this._coordinates[i - 1] || 0;
				outer = Math.abs(this._coordinates[i]) + padding * rtl;

				if ((this.op(inner, '<=', begin) && (this.op(inner, '>', end)))
					|| (this.op(outer, '<', begin) && this.op(outer, '>', end))) {
					matches.push(i);
				}
			}

			this.$stage.children('.active').removeClass('active');
			this.$stage.children(':eq(' + matches.join('), :eq(') + ')').addClass('active');

			this.$stage.children('.center').removeClass('center');
			if (this.settings.center) {
				this.$stage.children().eq(this.current()).addClass('center');
			}
		}
	} ];

	/**
	 * Create the stage DOM element
	 */
	Owl.prototype.initializeStage = function() {
		this.$stage = this.$element.find('.' + this.settings.stageClass);

		// if the stage is already in the DOM, grab it and skip stage initialization
		if (this.$stage.length) {
			return;
		}

		this.$element.addClass(this.options.loadingClass);

		// create stage
		this.$stage = $('<' + this.settings.stageElement + '>', {
			"class": this.settings.stageClass
		}).wrap( $( '<div/>', {
			"class": this.settings.stageOuterClass
		}));

		// append stage
		this.$element.append(this.$stage.parent());
	};

	/**
	 * Create item DOM elements
	 */
	Owl.prototype.initializeItems = function() {
		var $items = this.$element.find('.owl-item');

		// if the items are already in the DOM, grab them and skip item initialization
		if ($items.length) {
			this._items = $items.get().map(function(item) {
				return $(item);
			});

			this._mergers = this._items.map(function() {
				return 1;
			});

			this.refresh();

			return;
		}

		// append content
		this.replace(this.$element.children().not(this.$stage.parent()));

		// check visibility
		if (this.isVisible()) {
			// update view
			this.refresh();
		} else {
			// invalidate width
			this.invalidate('width');
		}

		this.$element
			.removeClass(this.options.loadingClass)
			.addClass(this.options.loadedClass);
	};

	/**
	 * Initializes the carousel.
	 * @protected
	 */
	Owl.prototype.initialize = function() {
		this.enter('initializing');
		this.trigger('initialize');

		this.$element.toggleClass(this.settings.rtlClass, this.settings.rtl);

		if (this.settings.autoWidth && !this.is('pre-loading')) {
			var imgs, nestedSelector, width;
			imgs = this.$element.find('img');
			nestedSelector = this.settings.nestedItemSelector ? '.' + this.settings.nestedItemSelector : undefined;
			width = this.$element.children(nestedSelector).width();

			if (imgs.length && width <= 0) {
				this.preloadAutoWidthImages(imgs);
			}
		}

		this.initializeStage();
		this.initializeItems();

		// register event handlers
		this.registerEventHandlers();

		this.leave('initializing');
		this.trigger('initialized');
	};

	/**
	 * @returns {Boolean} visibility of $element
	 *                    if you know the carousel will always be visible you can set `checkVisibility` to `false` to
	 *                    prevent the expensive browser layout forced reflow the $element.is(':visible') does
	 */
	Owl.prototype.isVisible = function() {
		return this.settings.checkVisibility
			? this.$element.is(':visible')
			: true;
	};

	/**
	 * Setups the current settings.
	 * @todo Remove responsive classes. Why should adaptive designs be brought into IE8?
	 * @todo Support for media queries by using `matchMedia` would be nice.
	 * @public
	 */
	Owl.prototype.setup = function() {
		var viewport = this.viewport(),
			overwrites = this.options.responsive,
			match = -1,
			settings = null;

		if (!overwrites) {
			settings = $.extend({}, this.options);
		} else {
			$.each(overwrites, function(breakpoint) {
				if (breakpoint <= viewport && breakpoint > match) {
					match = Number(breakpoint);
				}
			});

			settings = $.extend({}, this.options, overwrites[match]);
			if (typeof settings.stagePadding === 'function') {
				settings.stagePadding = settings.stagePadding();
			}
			delete settings.responsive;

			// responsive class
			if (settings.responsiveClass) {
				this.$element.attr('class',
					this.$element.attr('class').replace(new RegExp('(' + this.options.responsiveClass + '-)\\S+\\s', 'g'), '$1' + match)
				);
			}
		}

		this.trigger('change', { property: { name: 'settings', value: settings } });
		this._breakpoint = match;
		this.settings = settings;
		this.invalidate('settings');
		this.trigger('changed', { property: { name: 'settings', value: this.settings } });
	};

	/**
	 * Updates option logic if necessery.
	 * @protected
	 */
	Owl.prototype.optionsLogic = function() {
		if (this.settings.autoWidth) {
			this.settings.stagePadding = false;
			this.settings.merge = false;
		}
	};

	/**
	 * Prepares an item before add.
	 * @todo Rename event parameter `content` to `item`.
	 * @protected
	 * @returns {jQuery|HTMLElement} - The item container.
	 */
	Owl.prototype.prepare = function(item) {
		var event = this.trigger('prepare', { content: item });

		if (!event.data) {
			event.data = $('<' + this.settings.itemElement + '/>')
				.addClass(this.options.itemClass).append(item)
		}

		this.trigger('prepared', { content: event.data });

		return event.data;
	};

	/**
	 * Updates the view.
	 * @public
	 */
	Owl.prototype.update = function() {
		var i = 0,
			n = this._pipe.length,
			filter = $.proxy(function(p) { return this[p] }, this._invalidated),
			cache = {};

		while (i < n) {
			if (this._invalidated.all || $.grep(this._pipe[i].filter, filter).length > 0) {
				this._pipe[i].run(cache);
			}
			i++;
		}

		this._invalidated = {};

		!this.is('valid') && this.enter('valid');
	};

	/**
	 * Gets the width of the view.
	 * @public
	 * @param {Owl.Width} [dimension=Owl.Width.Default] - The dimension to return.
	 * @returns {Number} - The width of the view in pixel.
	 */
	Owl.prototype.width = function(dimension) {
		dimension = dimension || Owl.Width.Default;
		switch (dimension) {
			case Owl.Width.Inner:
			case Owl.Width.Outer:
				return this._width;
			default:
				return this._width - this.settings.stagePadding * 2 + this.settings.margin;
		}
	};

	/**
	 * Refreshes the carousel primarily for adaptive purposes.
	 * @public
	 */
	Owl.prototype.refresh = function() {
		this.enter('refreshing');
		this.trigger('refresh');

		this.setup();

		this.optionsLogic();

		this.$element.addClass(this.options.refreshClass);

		this.update();

		this.$element.removeClass(this.options.refreshClass);

		this.leave('refreshing');
		this.trigger('refreshed');
	};

	/**
	 * Checks window `resize` event.
	 * @protected
	 */
	Owl.prototype.onThrottledResize = function() {
		window.clearTimeout(this.resizeTimer);
		this.resizeTimer = window.setTimeout(this._handlers.onResize, this.settings.responsiveRefreshRate);
	};

	/**
	 * Checks window `resize` event.
	 * @protected
	 */
	Owl.prototype.onResize = function() {
		if (!this._items.length) {
			return false;
		}

		if (this._width === this.$element.width()) {
			return false;
		}

		if (!this.isVisible()) {
			return false;
		}

		this.enter('resizing');

		if (this.trigger('resize').isDefaultPrevented()) {
			this.leave('resizing');
			return false;
		}

		this.invalidate('width');

		this.refresh();

		this.leave('resizing');
		this.trigger('resized');
	};

	/**
	 * Registers event handlers.
	 * @todo Check `msPointerEnabled`
	 * @todo #261
	 * @protected
	 */
	Owl.prototype.registerEventHandlers = function() {
		if ($.support.transition) {
			this.$stage.on($.support.transition.end + '.owl.core', $.proxy(this.onTransitionEnd, this));
		}

		if (this.settings.responsive !== false) {
			this.on(window, 'resize', this._handlers.onThrottledResize);
		}

		if (this.settings.mouseDrag) {
			this.$element.addClass(this.options.dragClass);
			this.$stage.on('mousedown.owl.core', $.proxy(this.onDragStart, this));
			this.$stage.on('dragstart.owl.core selectstart.owl.core', function() { return false });
		}

		if (this.settings.touchDrag){
			this.$stage.on('touchstart.owl.core', $.proxy(this.onDragStart, this));
			this.$stage.on('touchcancel.owl.core', $.proxy(this.onDragEnd, this));
		}
	};

	/**
	 * Handles `touchstart` and `mousedown` events.
	 * @todo Horizontal swipe threshold as option
	 * @todo #261
	 * @protected
	 * @param {Event} event - The event arguments.
	 */
	Owl.prototype.onDragStart = function(event) {
		var stage = null;

		if (event.which === 3) {
			return;
		}

		if ($.support.transform) {
			stage = this.$stage.css('transform').replace(/.*\(|\)| /g, '').split(',');
			stage = {
				x: stage[stage.length === 16 ? 12 : 4],
				y: stage[stage.length === 16 ? 13 : 5]
			};
		} else {
			stage = this.$stage.position();
			stage = {
				x: this.settings.rtl ?
					stage.left + this.$stage.width() - this.width() + this.settings.margin :
					stage.left,
				y: stage.top
			};
		}

		if (this.is('animating')) {
			$.support.transform ? this.animate(stage.x) : this.$stage.stop()
			this.invalidate('position');
		}

		this.$element.toggleClass(this.options.grabClass, event.type === 'mousedown');

		this.speed(0);

		this._drag.time = new Date().getTime();
		this._drag.target = $(event.target);
		this._drag.stage.start = stage;
		this._drag.stage.current = stage;
		this._drag.pointer = this.pointer(event);

		$(document).on('mouseup.owl.core touchend.owl.core', $.proxy(this.onDragEnd, this));

		$(document).one('mousemove.owl.core touchmove.owl.core', $.proxy(function(event) {
			var delta = this.difference(this._drag.pointer, this.pointer(event));

			$(document).on('mousemove.owl.core touchmove.owl.core', $.proxy(this.onDragMove, this));

			if (Math.abs(delta.x) < Math.abs(delta.y) && this.is('valid')) {
				return;
			}

			event.preventDefault();

			this.enter('dragging');
			this.trigger('drag');
		}, this));
	};

	/**
	 * Handles the `touchmove` and `mousemove` events.
	 * @todo #261
	 * @protected
	 * @param {Event} event - The event arguments.
	 */
	Owl.prototype.onDragMove = function(event) {
		var minimum = null,
			maximum = null,
			pull = null,
			delta = this.difference(this._drag.pointer, this.pointer(event)),
			stage = this.difference(this._drag.stage.start, delta);

		if (!this.is('dragging')) {
			return;
		}

		event.preventDefault();

		if (this.settings.loop) {
			minimum = this.coordinates(this.minimum());
			maximum = this.coordinates(this.maximum() + 1) - minimum;
			stage.x = (((stage.x - minimum) % maximum + maximum) % maximum) + minimum;
		} else {
			minimum = this.settings.rtl ? this.coordinates(this.maximum()) : this.coordinates(this.minimum());
			maximum = this.settings.rtl ? this.coordinates(this.minimum()) : this.coordinates(this.maximum());
			pull = this.settings.pullDrag ? -1 * delta.x / 5 : 0;
			stage.x = Math.max(Math.min(stage.x, minimum + pull), maximum + pull);
		}

		this._drag.stage.current = stage;

		this.animate(stage.x);
	};

	/**
	 * Handles the `touchend` and `mouseup` events.
	 * @todo #261
	 * @todo Threshold for click event
	 * @protected
	 * @param {Event} event - The event arguments.
	 */
	Owl.prototype.onDragEnd = function(event) {
		var delta = this.difference(this._drag.pointer, this.pointer(event)),
			stage = this._drag.stage.current,
			direction = delta.x > 0 ^ this.settings.rtl ? 'left' : 'right';

		$(document).off('.owl.core');

		this.$element.removeClass(this.options.grabClass);

		if (delta.x !== 0 && this.is('dragging') || !this.is('valid')) {
			this.speed(this.settings.dragEndSpeed || this.settings.smartSpeed);
			this.current(this.closest(stage.x, delta.x !== 0 ? direction : this._drag.direction));
			this.invalidate('position');
			this.update();

			this._drag.direction = direction;

			if (Math.abs(delta.x) > 3 || new Date().getTime() - this._drag.time > 300) {
				this._drag.target.one('click.owl.core', function() { return false; });
			}
		}

		if (!this.is('dragging')) {
			return;
		}

		this.leave('dragging');
		this.trigger('dragged');
	};

	/**
	 * Gets absolute position of the closest item for a coordinate.
	 * @todo Setting `freeDrag` makes `closest` not reusable. See #165.
	 * @protected
	 * @param {Number} coordinate - The coordinate in pixel.
	 * @param {String} direction - The direction to check for the closest item. Ether `left` or `right`.
	 * @return {Number} - The absolute position of the closest item.
	 */
	Owl.prototype.closest = function(coordinate, direction) {
		var position = -1,
			pull = 30,
			width = this.width(),
			coordinates = this.coordinates();

		if (!this.settings.freeDrag) {
			// check closest item
			$.each(coordinates, $.proxy(function(index, value) {
				// on a left pull, check on current index
				if (direction === 'left' && coordinate > value - pull && coordinate < value + pull) {
					position = index;
				// on a right pull, check on previous index
				// to do so, subtract width from value and set position = index + 1
				} else if (direction === 'right' && coordinate > value - width - pull && coordinate < value - width + pull) {
					position = index + 1;
				} else if (this.op(coordinate, '<', value)
					&& this.op(coordinate, '>', coordinates[index + 1] !== undefined ? coordinates[index + 1] : value - width)) {
					position = direction === 'left' ? index + 1 : index;
				}
				return position === -1;
			}, this));
		}

		if (!this.settings.loop) {
			// non loop boundries
			if (this.op(coordinate, '>', coordinates[this.minimum()])) {
				position = coordinate = this.minimum();
			} else if (this.op(coordinate, '<', coordinates[this.maximum()])) {
				position = coordinate = this.maximum();
			}
		}

		return position;
	};

	/**
	 * Animates the stage.
	 * @todo #270
	 * @public
	 * @param {Number} coordinate - The coordinate in pixels.
	 */
	Owl.prototype.animate = function(coordinate) {
		var animate = this.speed() > 0;

		this.is('animating') && this.onTransitionEnd();

		if (animate) {
			this.enter('animating');
			this.trigger('translate');
		}

		if ($.support.transform3d && $.support.transition) {
			this.$stage.css({
				transform: 'translate3d(' + coordinate + 'px,0px,0px)',
				transition: (this.speed() / 1000) + 's' + (
					this.settings.slideTransition ? ' ' + this.settings.slideTransition : ''
				)
			});
		} else if (animate) {
			this.$stage.animate({
				left: coordinate + 'px'
			}, this.speed(), this.settings.fallbackEasing, $.proxy(this.onTransitionEnd, this));
		} else {
			this.$stage.css({
				left: coordinate + 'px'
			});
		}
	};

	/**
	 * Checks whether the carousel is in a specific state or not.
	 * @param {String} state - The state to check.
	 * @returns {Boolean} - The flag which indicates if the carousel is busy.
	 */
	Owl.prototype.is = function(state) {
		return this._states.current[state] && this._states.current[state] > 0;
	};

	/**
	 * Sets the absolute position of the current item.
	 * @public
	 * @param {Number} [position] - The new absolute position or nothing to leave it unchanged.
	 * @returns {Number} - The absolute position of the current item.
	 */
	Owl.prototype.current = function(position) {
		if (position === undefined) {
			return this._current;
		}

		if (this._items.length === 0) {
			return undefined;
		}

		position = this.normalize(position);

		if (this._current !== position) {
			var event = this.trigger('change', { property: { name: 'position', value: position } });

			if (event.data !== undefined) {
				position = this.normalize(event.data);
			}

			this._current = position;

			this.invalidate('position');

			this.trigger('changed', { property: { name: 'position', value: this._current } });
		}

		return this._current;
	};

	/**
	 * Invalidates the given part of the update routine.
	 * @param {String} [part] - The part to invalidate.
	 * @returns {Array.<String>} - The invalidated parts.
	 */
	Owl.prototype.invalidate = function(part) {
		if ($.type(part) === 'string') {
			this._invalidated[part] = true;
			this.is('valid') && this.leave('valid');
		}
		return $.map(this._invalidated, function(v, i) { return i });
	};

	/**
	 * Resets the absolute position of the current item.
	 * @public
	 * @param {Number} position - The absolute position of the new item.
	 */
	Owl.prototype.reset = function(position) {
		position = this.normalize(position);

		if (position === undefined) {
			return;
		}

		this._speed = 0;
		this._current = position;

		this.suppress([ 'translate', 'translated' ]);

		this.animate(this.coordinates(position));

		this.release([ 'translate', 'translated' ]);
	};

	/**
	 * Normalizes an absolute or a relative position of an item.
	 * @public
	 * @param {Number} position - The absolute or relative position to normalize.
	 * @param {Boolean} [relative=false] - Whether the given position is relative or not.
	 * @returns {Number} - The normalized position.
	 */
	Owl.prototype.normalize = function(position, relative) {
		var n = this._items.length,
			m = relative ? 0 : this._clones.length;

		if (!this.isNumeric(position) || n < 1) {
			position = undefined;
		} else if (position < 0 || position >= n + m) {
			position = ((position - m / 2) % n + n) % n + m / 2;
		}

		return position;
	};

	/**
	 * Converts an absolute position of an item into a relative one.
	 * @public
	 * @param {Number} position - The absolute position to convert.
	 * @returns {Number} - The converted position.
	 */
	Owl.prototype.relative = function(position) {
		position -= this._clones.length / 2;
		return this.normalize(position, true);
	};

	/**
	 * Gets the maximum position for the current item.
	 * @public
	 * @param {Boolean} [relative=false] - Whether to return an absolute position or a relative position.
	 * @returns {Number}
	 */
	Owl.prototype.maximum = function(relative) {
		var settings = this.settings,
			maximum = this._coordinates.length,
			iterator,
			reciprocalItemsWidth,
			elementWidth;

		if (settings.loop) {
			maximum = this._clones.length / 2 + this._items.length - 1;
		} else if (settings.autoWidth || settings.merge) {
			iterator = this._items.length;
			if (iterator) {
				reciprocalItemsWidth = this._items[--iterator].width();
				elementWidth = this.$element.width();
				while (iterator--) {
					reciprocalItemsWidth += this._items[iterator].width() + this.settings.margin;
					if (reciprocalItemsWidth > elementWidth) {
						break;
					}
				}
			}
			maximum = iterator + 1;
		} else if (settings.center) {
			maximum = this._items.length - 1;
		} else {
			maximum = this._items.length - settings.items;
		}

		if (relative) {
			maximum -= this._clones.length / 2;
		}

		return Math.max(maximum, 0);
	};

	/**
	 * Gets the minimum position for the current item.
	 * @public
	 * @param {Boolean} [relative=false] - Whether to return an absolute position or a relative position.
	 * @returns {Number}
	 */
	Owl.prototype.minimum = function(relative) {
		return relative ? 0 : this._clones.length / 2;
	};

	/**
	 * Gets an item at the specified relative position.
	 * @public
	 * @param {Number} [position] - The relative position of the item.
	 * @return {jQuery|Array.<jQuery>} - The item at the given position or all items if no position was given.
	 */
	Owl.prototype.items = function(position) {
		if (position === undefined) {
			return this._items.slice();
		}

		position = this.normalize(position, true);
		return this._items[position];
	};

	/**
	 * Gets an item at the specified relative position.
	 * @public
	 * @param {Number} [position] - The relative position of the item.
	 * @return {jQuery|Array.<jQuery>} - The item at the given position or all items if no position was given.
	 */
	Owl.prototype.mergers = function(position) {
		if (position === undefined) {
			return this._mergers.slice();
		}

		position = this.normalize(position, true);
		return this._mergers[position];
	};

	/**
	 * Gets the absolute positions of clones for an item.
	 * @public
	 * @param {Number} [position] - The relative position of the item.
	 * @returns {Array.<Number>} - The absolute positions of clones for the item or all if no position was given.
	 */
	Owl.prototype.clones = function(position) {
		var odd = this._clones.length / 2,
			even = odd + this._items.length,
			map = function(index) { return index % 2 === 0 ? even + index / 2 : odd - (index + 1) / 2 };

		if (position === undefined) {
			return $.map(this._clones, function(v, i) { return map(i) });
		}

		return $.map(this._clones, function(v, i) { return v === position ? map(i) : null });
	};

	/**
	 * Sets the current animation speed.
	 * @public
	 * @param {Number} [speed] - The animation speed in milliseconds or nothing to leave it unchanged.
	 * @returns {Number} - The current animation speed in milliseconds.
	 */
	Owl.prototype.speed = function(speed) {
		if (speed !== undefined) {
			this._speed = speed;
		}

		return this._speed;
	};

	/**
	 * Gets the coordinate of an item.
	 * @todo The name of this method is missleanding.
	 * @public
	 * @param {Number} position - The absolute position of the item within `minimum()` and `maximum()`.
	 * @returns {Number|Array.<Number>} - The coordinate of the item in pixel or all coordinates.
	 */
	Owl.prototype.coordinates = function(position) {
		var multiplier = 1,
			newPosition = position - 1,
			coordinate;

		if (position === undefined) {
			return $.map(this._coordinates, $.proxy(function(coordinate, index) {
				return this.coordinates(index);
			}, this));
		}

		if (this.settings.center) {
			if (this.settings.rtl) {
				multiplier = -1;
				newPosition = position + 1;
			}

			coordinate = this._coordinates[position];
			coordinate += (this.width() - coordinate + (this._coordinates[newPosition] || 0)) / 2 * multiplier;
		} else {
			coordinate = this._coordinates[newPosition] || 0;
		}

		coordinate = Math.ceil(coordinate);

		return coordinate;
	};

	/**
	 * Calculates the speed for a translation.
	 * @protected
	 * @param {Number} from - The absolute position of the start item.
	 * @param {Number} to - The absolute position of the target item.
	 * @param {Number} [factor=undefined] - The time factor in milliseconds.
	 * @returns {Number} - The time in milliseconds for the translation.
	 */
	Owl.prototype.duration = function(from, to, factor) {
		if (factor === 0) {
			return 0;
		}

		return Math.min(Math.max(Math.abs(to - from), 1), 6) * Math.abs((factor || this.settings.smartSpeed));
	};

	/**
	 * Slides to the specified item.
	 * @public
	 * @param {Number} position - The position of the item.
	 * @param {Number} [speed] - The time in milliseconds for the transition.
	 */
	Owl.prototype.to = function(position, speed) {
		var current = this.current(),
			revert = null,
			distance = position - this.relative(current),
			direction = (distance > 0) - (distance < 0),
			items = this._items.length,
			minimum = this.minimum(),
			maximum = this.maximum();

		if (this.settings.loop) {
			if (!this.settings.rewind && Math.abs(distance) > items / 2) {
				distance += direction * -1 * items;
			}

			position = current + distance;
			revert = ((position - minimum) % items + items) % items + minimum;

			if (revert !== position && revert - distance <= maximum && revert - distance > 0) {
				current = revert - distance;
				position = revert;
				this.reset(current);
			}
		} else if (this.settings.rewind) {
			maximum += 1;
			position = (position % maximum + maximum) % maximum;
		} else {
			position = Math.max(minimum, Math.min(maximum, position));
		}

		this.speed(this.duration(current, position, speed));
		this.current(position);

		if (this.isVisible()) {
			this.update();
		}
	};

	/**
	 * Slides to the next item.
	 * @public
	 * @param {Number} [speed] - The time in milliseconds for the transition.
	 */
	Owl.prototype.next = function(speed) {
		speed = speed || false;
		this.to(this.relative(this.current()) + 1, speed);
	};

	/**
	 * Slides to the previous item.
	 * @public
	 * @param {Number} [speed] - The time in milliseconds for the transition.
	 */
	Owl.prototype.prev = function(speed) {
		speed = speed || false;
		this.to(this.relative(this.current()) - 1, speed);
	};

	/**
	 * Handles the end of an animation.
	 * @protected
	 * @param {Event} event - The event arguments.
	 */
	Owl.prototype.onTransitionEnd = function(event) {

		// if css2 animation then event object is undefined
		if (event !== undefined) {
			event.stopPropagation();

			// Catch only owl-stage transitionEnd event
			if ((event.target || event.srcElement || event.originalTarget) !== this.$stage.get(0)) {
				return false;
			}
		}

		this.leave('animating');
		this.trigger('translated');
	};

	/**
	 * Gets viewport width.
	 * @protected
	 * @return {Number} - The width in pixel.
	 */
	Owl.prototype.viewport = function() {
		var width;
		if (this.options.responsiveBaseElement !== window) {
			width = $(this.options.responsiveBaseElement).width();
		} else if (window.innerWidth) {
			width = window.innerWidth;
		} else if (document.documentElement && document.documentElement.clientWidth) {
			width = document.documentElement.clientWidth;
		} else {
			console.warn('Can not detect viewport width.');
		}
		return width;
	};

	/**
	 * Replaces the current content.
	 * @public
	 * @param {HTMLElement|jQuery|String} content - The new content.
	 */
	Owl.prototype.replace = function(content) {
		this.$stage.empty();
		this._items = [];

		if (content) {
			content = (content instanceof jQuery) ? content : $(content);
		}

		if (this.settings.nestedItemSelector) {
			content = content.find('.' + this.settings.nestedItemSelector);
		}

		content.filter(function() {
			return this.nodeType === 1;
		}).each($.proxy(function(index, item) {
			item = this.prepare(item);
			this.$stage.append(item);
			this._items.push(item);
			this._mergers.push(item.find('[data-merge]').addBack('[data-merge]').attr('data-merge') * 1 || 1);
		}, this));

		this.reset(this.isNumeric(this.settings.startPosition) ? this.settings.startPosition : 0);

		this.invalidate('items');
	};

	/**
	 * Adds an item.
	 * @todo Use `item` instead of `content` for the event arguments.
	 * @public
	 * @param {HTMLElement|jQuery|String} content - The item content to add.
	 * @param {Number} [position] - The relative position at which to insert the item otherwise the item will be added to the end.
	 */
	Owl.prototype.add = function(content, position) {
		var current = this.relative(this._current);

		position = position === undefined ? this._items.length : this.normalize(position, true);
		content = content instanceof jQuery ? content : $(content);

		this.trigger('add', { content: content, position: position });

		content = this.prepare(content);

		if (this._items.length === 0 || position === this._items.length) {
			this._items.length === 0 && this.$stage.append(content);
			this._items.length !== 0 && this._items[position - 1].after(content);
			this._items.push(content);
			this._mergers.push(content.find('[data-merge]').addBack('[data-merge]').attr('data-merge') * 1 || 1);
		} else {
			this._items[position].before(content);
			this._items.splice(position, 0, content);
			this._mergers.splice(position, 0, content.find('[data-merge]').addBack('[data-merge]').attr('data-merge') * 1 || 1);
		}

		this._items[current] && this.reset(this._items[current].index());

		this.invalidate('items');

		this.trigger('added', { content: content, position: position });
	};

	/**
	 * Removes an item by its position.
	 * @todo Use `item` instead of `content` for the event arguments.
	 * @public
	 * @param {Number} position - The relative position of the item to remove.
	 */
	Owl.prototype.remove = function(position) {
		position = this.normalize(position, true);

		if (position === undefined) {
			return;
		}

		this.trigger('remove', { content: this._items[position], position: position });

		this._items[position].remove();
		this._items.splice(position, 1);
		this._mergers.splice(position, 1);

		this.invalidate('items');

		this.trigger('removed', { content: null, position: position });
	};

	/**
	 * Preloads images with auto width.
	 * @todo Replace by a more generic approach
	 * @protected
	 */
	Owl.prototype.preloadAutoWidthImages = function(images) {
		images.each($.proxy(function(i, element) {
			this.enter('pre-loading');
			element = $(element);
			$(new Image()).one('load', $.proxy(function(e) {
				element.attr('src', e.target.src);
				element.css('opacity', 1);
				this.leave('pre-loading');
				!this.is('pre-loading') && !this.is('initializing') && this.refresh();
			}, this)).attr('src', element.attr('src') || element.attr('data-src') || element.attr('data-src-retina'));
		}, this));
	};

	/**
	 * Destroys the carousel.
	 * @public
	 */
	Owl.prototype.destroy = function() {

		this.$element.off('.owl.core');
		this.$stage.off('.owl.core');
		$(document).off('.owl.core');

		if (this.settings.responsive !== false) {
			window.clearTimeout(this.resizeTimer);
			this.off(window, 'resize', this._handlers.onThrottledResize);
		}

		for (var i in this._plugins) {
			this._plugins[i].destroy();
		}

		this.$stage.children('.cloned').remove();

		this.$stage.unwrap();
		this.$stage.children().contents().unwrap();
		this.$stage.children().unwrap();
		this.$stage.remove();
		this.$element
			.removeClass(this.options.refreshClass)
			.removeClass(this.options.loadingClass)
			.removeClass(this.options.loadedClass)
			.removeClass(this.options.rtlClass)
			.removeClass(this.options.dragClass)
			.removeClass(this.options.grabClass)
			.attr('class', this.$element.attr('class').replace(new RegExp(this.options.responsiveClass + '-\\S+\\s', 'g'), ''))
			.removeData('owl.carousel');
	};

	/**
	 * Operators to calculate right-to-left and left-to-right.
	 * @protected
	 * @param {Number} [a] - The left side operand.
	 * @param {String} [o] - The operator.
	 * @param {Number} [b] - The right side operand.
	 */
	Owl.prototype.op = function(a, o, b) {
		var rtl = this.settings.rtl;
		switch (o) {
			case '<':
				return rtl ? a > b : a < b;
			case '>':
				return rtl ? a < b : a > b;
			case '>=':
				return rtl ? a <= b : a >= b;
			case '<=':
				return rtl ? a >= b : a <= b;
			default:
				break;
		}
	};

	/**
	 * Attaches to an internal event.
	 * @protected
	 * @param {HTMLElement} element - The event source.
	 * @param {String} event - The event name.
	 * @param {Function} listener - The event handler to attach.
	 * @param {Boolean} capture - Wether the event should be handled at the capturing phase or not.
	 */
	Owl.prototype.on = function(element, event, listener, capture) {
		if (element.addEventListener) {
			element.addEventListener(event, listener, capture);
		} else if (element.attachEvent) {
			element.attachEvent('on' + event, listener);
		}
	};

	/**
	 * Detaches from an internal event.
	 * @protected
	 * @param {HTMLElement} element - The event source.
	 * @param {String} event - The event name.
	 * @param {Function} listener - The attached event handler to detach.
	 * @param {Boolean} capture - Wether the attached event handler was registered as a capturing listener or not.
	 */
	Owl.prototype.off = function(element, event, listener, capture) {
		if (element.removeEventListener) {
			element.removeEventListener(event, listener, capture);
		} else if (element.detachEvent) {
			element.detachEvent('on' + event, listener);
		}
	};

	/**
	 * Triggers a public event.
	 * @todo Remove `status`, `relatedTarget` should be used instead.
	 * @protected
	 * @param {String} name - The event name.
	 * @param {*} [data=null] - The event data.
	 * @param {String} [namespace=carousel] - The event namespace.
	 * @param {String} [state] - The state which is associated with the event.
	 * @param {Boolean} [enter=false] - Indicates if the call enters the specified state or not.
	 * @returns {Event} - The event arguments.
	 */
	Owl.prototype.trigger = function(name, data, namespace, state, enter) {
		var status = {
			item: { count: this._items.length, index: this.current() }
		}, handler = $.camelCase(
			$.grep([ 'on', name, namespace ], function(v) { return v })
				.join('-').toLowerCase()
		), event = $.Event(
			[ name, 'owl', namespace || 'carousel' ].join('.').toLowerCase(),
			$.extend({ relatedTarget: this }, status, data)
		);

		if (!this._supress[name]) {
			$.each(this._plugins, function(name, plugin) {
				if (plugin.onTrigger) {
					plugin.onTrigger(event);
				}
			});

			this.register({ type: Owl.Type.Event, name: name });
			this.$element.trigger(event);

			if (this.settings && typeof this.settings[handler] === 'function') {
				this.settings[handler].call(this, event);
			}
		}

		return event;
	};

	/**
	 * Enters a state.
	 * @param name - The state name.
	 */
	Owl.prototype.enter = function(name) {
		$.each([ name ].concat(this._states.tags[name] || []), $.proxy(function(i, name) {
			if (this._states.current[name] === undefined) {
				this._states.current[name] = 0;
			}

			this._states.current[name]++;
		}, this));
	};

	/**
	 * Leaves a state.
	 * @param name - The state name.
	 */
	Owl.prototype.leave = function(name) {
		$.each([ name ].concat(this._states.tags[name] || []), $.proxy(function(i, name) {
			this._states.current[name]--;
		}, this));
	};

	/**
	 * Registers an event or state.
	 * @public
	 * @param {Object} object - The event or state to register.
	 */
	Owl.prototype.register = function(object) {
		if (object.type === Owl.Type.Event) {
			if (!$.event.special[object.name]) {
				$.event.special[object.name] = {};
			}

			if (!$.event.special[object.name].owl) {
				var _default = $.event.special[object.name]._default;
				$.event.special[object.name]._default = function(e) {
					if (_default && _default.apply && (!e.namespace || e.namespace.indexOf('owl') === -1)) {
						return _default.apply(this, arguments);
					}
					return e.namespace && e.namespace.indexOf('owl') > -1;
				};
				$.event.special[object.name].owl = true;
			}
		} else if (object.type === Owl.Type.State) {
			if (!this._states.tags[object.name]) {
				this._states.tags[object.name] = object.tags;
			} else {
				this._states.tags[object.name] = this._states.tags[object.name].concat(object.tags);
			}

			this._states.tags[object.name] = $.grep(this._states.tags[object.name], $.proxy(function(tag, i) {
				return $.inArray(tag, this._states.tags[object.name]) === i;
			}, this));
		}
	};

	/**
	 * Suppresses events.
	 * @protected
	 * @param {Array.<String>} events - The events to suppress.
	 */
	Owl.prototype.suppress = function(events) {
		$.each(events, $.proxy(function(index, event) {
			this._supress[event] = true;
		}, this));
	};

	/**
	 * Releases suppressed events.
	 * @protected
	 * @param {Array.<String>} events - The events to release.
	 */
	Owl.prototype.release = function(events) {
		$.each(events, $.proxy(function(index, event) {
			delete this._supress[event];
		}, this));
	};

	/**
	 * Gets unified pointer coordinates from event.
	 * @todo #261
	 * @protected
	 * @param {Event} - The `mousedown` or `touchstart` event.
	 * @returns {Object} - Contains `x` and `y` coordinates of current pointer position.
	 */
	Owl.prototype.pointer = function(event) {
		var result = { x: null, y: null };

		event = event.originalEvent || event || window.event;

		event = event.touches && event.touches.length ?
			event.touches[0] : event.changedTouches && event.changedTouches.length ?
				event.changedTouches[0] : event;

		if (event.pageX) {
			result.x = event.pageX;
			result.y = event.pageY;
		} else {
			result.x = event.clientX;
			result.y = event.clientY;
		}

		return result;
	};

	/**
	 * Determines if the input is a Number or something that can be coerced to a Number
	 * @protected
	 * @param {Number|String|Object|Array|Boolean|RegExp|Function|Symbol} - The input to be tested
	 * @returns {Boolean} - An indication if the input is a Number or can be coerced to a Number
	 */
	Owl.prototype.isNumeric = function(number) {
		return !isNaN(parseFloat(number));
	};

	/**
	 * Gets the difference of two vectors.
	 * @todo #261
	 * @protected
	 * @param {Object} - The first vector.
	 * @param {Object} - The second vector.
	 * @returns {Object} - The difference.
	 */
	Owl.prototype.difference = function(first, second) {
		return {
			x: first.x - second.x,
			y: first.y - second.y
		};
	};

	/**
	 * The jQuery Plugin for the Owl Carousel
	 * @todo Navigation plugin `next` and `prev`
	 * @public
	 */
	$.fn.owlCarousel = function(option) {
		var args = Array.prototype.slice.call(arguments, 1);

		return this.each(function() {
			var $this = $(this),
				data = $this.data('owl.carousel');

			if (!data) {
				data = new Owl(this, typeof option == 'object' && option);
				$this.data('owl.carousel', data);

				$.each([
					'next', 'prev', 'to', 'destroy', 'refresh', 'replace', 'add', 'remove'
				], function(i, event) {
					data.register({ type: Owl.Type.Event, name: event });
					data.$element.on(event + '.owl.carousel.core', $.proxy(function(e) {
						if (e.namespace && e.relatedTarget !== this) {
							this.suppress([ event ]);
							data[event].apply(this, [].slice.call(arguments, 1));
							this.release([ event ]);
						}
					}, data));
				});
			}

			if (typeof option == 'string' && option.charAt(0) !== '_') {
				data[option].apply(data, args);
			}
		});
	};

	/**
	 * The constructor for the jQuery Plugin
	 * @public
	 */
	$.fn.owlCarousel.Constructor = Owl;

})(window.Zepto || window.jQuery, window, document);

/**
 * AutoRefresh Plugin
 * @version 2.3.4
 * @author Artus Kolanowski
 * @author David Deutsch
 * @license The MIT License (MIT)
 */
;(function($, window, document, undefined) {

	/**
	 * Creates the auto refresh plugin.
	 * @class The Auto Refresh Plugin
	 * @param {Owl} carousel - The Owl Carousel
	 */
	var AutoRefresh = function(carousel) {
		/**
		 * Reference to the core.
		 * @protected
		 * @type {Owl}
		 */
		this._core = carousel;

		/**
		 * Refresh interval.
		 * @protected
		 * @type {number}
		 */
		this._interval = null;

		/**
		 * Whether the element is currently visible or not.
		 * @protected
		 * @type {Boolean}
		 */
		this._visible = null;

		/**
		 * All event handlers.
		 * @protected
		 * @type {Object}
		 */
		this._handlers = {
			'initialized.owl.carousel': $.proxy(function(e) {
				if (e.namespace && this._core.settings.autoRefresh) {
					this.watch();
				}
			}, this)
		};

		// set default options
		this._core.options = $.extend({}, AutoRefresh.Defaults, this._core.options);

		// register event handlers
		this._core.$element.on(this._handlers);
	};

	/**
	 * Default options.
	 * @public
	 */
	AutoRefresh.Defaults = {
		autoRefresh: true,
		autoRefreshInterval: 500
	};

	/**
	 * Watches the element.
	 */
	AutoRefresh.prototype.watch = function() {
		if (this._interval) {
			return;
		}

		this._visible = this._core.isVisible();
		this._interval = window.setInterval($.proxy(this.refresh, this), this._core.settings.autoRefreshInterval);
	};

	/**
	 * Refreshes the element.
	 */
	AutoRefresh.prototype.refresh = function() {
		if (this._core.isVisible() === this._visible) {
			return;
		}

		this._visible = !this._visible;

		this._core.$element.toggleClass('owl-hidden', !this._visible);

		this._visible && (this._core.invalidate('width') && this._core.refresh());
	};

	/**
	 * Destroys the plugin.
	 */
	AutoRefresh.prototype.destroy = function() {
		var handler, property;

		window.clearInterval(this._interval);

		for (handler in this._handlers) {
			this._core.$element.off(handler, this._handlers[handler]);
		}
		for (property in Object.getOwnPropertyNames(this)) {
			typeof this[property] != 'function' && (this[property] = null);
		}
	};

	$.fn.owlCarousel.Constructor.Plugins.AutoRefresh = AutoRefresh;

})(window.Zepto || window.jQuery, window, document);

/**
 * Lazy Plugin
 * @version 2.3.4
 * @author Bartosz Wojciechowski
 * @author David Deutsch
 * @license The MIT License (MIT)
 */
;(function($, window, document, undefined) {

	/**
	 * Creates the lazy plugin.
	 * @class The Lazy Plugin
	 * @param {Owl} carousel - The Owl Carousel
	 */
	var Lazy = function(carousel) {

		/**
		 * Reference to the core.
		 * @protected
		 * @type {Owl}
		 */
		this._core = carousel;

		/**
		 * Already loaded items.
		 * @protected
		 * @type {Array.<jQuery>}
		 */
		this._loaded = [];

		/**
		 * Event handlers.
		 * @protected
		 * @type {Object}
		 */
		this._handlers = {
			'initialized.owl.carousel change.owl.carousel resized.owl.carousel': $.proxy(function(e) {
				if (!e.namespace) {
					return;
				}

				if (!this._core.settings || !this._core.settings.lazyLoad) {
					return;
				}

				if ((e.property && e.property.name == 'position') || e.type == 'initialized') {
					var settings = this._core.settings,
						n = (settings.center && Math.ceil(settings.items / 2) || settings.items),
						i = ((settings.center && n * -1) || 0),
						position = (e.property && e.property.value !== undefined ? e.property.value : this._core.current()) + i,
						clones = this._core.clones().length,
						load = $.proxy(function(i, v) { this.load(v) }, this);
					//TODO: Need documentation for this new option
					if (settings.lazyLoadEager > 0) {
						n += settings.lazyLoadEager;
						// If the carousel is looping also preload images that are to the "left"
						if (settings.loop) {
              position -= settings.lazyLoadEager;
              n++;
            }
					}

					while (i++ < n) {
						this.load(clones / 2 + this._core.relative(position));
						clones && $.each(this._core.clones(this._core.relative(position)), load);
						position++;
					}
				}
			}, this)
		};

		// set the default options
		this._core.options = $.extend({}, Lazy.Defaults, this._core.options);

		// register event handler
		this._core.$element.on(this._handlers);
	};

	/**
	 * Default options.
	 * @public
	 */
	Lazy.Defaults = {
		lazyLoad: false,
		lazyLoadEager: 0
	};

	/**
	 * Loads all resources of an item at the specified position.
	 * @param {Number} position - The absolute position of the item.
	 * @protected
	 */
	Lazy.prototype.load = function(position) {
		var $item = this._core.$stage.children().eq(position),
			$elements = $item && $item.find('.owl-lazy');

		if (!$elements || $.inArray($item.get(0), this._loaded) > -1) {
			return;
		}

		$elements.each($.proxy(function(index, element) {
			var $element = $(element), image,
                url = (window.devicePixelRatio > 1 && $element.attr('data-src-retina')) || $element.attr('data-src') || $element.attr('data-srcset');

			this._core.trigger('load', { element: $element, url: url }, 'lazy');

			if ($element.is('img')) {
				$element.one('load.owl.lazy', $.proxy(function() {
					$element.css('opacity', 1);
					this._core.trigger('loaded', { element: $element, url: url }, 'lazy');
				}, this)).attr('src', url);
            } else if ($element.is('source')) {
                $element.one('load.owl.lazy', $.proxy(function() {
                    this._core.trigger('loaded', { element: $element, url: url }, 'lazy');
                }, this)).attr('srcset', url);
			} else {
				image = new Image();
				image.onload = $.proxy(function() {
					$element.css({
						'background-image': 'url("' + url + '")',
						'opacity': '1'
					});
					this._core.trigger('loaded', { element: $element, url: url }, 'lazy');
				}, this);
				image.src = url;
			}
		}, this));

		this._loaded.push($item.get(0));
	};

	/**
	 * Destroys the plugin.
	 * @public
	 */
	Lazy.prototype.destroy = function() {
		var handler, property;

		for (handler in this.handlers) {
			this._core.$element.off(handler, this.handlers[handler]);
		}
		for (property in Object.getOwnPropertyNames(this)) {
			typeof this[property] != 'function' && (this[property] = null);
		}
	};

	$.fn.owlCarousel.Constructor.Plugins.Lazy = Lazy;

})(window.Zepto || window.jQuery, window, document);

/**
 * AutoHeight Plugin
 * @version 2.3.4
 * @author Bartosz Wojciechowski
 * @author David Deutsch
 * @license The MIT License (MIT)
 */
;(function($, window, document, undefined) {

	/**
	 * Creates the auto height plugin.
	 * @class The Auto Height Plugin
	 * @param {Owl} carousel - The Owl Carousel
	 */
	var AutoHeight = function(carousel) {
		/**
		 * Reference to the core.
		 * @protected
		 * @type {Owl}
		 */
		this._core = carousel;

		this._previousHeight = null;

		/**
		 * All event handlers.
		 * @protected
		 * @type {Object}
		 */
		this._handlers = {
			'initialized.owl.carousel refreshed.owl.carousel': $.proxy(function(e) {
				if (e.namespace && this._core.settings.autoHeight) {
					this.update();
				}
			}, this),
			'changed.owl.carousel': $.proxy(function(e) {
				if (e.namespace && this._core.settings.autoHeight && e.property.name === 'position'){
					this.update();
				}
			}, this),
			'loaded.owl.lazy': $.proxy(function(e) {
				if (e.namespace && this._core.settings.autoHeight
					&& e.element.closest('.' + this._core.settings.itemClass).index() === this._core.current()) {
					this.update();
				}
			}, this)
		};

		// set default options
		this._core.options = $.extend({}, AutoHeight.Defaults, this._core.options);

		// register event handlers
		this._core.$element.on(this._handlers);
		this._intervalId = null;
		var refThis = this;

		// These changes have been taken from a PR by gavrochelegnou proposed in #1575
		// and have been made compatible with the latest jQuery version
		$(window).on('load', function() {
			if (refThis._core.settings.autoHeight) {
				refThis.update();
			}
		});

		// Autoresize the height of the carousel when window is resized
		// When carousel has images, the height is dependent on the width
		// and should also change on resize
		$(window).resize(function() {
			if (refThis._core.settings.autoHeight) {
				if (refThis._intervalId != null) {
					clearTimeout(refThis._intervalId);
				}

				refThis._intervalId = setTimeout(function() {
					refThis.update();
				}, 250);
			}
		});

	};

	/**
	 * Default options.
	 * @public
	 */
	AutoHeight.Defaults = {
		autoHeight: false,
		autoHeightClass: 'owl-height'
	};

	/**
	 * Updates the view.
	 */
	AutoHeight.prototype.update = function() {
		var start = this._core._current,
			end = start + this._core.settings.items,
			lazyLoadEnabled = this._core.settings.lazyLoad,
			visible = this._core.$stage.children().toArray().slice(start, end),
			heights = [],
			maxheight = 0;

		$.each(visible, function(index, item) {
			heights.push($(item).height());
		});

		maxheight = Math.max.apply(null, heights);

		if (maxheight <= 1 && lazyLoadEnabled && this._previousHeight) {
			maxheight = this._previousHeight;
		}

		this._previousHeight = maxheight;

		this._core.$stage.parent()
			.height(maxheight)
			.addClass(this._core.settings.autoHeightClass);
	};

	AutoHeight.prototype.destroy = function() {
		var handler, property;

		for (handler in this._handlers) {
			this._core.$element.off(handler, this._handlers[handler]);
		}
		for (property in Object.getOwnPropertyNames(this)) {
			typeof this[property] !== 'function' && (this[property] = null);
		}
	};

	$.fn.owlCarousel.Constructor.Plugins.AutoHeight = AutoHeight;

})(window.Zepto || window.jQuery, window, document);

/**
 * Video Plugin
 * @version 2.3.4
 * @author Bartosz Wojciechowski
 * @author David Deutsch
 * @license The MIT License (MIT)
 */
;(function($, window, document, undefined) {

	/**
	 * Creates the video plugin.
	 * @class The Video Plugin
	 * @param {Owl} carousel - The Owl Carousel
	 */
	var Video = function(carousel) {
		/**
		 * Reference to the core.
		 * @protected
		 * @type {Owl}
		 */
		this._core = carousel;

		/**
		 * Cache all video URLs.
		 * @protected
		 * @type {Object}
		 */
		this._videos = {};

		/**
		 * Current playing item.
		 * @protected
		 * @type {jQuery}
		 */
		this._playing = null;

		/**
		 * All event handlers.
		 * @todo The cloned content removale is too late
		 * @protected
		 * @type {Object}
		 */
		this._handlers = {
			'initialized.owl.carousel': $.proxy(function(e) {
				if (e.namespace) {
					this._core.register({ type: 'state', name: 'playing', tags: [ 'interacting' ] });
				}
			}, this),
			'resize.owl.carousel': $.proxy(function(e) {
				if (e.namespace && this._core.settings.video && this.isInFullScreen()) {
					e.preventDefault();
				}
			}, this),
			'refreshed.owl.carousel': $.proxy(function(e) {
				if (e.namespace && this._core.is('resizing')) {
					this._core.$stage.find('.cloned .owl-video-frame').remove();
				}
			}, this),
			'changed.owl.carousel': $.proxy(function(e) {
				if (e.namespace && e.property.name === 'position' && this._playing) {
					this.stop();
				}
			}, this),
			'prepared.owl.carousel': $.proxy(function(e) {
				if (!e.namespace) {
					return;
				}

				var $element = $(e.content).find('.owl-video');

				if ($element.length) {
					$element.css('display', 'none');
					this.fetch($element, $(e.content));
				}
			}, this)
		};

		// set default options
		this._core.options = $.extend({}, Video.Defaults, this._core.options);

		// register event handlers
		this._core.$element.on(this._handlers);

		this._core.$element.on('click.owl.video', '.owl-video-play-icon', $.proxy(function(e) {
			this.play(e);
		}, this));
	};

	/**
	 * Default options.
	 * @public
	 */
	Video.Defaults = {
		video: false,
		videoHeight: false,
		videoWidth: false
	};

	/**
	 * Gets the video ID and the type (YouTube/Vimeo/vzaar only).
	 * @protected
	 * @param {jQuery} target - The target containing the video data.
	 * @param {jQuery} item - The item containing the video.
	 */
	Video.prototype.fetch = function(target, item) {
			var type = (function() {
					if (target.attr('data-vimeo-id')) {
						return 'vimeo';
					} else if (target.attr('data-vzaar-id')) {
						return 'vzaar'
					} else {
						return 'youtube';
					}
				})(),
				id = target.attr('data-vimeo-id') || target.attr('data-youtube-id') || target.attr('data-vzaar-id'),
				width = target.attr('data-width') || this._core.settings.videoWidth,
				height = target.attr('data-height') || this._core.settings.videoHeight,
				url = target.attr('href');

		if (url) {

			/*
					Parses the id's out of the following urls (and probably more):
					https://www.youtube.com/watch?v=:id
					https://youtu.be/:id
					https://vimeo.com/:id
					https://vimeo.com/channels/:channel/:id
					https://vimeo.com/groups/:group/videos/:id
					https://app.vzaar.com/videos/:id

					Visual example: https://regexper.com/#(http%3A%7Chttps%3A%7C)%5C%2F%5C%2F(player.%7Cwww.%7Capp.)%3F(vimeo%5C.com%7Cyoutu(be%5C.com%7C%5C.be%7Cbe%5C.googleapis%5C.com)%7Cvzaar%5C.com)%5C%2F(video%5C%2F%7Cvideos%5C%2F%7Cembed%5C%2F%7Cchannels%5C%2F.%2B%5C%2F%7Cgroups%5C%2F.%2B%5C%2F%7Cwatch%5C%3Fv%3D%7Cv%5C%2F)%3F(%5BA-Za-z0-9._%25-%5D*)(%5C%26%5CS%2B)%3F
			*/

			id = url.match(/(http:|https:|)\/\/(player.|www.|app.)?(vimeo\.com|youtu(be\.com|\.be|be\.googleapis\.com|be\-nocookie\.com)|vzaar\.com)\/(video\/|videos\/|embed\/|channels\/.+\/|groups\/.+\/|watch\?v=|v\/)?([A-Za-z0-9._%-]*)(\&\S+)?/);

			if (id[3].indexOf('youtu') > -1) {
				type = 'youtube';
			} else if (id[3].indexOf('vimeo') > -1) {
				type = 'vimeo';
			} else if (id[3].indexOf('vzaar') > -1) {
				type = 'vzaar';
			} else {
				throw new Error('Video URL not supported.');
			}
			id = id[6];
		} else {
			throw new Error('Missing video URL.');
		}

		this._videos[url] = {
			type: type,
			id: id,
			width: width,
			height: height
		};

		item.attr('data-video', url);

		this.thumbnail(target, this._videos[url]);
	};

	/**
	 * Creates video thumbnail.
	 * @protected
	 * @param {jQuery} target - The target containing the video data.
	 * @param {Object} info - The video info object.
	 * @see `fetch`
	 */
	Video.prototype.thumbnail = function(target, video) {
		var tnLink,
			icon,
			path,
			dimensions = video.width && video.height ? 'width:' + video.width + 'px;height:' + video.height + 'px;' : '',
			customTn = target.find('img'),
			srcType = 'src',
			lazyClass = '',
			settings = this._core.settings,
			create = function(path) {
				icon = '<div class="owl-video-play-icon"></div>';

				if (settings.lazyLoad) {
					tnLink = $('<div/>',{
						"class": 'owl-video-tn ' + lazyClass,
						"srcType": path
					});
				} else {
					tnLink = $( '<div/>', {
						"class": "owl-video-tn",
						"style": 'opacity:1;background-image:url(' + path + ')'
					});
				}
				target.after(tnLink);
				target.after(icon);
			};

		// wrap video content into owl-video-wrapper div
		target.wrap( $( '<div/>', {
			"class": "owl-video-wrapper",
			"style": dimensions
		}));

		if (this._core.settings.lazyLoad) {
			srcType = 'data-src';
			lazyClass = 'owl-lazy';
		}

		// custom thumbnail
		if (customTn.length) {
			create(customTn.attr(srcType));
			customTn.remove();
			return false;
		}

		if (video.type === 'youtube') {
			path = "//img.youtube.com/vi/" + video.id + "/hqdefault.jpg";
			create(path);
		} else if (video.type === 'vimeo') {
			$.ajax({
				type: 'GET',
				url: '//vimeo.com/api/v2/video/' + video.id + '.json',
				jsonp: 'callback',
				dataType: 'jsonp',
				success: function(data) {
					path = data[0].thumbnail_large;
					create(path);
				}
			});
		} else if (video.type === 'vzaar') {
			$.ajax({
				type: 'GET',
				url: '//vzaar.com/api/videos/' + video.id + '.json',
				jsonp: 'callback',
				dataType: 'jsonp',
				success: function(data) {
					path = data.framegrab_url;
					create(path);
				}
			});
		}
	};

	/**
	 * Stops the current video.
	 * @public
	 */
	Video.prototype.stop = function() {
		this._core.trigger('stop', null, 'video');
		this._playing.find('.owl-video-frame').remove();
		this._playing.removeClass('owl-video-playing');
		this._playing = null;
		this._core.leave('playing');
		this._core.trigger('stopped', null, 'video');
	};

	/**
	 * Starts the current video.
	 * @public
	 * @param {Event} event - The event arguments.
	 */
	Video.prototype.play = function(event) {
		var target = $(event.target),
			item = target.closest('.' + this._core.settings.itemClass),
			video = this._videos[item.attr('data-video')],
			width = video.width || '100%',
			height = video.height || this._core.$stage.height(),
			html,
			iframe;

		if (this._playing) {
			return;
		}

		this._core.enter('playing');
		this._core.trigger('play', null, 'video');

		item = this._core.items(this._core.relative(item.index()));

		this._core.reset(item.index());

		html = $( '<iframe frameborder="0" allowfullscreen mozallowfullscreen webkitAllowFullScreen ></iframe>' );
		html.attr( 'height', height );
		html.attr( 'width', width );
		if (video.type === 'youtube') {
			html.attr( 'src', '//www.youtube.com/embed/' + video.id + '?autoplay=1&rel=0&v=' + video.id );
		} else if (video.type === 'vimeo') {
			html.attr( 'src', '//player.vimeo.com/video/' + video.id + '?autoplay=1' );
		} else if (video.type === 'vzaar') {
			html.attr( 'src', '//view.vzaar.com/' + video.id + '/player?autoplay=true' );
		}

		iframe = $(html).wrap( '<div class="owl-video-frame" />' ).insertAfter(item.find('.owl-video'));

		this._playing = item.addClass('owl-video-playing');
	};

	/**
	 * Checks whether an video is currently in full screen mode or not.
	 * @todo Bad style because looks like a readonly method but changes members.
	 * @protected
	 * @returns {Boolean}
	 */
	Video.prototype.isInFullScreen = function() {
		var element = document.fullscreenElement || document.mozFullScreenElement ||
				document.webkitFullscreenElement;

		return element && $(element).parent().hasClass('owl-video-frame');
	};

	/**
	 * Destroys the plugin.
	 */
	Video.prototype.destroy = function() {
		var handler, property;

		this._core.$element.off('click.owl.video');

		for (handler in this._handlers) {
			this._core.$element.off(handler, this._handlers[handler]);
		}
		for (property in Object.getOwnPropertyNames(this)) {
			typeof this[property] != 'function' && (this[property] = null);
		}
	};

	$.fn.owlCarousel.Constructor.Plugins.Video = Video;

})(window.Zepto || window.jQuery, window, document);

/**
 * Animate Plugin
 * @version 2.3.4
 * @author Bartosz Wojciechowski
 * @author David Deutsch
 * @license The MIT License (MIT)
 */
;(function($, window, document, undefined) {

	/**
	 * Creates the animate plugin.
	 * @class The Navigation Plugin
	 * @param {Owl} scope - The Owl Carousel
	 */
	var Animate = function(scope) {
		this.core = scope;
		this.core.options = $.extend({}, Animate.Defaults, this.core.options);
		this.swapping = true;
		this.previous = undefined;
		this.next = undefined;

		this.handlers = {
			'change.owl.carousel': $.proxy(function(e) {
				if (e.namespace && e.property.name == 'position') {
					this.previous = this.core.current();
					this.next = e.property.value;
				}
			}, this),
			'drag.owl.carousel dragged.owl.carousel translated.owl.carousel': $.proxy(function(e) {
				if (e.namespace) {
					this.swapping = e.type == 'translated';
				}
			}, this),
			'translate.owl.carousel': $.proxy(function(e) {
				if (e.namespace && this.swapping && (this.core.options.animateOut || this.core.options.animateIn)) {
					this.swap();
				}
			}, this)
		};

		this.core.$element.on(this.handlers);
	};

	/**
	 * Default options.
	 * @public
	 */
	Animate.Defaults = {
		animateOut: false,
		animateIn: false
	};

	/**
	 * Toggles the animation classes whenever an translations starts.
	 * @protected
	 * @returns {Boolean|undefined}
	 */
	Animate.prototype.swap = function() {

		if (this.core.settings.items !== 1) {
			return;
		}

		if (!$.support.animation || !$.support.transition) {
			return;
		}

		this.core.speed(0);

		var left,
			clear = $.proxy(this.clear, this),
			previous = this.core.$stage.children().eq(this.previous),
			next = this.core.$stage.children().eq(this.next),
			incoming = this.core.settings.animateIn,
			outgoing = this.core.settings.animateOut;

		if (this.core.current() === this.previous) {
			return;
		}

		if (outgoing) {
			left = this.core.coordinates(this.previous) - this.core.coordinates(this.next);
			previous.one($.support.animation.end, clear)
				.css( { 'left': left + 'px' } )
				.addClass('animated owl-animated-out')
				.addClass(outgoing);
		}

		if (incoming) {
			next.one($.support.animation.end, clear)
				.addClass('animated owl-animated-in')
				.addClass(incoming);
		}
	};

	Animate.prototype.clear = function(e) {
		$(e.target).css( { 'left': '' } )
			.removeClass('animated owl-animated-out owl-animated-in')
			.removeClass(this.core.settings.animateIn)
			.removeClass(this.core.settings.animateOut);
		this.core.onTransitionEnd();
	};

	/**
	 * Destroys the plugin.
	 * @public
	 */
	Animate.prototype.destroy = function() {
		var handler, property;

		for (handler in this.handlers) {
			this.core.$element.off(handler, this.handlers[handler]);
		}
		for (property in Object.getOwnPropertyNames(this)) {
			typeof this[property] != 'function' && (this[property] = null);
		}
	};

	$.fn.owlCarousel.Constructor.Plugins.Animate = Animate;

})(window.Zepto || window.jQuery, window, document);

/**
 * Autoplay Plugin
 * @version 2.3.4
 * @author Bartosz Wojciechowski
 * @author Artus Kolanowski
 * @author David Deutsch
 * @author Tom De Caluwé
 * @license The MIT License (MIT)
 */
;(function($, window, document, undefined) {

	/**
	 * Creates the autoplay plugin.
	 * @class The Autoplay Plugin
	 * @param {Owl} scope - The Owl Carousel
	 */
	var Autoplay = function(carousel) {
		/**
		 * Reference to the core.
		 * @protected
		 * @type {Owl}
		 */
		this._core = carousel;

		/**
		 * The autoplay timeout id.
		 * @type {Number}
		 */
		this._call = null;

		/**
		 * Depending on the state of the plugin, this variable contains either
		 * the start time of the timer or the current timer value if it's
		 * paused. Since we start in a paused state we initialize the timer
		 * value.
		 * @type {Number}
		 */
		this._time = 0;

		/**
		 * Stores the timeout currently used.
		 * @type {Number}
		 */
		this._timeout = 0;

		/**
		 * Indicates whenever the autoplay is paused.
		 * @type {Boolean}
		 */
		this._paused = true;

		/**
		 * All event handlers.
		 * @protected
		 * @type {Object}
		 */
		this._handlers = {
			'changed.owl.carousel': $.proxy(function(e) {
				if (e.namespace && e.property.name === 'settings') {
					if (this._core.settings.autoplay) {
						this.play();
					} else {
						this.stop();
					}
				} else if (e.namespace && e.property.name === 'position' && this._paused) {
					// Reset the timer. This code is triggered when the position
					// of the carousel was changed through user interaction.
					this._time = 0;
				}
			}, this),
			'initialized.owl.carousel': $.proxy(function(e) {
				if (e.namespace && this._core.settings.autoplay) {
					this.play();
				}
			}, this),
			'play.owl.autoplay': $.proxy(function(e, t, s) {
				if (e.namespace) {
					this.play(t, s);
				}
			}, this),
			'stop.owl.autoplay': $.proxy(function(e) {
				if (e.namespace) {
					this.stop();
				}
			}, this),
			'mouseover.owl.autoplay': $.proxy(function() {
				if (this._core.settings.autoplayHoverPause && this._core.is('rotating')) {
					this.pause();
				}
			}, this),
			'mouseleave.owl.autoplay': $.proxy(function() {
				if (this._core.settings.autoplayHoverPause && this._core.is('rotating')) {
					this.play();
				}
			}, this),
			'touchstart.owl.core': $.proxy(function() {
				if (this._core.settings.autoplayHoverPause && this._core.is('rotating')) {
					this.pause();
				}
			}, this),
			'touchend.owl.core': $.proxy(function() {
				if (this._core.settings.autoplayHoverPause) {
					this.play();
				}
			}, this)
		};

		// register event handlers
		this._core.$element.on(this._handlers);

		// set default options
		this._core.options = $.extend({}, Autoplay.Defaults, this._core.options);
	};

	/**
	 * Default options.
	 * @public
	 */
	Autoplay.Defaults = {
		autoplay: false,
		autoplayTimeout: 5000,
		autoplayHoverPause: false,
		autoplaySpeed: false
	};

	/**
	 * Transition to the next slide and set a timeout for the next transition.
	 * @private
	 * @param {Number} [speed] - The animation speed for the animations.
	 */
	Autoplay.prototype._next = function(speed) {
		this._call = window.setTimeout(
			$.proxy(this._next, this, speed),
			this._timeout * (Math.round(this.read() / this._timeout) + 1) - this.read()
		);

		if (this._core.is('interacting') || document.hidden) {
			return;
		}
		this._core.next(speed || this._core.settings.autoplaySpeed);
	}

	/**
	 * Reads the current timer value when the timer is playing.
	 * @public
	 */
	Autoplay.prototype.read = function() {
		return new Date().getTime() - this._time;
	};

	/**
	 * Starts the autoplay.
	 * @public
	 * @param {Number} [timeout] - The interval before the next animation starts.
	 * @param {Number} [speed] - The animation speed for the animations.
	 */
	Autoplay.prototype.play = function(timeout, speed) {
		var elapsed;

		if (!this._core.is('rotating')) {
			this._core.enter('rotating');
		}

		timeout = timeout || this._core.settings.autoplayTimeout;

		// Calculate the elapsed time since the last transition. If the carousel
		// wasn't playing this calculation will yield zero.
		elapsed = Math.min(this._time % (this._timeout || timeout), timeout);

		if (this._paused) {
			// Start the clock.
			this._time = this.read();
			this._paused = false;
		} else {
			// Clear the active timeout to allow replacement.
			window.clearTimeout(this._call);
		}

		// Adjust the origin of the timer to match the new timeout value.
		this._time += this.read() % timeout - elapsed;

		this._timeout = timeout;
		this._call = window.setTimeout($.proxy(this._next, this, speed), timeout - elapsed);
	};

	/**
	 * Stops the autoplay.
	 * @public
	 */
	Autoplay.prototype.stop = function() {
		if (this._core.is('rotating')) {
			// Reset the clock.
			this._time = 0;
			this._paused = true;

			window.clearTimeout(this._call);
			this._core.leave('rotating');
		}
	};

	/**
	 * Pauses the autoplay.
	 * @public
	 */
	Autoplay.prototype.pause = function() {
		if (this._core.is('rotating') && !this._paused) {
			// Pause the clock.
			this._time = this.read();
			this._paused = true;

			window.clearTimeout(this._call);
		}
	};

	/**
	 * Destroys the plugin.
	 */
	Autoplay.prototype.destroy = function() {
		var handler, property;

		this.stop();

		for (handler in this._handlers) {
			this._core.$element.off(handler, this._handlers[handler]);
		}
		for (property in Object.getOwnPropertyNames(this)) {
			typeof this[property] != 'function' && (this[property] = null);
		}
	};

	$.fn.owlCarousel.Constructor.Plugins.autoplay = Autoplay;

})(window.Zepto || window.jQuery, window, document);

/**
 * Navigation Plugin
 * @version 2.3.4
 * @author Artus Kolanowski
 * @author David Deutsch
 * @license The MIT License (MIT)
 */
;(function($, window, document, undefined) {
	'use strict';

	/**
	 * Creates the navigation plugin.
	 * @class The Navigation Plugin
	 * @param {Owl} carousel - The Owl Carousel.
	 */
	var Navigation = function(carousel) {
		/**
		 * Reference to the core.
		 * @protected
		 * @type {Owl}
		 */
		this._core = carousel;

		/**
		 * Indicates whether the plugin is initialized or not.
		 * @protected
		 * @type {Boolean}
		 */
		this._initialized = false;

		/**
		 * The current paging indexes.
		 * @protected
		 * @type {Array}
		 */
		this._pages = [];

		/**
		 * All DOM elements of the user interface.
		 * @protected
		 * @type {Object}
		 */
		this._controls = {};

		/**
		 * Markup for an indicator.
		 * @protected
		 * @type {Array.<String>}
		 */
		this._templates = [];

		/**
		 * The carousel element.
		 * @type {jQuery}
		 */
		this.$element = this._core.$element;

		/**
		 * Overridden methods of the carousel.
		 * @protected
		 * @type {Object}
		 */
		this._overrides = {
			next: this._core.next,
			prev: this._core.prev,
			to: this._core.to
		};

		/**
		 * All event handlers.
		 * @protected
		 * @type {Object}
		 */
		this._handlers = {
			'prepared.owl.carousel': $.proxy(function(e) {
				if (e.namespace && this._core.settings.dotsData) {
					this._templates.push('<div class="' + this._core.settings.dotClass + '">' +
						$(e.content).find('[data-dot]').addBack('[data-dot]').attr('data-dot') + '</div>');
				}
			}, this),
			'added.owl.carousel': $.proxy(function(e) {
				if (e.namespace && this._core.settings.dotsData) {
					this._templates.splice(e.position, 0, this._templates.pop());
				}
			}, this),
			'remove.owl.carousel': $.proxy(function(e) {
				if (e.namespace && this._core.settings.dotsData) {
					this._templates.splice(e.position, 1);
				}
			}, this),
			'changed.owl.carousel': $.proxy(function(e) {
				if (e.namespace && e.property.name == 'position') {
					this.draw();
				}
			}, this),
			'initialized.owl.carousel': $.proxy(function(e) {
				if (e.namespace && !this._initialized) {
					this._core.trigger('initialize', null, 'navigation');
					this.initialize();
					this.update();
					this.draw();
					this._initialized = true;
					this._core.trigger('initialized', null, 'navigation');
				}
			}, this),
			'refreshed.owl.carousel': $.proxy(function(e) {
				if (e.namespace && this._initialized) {
					this._core.trigger('refresh', null, 'navigation');
					this.update();
					this.draw();
					this._core.trigger('refreshed', null, 'navigation');
				}
			}, this)
		};

		// set default options
		this._core.options = $.extend({}, Navigation.Defaults, this._core.options);

		// register event handlers
		this.$element.on(this._handlers);
	};

	/**
	 * Default options.
	 * @public
	 * @todo Rename `slideBy` to `navBy`
	 */
	Navigation.Defaults = {
		nav: false,
		navText: [
			'<span aria-label="' + 'Previous' + '">&#x2039;</span>',
			'<span aria-label="' + 'Next' + '">&#x203a;</span>'
		],
		navSpeed: false,
		navElement: 'button type="button" role="presentation"',
		navContainer: false,
		navContainerClass: 'owl-nav',
		navClass: [
			'owl-prev',
			'owl-next'
		],
		slideBy: 1,
		dotClass: 'owl-dot',
		dotsClass: 'owl-dots',
		dots: true,
		dotsEach: false,
		dotsData: false,
		dotsSpeed: false,
		dotsContainer: false
	};

	/**
	 * Initializes the layout of the plugin and extends the carousel.
	 * @protected
	 */
	Navigation.prototype.initialize = function() {
		var override,
			settings = this._core.settings;

		// create DOM structure for relative navigation
		this._controls.$relative = (settings.navContainer ? $(settings.navContainer)
			: $('<div>').addClass(settings.navContainerClass).appendTo(this.$element)).addClass('disabled');

		this._controls.$previous = $('<' + settings.navElement + '>')
			.addClass(settings.navClass[0])
			.html(settings.navText[0])
			.prependTo(this._controls.$relative)
			.on('click', $.proxy(function(e) {
				this.prev(settings.navSpeed);
			}, this));
		this._controls.$next = $('<' + settings.navElement + '>')
			.addClass(settings.navClass[1])
			.html(settings.navText[1])
			.appendTo(this._controls.$relative)
			.on('click', $.proxy(function(e) {
				this.next(settings.navSpeed);
			}, this));

		// create DOM structure for absolute navigation
		if (!settings.dotsData) {
			this._templates = [ $('<button role="button">')
				.addClass(settings.dotClass)
				.append($('<span>'))
				.prop('outerHTML') ];
		}

		this._controls.$absolute = (settings.dotsContainer ? $(settings.dotsContainer)
			: $('<div>').addClass(settings.dotsClass).appendTo(this.$element)).addClass('disabled');

		this._controls.$absolute.on('click', 'button', $.proxy(function(e) {
			var index = $(e.target).parent().is(this._controls.$absolute)
				? $(e.target).index() : $(e.target).parent().index();

			e.preventDefault();

			this.to(index, settings.dotsSpeed);
		}, this));

		/*$el.on('focusin', function() {
			$(document).off(".carousel");

			$(document).on('keydown.carousel', function(e) {
				if(e.keyCode == 37) {
					$el.trigger('prev.owl')
				}
				if(e.keyCode == 39) {
					$el.trigger('next.owl')
				}
			});
		});*/

		// override public methods of the carousel
		for (override in this._overrides) {
			this._core[override] = $.proxy(this[override], this);
		}
	};

	/**
	 * Destroys the plugin.
	 * @protected
	 */
	Navigation.prototype.destroy = function() {
		var handler, control, property, override, settings;
		settings = this._core.settings;

		for (handler in this._handlers) {
			this.$element.off(handler, this._handlers[handler]);
		}
		for (control in this._controls) {
			if (control === '$relative' && settings.navContainer) {
				this._controls[control].html('');
			} else {
				this._controls[control].remove();
			}
		}
		for (override in this.overides) {
			this._core[override] = this._overrides[override];
		}
		for (property in Object.getOwnPropertyNames(this)) {
			typeof this[property] != 'function' && (this[property] = null);
		}
	};

	/**
	 * Updates the internal state.
	 * @protected
	 */
	Navigation.prototype.update = function() {
		var i, j, k,
			lower = this._core.clones().length / 2,
			upper = lower + this._core.items().length,
			maximum = this._core.maximum(true),
			settings = this._core.settings,
			size = settings.center || settings.autoWidth || settings.dotsData
				? 1 : settings.dotsEach || settings.items;

		if (settings.slideBy !== 'page') {
			settings.slideBy = Math.min(settings.slideBy, settings.items);
		}

		if (settings.dots || settings.slideBy == 'page') {
			this._pages = [];

			for (i = lower, j = 0, k = 0; i < upper; i++) {
				if (j >= size || j === 0) {
					this._pages.push({
						start: Math.min(maximum, i - lower),
						end: i - lower + size - 1
					});
					if (Math.min(maximum, i - lower) === maximum) {
						break;
					}
					j = 0, ++k;
				}
				j += this._core.mergers(this._core.relative(i));
			}
		}
	};

	/**
	 * Draws the user interface.
	 * @todo The option `dotsData` wont work.
	 * @protected
	 */
	Navigation.prototype.draw = function() {
		var difference,
			settings = this._core.settings,
			disabled = this._core.items().length <= settings.items,
			index = this._core.relative(this._core.current()),
			loop = settings.loop || settings.rewind;

		this._controls.$relative.toggleClass('disabled', !settings.nav || disabled);

		if (settings.nav) {
			this._controls.$previous.toggleClass('disabled', !loop && index <= this._core.minimum(true));
			this._controls.$next.toggleClass('disabled', !loop && index >= this._core.maximum(true));
		}

		this._controls.$absolute.toggleClass('disabled', !settings.dots || disabled);

		if (settings.dots) {
			difference = this._pages.length - this._controls.$absolute.children().length;

			if (settings.dotsData && difference !== 0) {
				this._controls.$absolute.html(this._templates.join(''));
			} else if (difference > 0) {
				this._controls.$absolute.append(new Array(difference + 1).join(this._templates[0]));
			} else if (difference < 0) {
				this._controls.$absolute.children().slice(difference).remove();
			}

			this._controls.$absolute.find('.active').removeClass('active');
			this._controls.$absolute.children().eq($.inArray(this.current(), this._pages)).addClass('active');
		}
	};

	/**
	 * Extends event data.
	 * @protected
	 * @param {Event} event - The event object which gets thrown.
	 */
	Navigation.prototype.onTrigger = function(event) {
		var settings = this._core.settings;

		event.page = {
			index: $.inArray(this.current(), this._pages),
			count: this._pages.length,
			size: settings && (settings.center || settings.autoWidth || settings.dotsData
				? 1 : settings.dotsEach || settings.items)
		};
	};

	/**
	 * Gets the current page position of the carousel.
	 * @protected
	 * @returns {Number}
	 */
	Navigation.prototype.current = function() {
		var current = this._core.relative(this._core.current());
		return $.grep(this._pages, $.proxy(function(page, index) {
			return page.start <= current && page.end >= current;
		}, this)).pop();
	};

	/**
	 * Gets the current succesor/predecessor position.
	 * @protected
	 * @returns {Number}
	 */
	Navigation.prototype.getPosition = function(successor) {
		var position, length,
			settings = this._core.settings;

		if (settings.slideBy == 'page') {
			position = $.inArray(this.current(), this._pages);
			length = this._pages.length;
			successor ? ++position : --position;
			position = this._pages[((position % length) + length) % length].start;
		} else {
			position = this._core.relative(this._core.current());
			length = this._core.items().length;
			successor ? position += settings.slideBy : position -= settings.slideBy;
		}

		return position;
	};

	/**
	 * Slides to the next item or page.
	 * @public
	 * @param {Number} [speed=false] - The time in milliseconds for the transition.
	 */
	Navigation.prototype.next = function(speed) {
		$.proxy(this._overrides.to, this._core)(this.getPosition(true), speed);
	};

	/**
	 * Slides to the previous item or page.
	 * @public
	 * @param {Number} [speed=false] - The time in milliseconds for the transition.
	 */
	Navigation.prototype.prev = function(speed) {
		$.proxy(this._overrides.to, this._core)(this.getPosition(false), speed);
	};

	/**
	 * Slides to the specified item or page.
	 * @public
	 * @param {Number} position - The position of the item or page.
	 * @param {Number} [speed] - The time in milliseconds for the transition.
	 * @param {Boolean} [standard=false] - Whether to use the standard behaviour or not.
	 */
	Navigation.prototype.to = function(position, speed, standard) {
		var length;

		if (!standard && this._pages.length) {
			length = this._pages.length;
			$.proxy(this._overrides.to, this._core)(this._pages[((position % length) + length) % length].start, speed);
		} else {
			$.proxy(this._overrides.to, this._core)(position, speed);
		}
	};

	$.fn.owlCarousel.Constructor.Plugins.Navigation = Navigation;

})(window.Zepto || window.jQuery, window, document);

/**
 * Hash Plugin
 * @version 2.3.4
 * @author Artus Kolanowski
 * @author David Deutsch
 * @license The MIT License (MIT)
 */
;(function($, window, document, undefined) {
	'use strict';

	/**
	 * Creates the hash plugin.
	 * @class The Hash Plugin
	 * @param {Owl} carousel - The Owl Carousel
	 */
	var Hash = function(carousel) {
		/**
		 * Reference to the core.
		 * @protected
		 * @type {Owl}
		 */
		this._core = carousel;

		/**
		 * Hash index for the items.
		 * @protected
		 * @type {Object}
		 */
		this._hashes = {};

		/**
		 * The carousel element.
		 * @type {jQuery}
		 */
		this.$element = this._core.$element;

		/**
		 * All event handlers.
		 * @protected
		 * @type {Object}
		 */
		this._handlers = {
			'initialized.owl.carousel': $.proxy(function(e) {
				if (e.namespace && this._core.settings.startPosition === 'URLHash') {
					$(window).trigger('hashchange.owl.navigation');
				}
			}, this),
			'prepared.owl.carousel': $.proxy(function(e) {
				if (e.namespace) {
					var hash = $(e.content).find('[data-hash]').addBack('[data-hash]').attr('data-hash');

					if (!hash) {
						return;
					}

					this._hashes[hash] = e.content;
				}
			}, this),
			'changed.owl.carousel': $.proxy(function(e) {
				if (e.namespace && e.property.name === 'position') {
					var current = this._core.items(this._core.relative(this._core.current())),
						hash = $.map(this._hashes, function(item, hash) {
							return item === current ? hash : null;
						}).join();

					if (!hash || window.location.hash.slice(1) === hash) {
						return;
					}

					window.location.hash = hash;
				}
			}, this)
		};

		// set default options
		this._core.options = $.extend({}, Hash.Defaults, this._core.options);

		// register the event handlers
		this.$element.on(this._handlers);

		// register event listener for hash navigation
		$(window).on('hashchange.owl.navigation', $.proxy(function(e) {
			var hash = window.location.hash.substring(1),
				items = this._core.$stage.children(),
				position = this._hashes[hash] && items.index(this._hashes[hash]);

			if (position === undefined || position === this._core.current()) {
				return;
			}

			this._core.to(this._core.relative(position), false, true);
		}, this));
	};

	/**
	 * Default options.
	 * @public
	 */
	Hash.Defaults = {
		URLhashListener: false
	};

	/**
	 * Destroys the plugin.
	 * @public
	 */
	Hash.prototype.destroy = function() {
		var handler, property;

		$(window).off('hashchange.owl.navigation');

		for (handler in this._handlers) {
			this._core.$element.off(handler, this._handlers[handler]);
		}
		for (property in Object.getOwnPropertyNames(this)) {
			typeof this[property] != 'function' && (this[property] = null);
		}
	};

	$.fn.owlCarousel.Constructor.Plugins.Hash = Hash;

})(window.Zepto || window.jQuery, window, document);

/**
 * Support Plugin
 *
 * @version 2.3.4
 * @author Vivid Planet Software GmbH
 * @author Artus Kolanowski
 * @author David Deutsch
 * @license The MIT License (MIT)
 */
;(function($, window, document, undefined) {

	var style = $('<support>').get(0).style,
		prefixes = 'Webkit Moz O ms'.split(' '),
		events = {
			transition: {
				end: {
					WebkitTransition: 'webkitTransitionEnd',
					MozTransition: 'transitionend',
					OTransition: 'oTransitionEnd',
					transition: 'transitionend'
				}
			},
			animation: {
				end: {
					WebkitAnimation: 'webkitAnimationEnd',
					MozAnimation: 'animationend',
					OAnimation: 'oAnimationEnd',
					animation: 'animationend'
				}
			}
		},
		tests = {
			csstransforms: function() {
				return !!test('transform');
			},
			csstransforms3d: function() {
				return !!test('perspective');
			},
			csstransitions: function() {
				return !!test('transition');
			},
			cssanimations: function() {
				return !!test('animation');
			}
		};

	function test(property, prefixed) {
		var result = false,
			upper = property.charAt(0).toUpperCase() + property.slice(1);

		$.each((property + ' ' + prefixes.join(upper + ' ') + upper).split(' '), function(i, property) {
			if (style[property] !== undefined) {
				result = prefixed ? property : true;
				return false;
			}
		});

		return result;
	}

	function prefixed(property) {
		return test(property, true);
	}

	if (tests.csstransitions()) {
		/* jshint -W053 */
		$.support.transition = new String(prefixed('transition'))
		$.support.transition.end = events.transition.end[ $.support.transition ];
	}

	if (tests.cssanimations()) {
		/* jshint -W053 */
		$.support.animation = new String(prefixed('animation'))
		$.support.animation.end = events.animation.end[ $.support.animation ];
	}

	if (tests.csstransforms()) {
		/* jshint -W053 */
		$.support.transform = new String(prefixed('transform'));
		$.support.transform3d = tests.csstransforms3d();
	}

})(window.Zepto || window.jQuery, window, document);


/***/ }),

/***/ "jquery":
/*!*************************!*\
  !*** external "jQuery" ***!
  \*************************/
/***/ (function(module) {

"use strict";
module.exports = window["jQuery"];

/***/ }),

/***/ "./node_modules/axios/package.json":
/*!*****************************************!*\
  !*** ./node_modules/axios/package.json ***!
  \*****************************************/
/***/ (function(module) {

"use strict";
module.exports = JSON.parse('{"name":"axios","version":"0.21.4","description":"Promise based HTTP client for the browser and node.js","main":"index.js","scripts":{"test":"grunt test","start":"node ./sandbox/server.js","build":"NODE_ENV=production grunt build","preversion":"npm test","version":"npm run build && grunt version && git add -A dist && git add CHANGELOG.md bower.json package.json","postversion":"git push && git push --tags","examples":"node ./examples/server.js","coveralls":"cat coverage/lcov.info | ./node_modules/coveralls/bin/coveralls.js","fix":"eslint --fix lib/**/*.js"},"repository":{"type":"git","url":"https://github.com/axios/axios.git"},"keywords":["xhr","http","ajax","promise","node"],"author":"Matt Zabriskie","license":"MIT","bugs":{"url":"https://github.com/axios/axios/issues"},"homepage":"https://axios-http.com","devDependencies":{"coveralls":"^3.0.0","es6-promise":"^4.2.4","grunt":"^1.3.0","grunt-banner":"^0.6.0","grunt-cli":"^1.2.0","grunt-contrib-clean":"^1.1.0","grunt-contrib-watch":"^1.0.0","grunt-eslint":"^23.0.0","grunt-karma":"^4.0.0","grunt-mocha-test":"^0.13.3","grunt-ts":"^6.0.0-beta.19","grunt-webpack":"^4.0.2","istanbul-instrumenter-loader":"^1.0.0","jasmine-core":"^2.4.1","karma":"^6.3.2","karma-chrome-launcher":"^3.1.0","karma-firefox-launcher":"^2.1.0","karma-jasmine":"^1.1.1","karma-jasmine-ajax":"^0.1.13","karma-safari-launcher":"^1.0.0","karma-sauce-launcher":"^4.3.6","karma-sinon":"^1.0.5","karma-sourcemap-loader":"^0.3.8","karma-webpack":"^4.0.2","load-grunt-tasks":"^3.5.2","minimist":"^1.2.0","mocha":"^8.2.1","sinon":"^4.5.0","terser-webpack-plugin":"^4.2.3","typescript":"^4.0.5","url-search-params":"^0.10.0","webpack":"^4.44.2","webpack-dev-server":"^3.11.0"},"browser":{"./lib/adapters/http.js":"./lib/adapters/xhr.js"},"jsdelivr":"dist/axios.min.js","unpkg":"dist/axios.min.js","typings":"./index.d.ts","dependencies":{"follow-redirects":"^1.14.0"},"bundlesize":[{"path":"./dist/axios.min.js","threshold":"5kB"}]}');

/***/ })

/******/ 	});
/************************************************************************/
/******/ 	// The module cache
/******/ 	var __webpack_module_cache__ = {};
/******/ 	
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/ 		// Check if module is in cache
/******/ 		var cachedModule = __webpack_module_cache__[moduleId];
/******/ 		if (cachedModule !== undefined) {
/******/ 			return cachedModule.exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = __webpack_module_cache__[moduleId] = {
/******/ 			// no module.id needed
/******/ 			// no module.loaded needed
/******/ 			exports: {}
/******/ 		};
/******/ 	
/******/ 		// Execute the module function
/******/ 		__webpack_modules__[moduleId](module, module.exports, __webpack_require__);
/******/ 	
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/ 	
/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = __webpack_modules__;
/******/ 	
/************************************************************************/
/******/ 	/* webpack/runtime/chunk loaded */
/******/ 	!function() {
/******/ 		var deferred = [];
/******/ 		__webpack_require__.O = function(result, chunkIds, fn, priority) {
/******/ 			if(chunkIds) {
/******/ 				priority = priority || 0;
/******/ 				for(var i = deferred.length; i > 0 && deferred[i - 1][2] > priority; i--) deferred[i] = deferred[i - 1];
/******/ 				deferred[i] = [chunkIds, fn, priority];
/******/ 				return;
/******/ 			}
/******/ 			var notFulfilled = Infinity;
/******/ 			for (var i = 0; i < deferred.length; i++) {
/******/ 				var chunkIds = deferred[i][0];
/******/ 				var fn = deferred[i][1];
/******/ 				var priority = deferred[i][2];
/******/ 				var fulfilled = true;
/******/ 				for (var j = 0; j < chunkIds.length; j++) {
/******/ 					if ((priority & 1 === 0 || notFulfilled >= priority) && Object.keys(__webpack_require__.O).every(function(key) { return __webpack_require__.O[key](chunkIds[j]); })) {
/******/ 						chunkIds.splice(j--, 1);
/******/ 					} else {
/******/ 						fulfilled = false;
/******/ 						if(priority < notFulfilled) notFulfilled = priority;
/******/ 					}
/******/ 				}
/******/ 				if(fulfilled) {
/******/ 					deferred.splice(i--, 1)
/******/ 					var r = fn();
/******/ 					if (r !== undefined) result = r;
/******/ 				}
/******/ 			}
/******/ 			return result;
/******/ 		};
/******/ 	}();
/******/ 	
/******/ 	/* webpack/runtime/compat get default export */
/******/ 	!function() {
/******/ 		// getDefaultExport function for compatibility with non-harmony modules
/******/ 		__webpack_require__.n = function(module) {
/******/ 			var getter = module && module.__esModule ?
/******/ 				function() { return module['default']; } :
/******/ 				function() { return module; };
/******/ 			__webpack_require__.d(getter, { a: getter });
/******/ 			return getter;
/******/ 		};
/******/ 	}();
/******/ 	
/******/ 	/* webpack/runtime/define property getters */
/******/ 	!function() {
/******/ 		// define getter functions for harmony exports
/******/ 		__webpack_require__.d = function(exports, definition) {
/******/ 			for(var key in definition) {
/******/ 				if(__webpack_require__.o(definition, key) && !__webpack_require__.o(exports, key)) {
/******/ 					Object.defineProperty(exports, key, { enumerable: true, get: definition[key] });
/******/ 				}
/******/ 			}
/******/ 		};
/******/ 	}();
/******/ 	
/******/ 	/* webpack/runtime/hasOwnProperty shorthand */
/******/ 	!function() {
/******/ 		__webpack_require__.o = function(obj, prop) { return Object.prototype.hasOwnProperty.call(obj, prop); }
/******/ 	}();
/******/ 	
/******/ 	/* webpack/runtime/make namespace object */
/******/ 	!function() {
/******/ 		// define __esModule on exports
/******/ 		__webpack_require__.r = function(exports) {
/******/ 			if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 				Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 			}
/******/ 			Object.defineProperty(exports, '__esModule', { value: true });
/******/ 		};
/******/ 	}();
/******/ 	
/******/ 	/* webpack/runtime/jsonp chunk loading */
/******/ 	!function() {
/******/ 		// no baseURI
/******/ 		
/******/ 		// object to store loaded and loading chunks
/******/ 		// undefined = chunk not loaded, null = chunk preloaded/prefetched
/******/ 		// [resolve, reject, Promise] = chunk loading, 0 = chunk loaded
/******/ 		var installedChunks = {
/******/ 			"index": 0,
/******/ 			"./style-index": 0
/******/ 		};
/******/ 		
/******/ 		// no chunk on demand loading
/******/ 		
/******/ 		// no prefetching
/******/ 		
/******/ 		// no preloaded
/******/ 		
/******/ 		// no HMR
/******/ 		
/******/ 		// no HMR manifest
/******/ 		
/******/ 		__webpack_require__.O.j = function(chunkId) { return installedChunks[chunkId] === 0; };
/******/ 		
/******/ 		// install a JSONP callback for chunk loading
/******/ 		var webpackJsonpCallback = function(parentChunkLoadingFunction, data) {
/******/ 			var chunkIds = data[0];
/******/ 			var moreModules = data[1];
/******/ 			var runtime = data[2];
/******/ 			// add "moreModules" to the modules object,
/******/ 			// then flag all "chunkIds" as loaded and fire callback
/******/ 			var moduleId, chunkId, i = 0;
/******/ 			if(chunkIds.some(function(id) { return installedChunks[id] !== 0; })) {
/******/ 				for(moduleId in moreModules) {
/******/ 					if(__webpack_require__.o(moreModules, moduleId)) {
/******/ 						__webpack_require__.m[moduleId] = moreModules[moduleId];
/******/ 					}
/******/ 				}
/******/ 				if(runtime) var result = runtime(__webpack_require__);
/******/ 			}
/******/ 			if(parentChunkLoadingFunction) parentChunkLoadingFunction(data);
/******/ 			for(;i < chunkIds.length; i++) {
/******/ 				chunkId = chunkIds[i];
/******/ 				if(__webpack_require__.o(installedChunks, chunkId) && installedChunks[chunkId]) {
/******/ 					installedChunks[chunkId][0]();
/******/ 				}
/******/ 				installedChunks[chunkId] = 0;
/******/ 			}
/******/ 			return __webpack_require__.O(result);
/******/ 		}
/******/ 		
/******/ 		var chunkLoadingGlobal = self["webpackChunkwebduel_theme"] = self["webpackChunkwebduel_theme"] || [];
/******/ 		chunkLoadingGlobal.forEach(webpackJsonpCallback.bind(null, 0));
/******/ 		chunkLoadingGlobal.push = webpackJsonpCallback.bind(null, chunkLoadingGlobal.push.bind(chunkLoadingGlobal));
/******/ 	}();
/******/ 	
/************************************************************************/
/******/ 	
/******/ 	// startup
/******/ 	// Load entry module and return exports
/******/ 	// This entry module depends on other loaded chunks and execution need to be delayed
/******/ 	var __webpack_exports__ = __webpack_require__.O(undefined, ["./style-index"], function() { return __webpack_require__("./src/index.js"); })
/******/ 	__webpack_exports__ = __webpack_require__.O(__webpack_exports__);
/******/ 	
/******/ })()
;
//# sourceMappingURL=index.js.map