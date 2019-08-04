(function() {
    var ChargeBee = {};

    ChargeBee._env = {
        protocol: 'https',
        hostSuffix: '.chargebee.com',
        apiPath: '/api/js/v1',
        timeout: 40000
    };

    ChargeBee.configure = function(conf) {
        ChargeBee._util.extend(true, ChargeBee._env, conf);
    };

    ChargeBee._endpoints = {
        'subscription': [['create', 'POST', '/subscriptions', null, false]],
        'estimate': [['create_subscription', 'POST', '/estimates', '/create_subscription', false], ['update_subscription', 'POST', '/estimates', '/update_subscription', false]],
        'plan': [['list', 'GET', '/plans', null, false], ['retrieve', 'GET', '/plans', null, true]],
        'addon': [['list', 'GET', '/addons', null, false], ['retrieve', 'GET', '/addons', null, true]]
    };

    function createApiFunc(apiCall) {
        return function() {
            return new RequestWrapper(arguments, apiCall);
        };
    };

    function RequestWrapper(args, apiCall) {
        this.args = args;
        this.apiCall = apiCall;
        if(this.apiCall[4]){
           validateIdParam(this.args[0]);
        }
    };

    RequestWrapper.prototype.request = function(callBack) {
            var urlIdParam = this.apiCall[4] ? this.args[0] : null;
            var params = this.apiCall[4] ? this.args[1] : this.args[0];
            if (!ChargeBee._util.isFunction(callBack)) {
                throw new Error(('The callback parameter passed is incorrect.'));
            }
            return ChargeBee.core.ajax(callBack, this.apiCall[1], this.apiCall[2], this.apiCall[3], urlIdParam, params);
    };

    function validateIdParam(idParam) {
        if (typeof idParam === 'undefined' || typeof idParam !== 'string') {
            throw new Error('the required id parameter missing or wrong');
        }
        return idParam;
    }

    (function() {
        for (var res in ChargeBee._endpoints) {
            ChargeBee[res] = {};
            var apiCalls = ChargeBee._endpoints[res];
            for (var apiIdx = 0; apiIdx < apiCalls.length; apiIdx++) {
                var apiCall = apiCalls[apiIdx];
                ChargeBee[res][apiCall[0]] = createApiFunc(apiCall);
            }
        }
    })();

    ChargeBee.core = (function() {
        var coreRef = {};
        var requestId = 0;

        coreRef.ajax = function(callBack, httpMethod, urlPrefix, urlSuffix, urlIdParam, params) {
            if (typeof params === 'undefined' || params === null) {
                params = {};
            }
            params._jsapi_key = ChargeBee._env.api_key;
            params._jsapi_method = httpMethod;
            return jsonpAjax({
                url: getApiURL(urlPrefix, urlSuffix, urlIdParam),
                data: params,
                callBack: callBack,
                timeout: ChargeBee._env.timeout
            });
        };

        function clearScript(refs) {
            clearTimeout(refs.abortTimeout);
            if (refs.scriptTag.parentNode !== null) {
                refs.scriptTag.parentNode.removeChild(refs.scriptTag);
            }
            if (typeof window[refs.jsonpName] !== 'undefined') {
                try {
                    delete window[refs.jsonpName];
                } catch (e) {
                    window[refs.jsonpName] = void 0;
                }
            }
        }

        function requestAborted(refs, httpCode, errorCode, message) {
            clearScript(refs);
            throwError(refs.options.callBack, httpCode, errorCode, message);//Has to be fixed
        }

        var jsonpAjax = function(options) {
            var refs = {options: options};
            refs.jsonpName = 'cbjsonp_' + new Date().getTime() + '_' + (requestId++);
            refs.scriptTag = document.createElement('script');
            refs.scriptTag.onerror = function() {
                requestAborted(refs, 500, 'js_scripttag_onerror', " 'onerror' invoked when trying to call the api via script tag");
            };
            window[refs.jsonpName] = function(response, status) {
                clearScript(refs);
                return options.callBack(status, response);
            };
            options.data._jsapi_callback = refs.jsonpName;
            refs.scriptTag.src = options.url + '?' + encodeParams(options.data);
            var headTag = document.getElementsByTagName('head')[0];//Throw error if headTag not present.
            if (typeof headTag === 'undefined') {
                throw new Error('the head tag is not defined.');
            }
            headTag.appendChild(refs.scriptTag);
            if (options.timeout > 0) {
                refs.abortTimeout = setTimeout(function() {
                    requestAborted(refs, 504, 'js_timeout', 'request timed out.');
                }, options.timeout);
            }
        };

        function getApiURL(urlPrefix, urlSuffix, urlIdParam) {
            var ce = ChargeBee._env;
            if (typeof ce.api_key === 'undefined' || typeof ce.site === 'undefined') {
                throw new Error('Your site or api key is not configured.');
            }
            return ce.protocol + "://" + ce.site + ce.hostSuffix + ce.apiPath + urlPrefix + (urlIdParam !== null ? //
                    '/' + encodeURIComponent(urlIdParam) : '') + (urlSuffix !== null ? urlSuffix : '');
        }


        var encodeParams = function(paramObj, serialized, scope, index) {
            var key, value;
            if (typeof serialized === 'undefined' || serialized === null) {
                serialized = [];
            }
            for (key in paramObj) {
                value = paramObj[key];
                if (scope) {
                    key = "" + scope + "[" + key + "]";
                }
                if (typeof index !== 'undefined' && index !== null) {
                    key = key + "[" + index + "]";
                }

                if (ChargeBee._util.isArray(value)) {
                    for (var arrIdx = 0; arrIdx < value.length; arrIdx++) {
                        if (typeof value[arrIdx] === 'object' || ChargeBee._util.isArray(value[arrIdx])) {
                            encodeParams(value[arrIdx], serialized, key, arrIdx);
                        } else {
                            if (typeof value[arrIdx] !== 'undefined' && ChargeBee._util.trim(value[arrIdx]) !== '') {
                                serialized.push("" + key + "=" + (encodeURIComponent(value[arrIdx])));
                            }
                        }
                    }
                } else if (typeof value === 'object' && !ChargeBee._util.isArray(value)) {
                    encodeParams(value, serialized, key);
                } else {
                    if (typeof value !== 'undefined' && ChargeBee._util.trim(value) !== '') {
                        serialized.push("" + key + "=" + (encodeURIComponent(value)));
                    }
                }
            }
            return serialized.join('&').replace(/%20/g, '+');
        };
        var throwError = function(callBack, httpCode, errorCode, message) {
            var error = {
                'http_code': httpCode,
                'error_code': errorCode,
                'message': message
            };
            return callBack(httpCode, error);
        };

        return coreRef;
    }).call(this);

    ChargeBee._util = (function() {
        var util = {};

        util.extend = function(deep, target, copy) {
            util.extendsFn.call(this, deep, target, copy);
        };

        util.extendsFn = function() {
            var options, name, src, copy, copyIsArray, clone,
                    target = arguments[0] || {},
                    i = 1,
                    length = arguments.length,
                    deep = false;
            if (typeof target === "boolean") {
                deep = target;
                target = arguments[1] || {};
                i = 2;
            }
            if (typeof target !== "object" && typeof target !== "function") {
                target = {};
            }
            if (length === i) {
                target = this;
                --i;
            }
            for (; i < length; i++) {
                if ((options = arguments[ i ]) !== null) {
                    for (name in options) {
                        src = target[ name ];
                        copy = options[ name ];

                        if (target === copy) {
                            continue;
                        }
                        if (deep && copy && (typeof copy === 'object' || (copyIsArray = (util.isArray(copy))))) {
                            if (copyIsArray) {
                                copyIsArray = false;
                                clone = src && util.isArray(src) ? src : [];
                            } else {
                                clone = src && typeof src === 'object' ? src : {};
                            }
                            target[ name ] = util.extend.call(this, deep, clone, copy);
                        } else if (copy !== undefined) {
                            target[ name ] = copy;
                        }
                    }
                }
            }
            return target;
        };

        util.indexOf = function(array, item) {
            if (![].indexOf()) {
                for (var i = 0; i < array.length; i++) {
                    if (array[i] === item) {
                        return i;
                    }
                }
                return -1;
            } else {
                return array.indexOf(item);
            }
        };

        util.trim = function(str) {
            return str !== '' ? str : str.replace(/^\s+|\s+$/g, '');
        };

        util.isEmptyObject = function(obj) {
            var name;
            for (name in obj) {
                return false;
            }
            return true;
        };

        util.isNotUndefinedNEmpty = function(obj) {
            if (typeof obj !== 'undefined' && !util.isEmptyObject(obj)) {
                return true;
            }
            return false;
        };

        util.isArray = Array.isArray || function(obj) {
            return  Object.prototype.toString.call(obj) === '[object Array]';
        };

        util.isFunction = function(obj) {
            return typeof obj === 'function';
        };

        return util;
    }).call(this);

    window.ChargeBee = ChargeBee;
}).call(this);
