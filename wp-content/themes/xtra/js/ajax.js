/* jQuery history */
window.JSON||(window.JSON={}),function(){function f(e){return e<10?"0"+e:e}function quote(e){return escapable.lastIndex=0,escapable.test(e)?'"'+e.replace(escapable,function(e){var t=meta[e];return"string"==typeof t?t:"\\u"+("0000"+e.charCodeAt(0).toString(16)).slice(-4)})+'"':'"'+e+'"'}function str(e,t){var a,r,n,o,i,s=gap,u=t[e];switch(u&&"object"==typeof u&&"function"==typeof u.toJSON&&(u=u.toJSON(e)),"function"==typeof rep&&(u=rep.call(t,e,u)),typeof u){case"string":return quote(u);case"number":return isFinite(u)?String(u):"null";case"boolean":case"null":return String(u);case"object":if(!u)return"null";if(gap+=indent,i=[],"[object Array]"===Object.prototype.toString.apply(u)){for(o=u.length,a=0;a<o;a+=1)i[a]=str(a,u)||"null";return n=0===i.length?"[]":gap?"[\n"+gap+i.join(",\n"+gap)+"\n"+s+"]":"["+i.join(",")+"]",gap=s,n}if(rep&&"object"==typeof rep)for(o=rep.length,a=0;a<o;a+=1)"string"==typeof(r=rep[a])&&(n=str(r,u))&&i.push(quote(r)+(gap?": ":":")+n);else for(r in u)Object.hasOwnProperty.call(u,r)&&(n=str(r,u))&&i.push(quote(r)+(gap?": ":":")+n);return n=0===i.length?"{}":gap?"{\n"+gap+i.join(",\n"+gap)+"\n"+s+"}":"{"+i.join(",")+"}",gap=s,n}}"function"!=typeof Date.prototype.toJSON&&(Date.prototype.toJSON=function(e){return isFinite(this.valueOf())?this.getUTCFullYear()+"-"+f(this.getUTCMonth()+1)+"-"+f(this.getUTCDate())+"T"+f(this.getUTCHours())+":"+f(this.getUTCMinutes())+":"+f(this.getUTCSeconds())+"Z":null},String.prototype.toJSON=Number.prototype.toJSON=Boolean.prototype.toJSON=function(e){return this.valueOf()});var JSON=window.JSON,cx=/[\u0000\u00ad\u0600-\u0604\u070f\u17b4\u17b5\u200c-\u200f\u2028-\u202f\u2060-\u206f\ufeff\ufff0-\uffff]/g,escapable=/[\\\"\x00-\x1f\x7f-\x9f\u00ad\u0600-\u0604\u070f\u17b4\u17b5\u200c-\u200f\u2028-\u202f\u2060-\u206f\ufeff\ufff0-\uffff]/g,gap,indent,meta={"\b":"\\b","\t":"\\t","\n":"\\n","\f":"\\f","\r":"\\r",'"':'\\"',"\\":"\\\\"},rep;"function"!=typeof JSON.stringify&&(JSON.stringify=function(e,t,a){var r;if(gap="",indent="","number"==typeof a)for(r=0;r<a;r+=1)indent+=" ";else"string"==typeof a&&(indent=a);if(rep=t,!t||"function"==typeof t||"object"==typeof t&&"number"==typeof t.length)return str("",{"":e});throw new Error("JSON.stringify")}),"function"!=typeof JSON.parse&&(JSON.parse=function(text,reviver){function walk(e,t){var a,r,n=e[t];if(n&&"object"==typeof n)for(a in n)Object.hasOwnProperty.call(n,a)&&(r=walk(n,a),void 0!==r?n[a]=r:delete n[a]);return reviver.call(e,t,n)}var j;if(text=String(text),cx.lastIndex=0,cx.test(text)&&(text=text.replace(cx,function(e){return"\\u"+("0000"+e.charCodeAt(0).toString(16)).slice(-4)})),/^[\],:{}\s]*$/.test(text.replace(/\\(?:["\\\/bfnrt]|u[0-9a-fA-F]{4})/g,"@").replace(/"[^"\\\n\r]*"|true|false|null|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?/g,"]").replace(/(?:^|:|,)(?:\s*\[)+/g,"")))return j=eval("("+text+")"),"function"==typeof reviver?walk({"":j},""):j;throw new SyntaxError("JSON.parse")})}(),function(e,t){"use strict";var a=e.History=e.History||{},r=e.jQuery;if(void 0!==a.Adapter)throw new Error("History.js Adapter has already been loaded...");a.Adapter={bind:function(e,t,a){r(e).bind(t,a)},trigger:function(e,t,a){r(e).trigger(t,a)},extractEventData:function(e,t,a){return t&&t.originalEvent&&t.originalEvent[e]||a&&a[e]||void 0},onDomLoad:function(e){r(e)}},void 0!==a.init&&a.init()}(window),function(e,t){"use strict";var a=e.document,r=e.setTimeout||r,n=e.clearTimeout||n,o=e.setInterval||o,i=e.History=e.History||{};if(void 0!==i.initHtml4)throw new Error("History.js HTML4 Support has already been loaded...");i.initHtml4=function(){if(void 0!==i.initHtml4.initialized)return!1;i.initHtml4.initialized=!0,i.enabled=!0,i.savedHashes=[],i.isLastHash=function(e){return e===i.getHashByIndex()},i.saveHash=function(e){return!i.isLastHash(e)&&(i.savedHashes.push(e),!0)},i.getHashByIndex=function(e){return void 0===e?i.savedHashes[i.savedHashes.length-1]:e<0?i.savedHashes[i.savedHashes.length+e]:i.savedHashes[e]},i.discardedHashes={},i.discardedStates={},i.discardState=function(e,t,a){var r,n=i.getHashByState(e);return r={discardedState:e,backState:a,forwardState:t},i.discardedStates[n]=r,!0},i.discardHash=function(e,t,a){var r={discardedHash:e,backState:a,forwardState:t};return i.discardedHashes[e]=r,!0},i.discardedState=function(e){var t=i.getHashByState(e);return i.discardedStates[t]||!1},i.discardedHash=function(e){return i.discardedHashes[e]||!1},i.recycleState=function(e){var t=i.getHashByState(e);return i.discardedState(e)&&delete i.discardedStates[t],!0},i.emulated.hashChange&&(i.hashChangeInit=function(){i.checkerFunction=null;var t,r,n,s,u="";return i.isInternetExplorer()?(t="historyjs-iframe",(r=a.createElement("iframe")).setAttribute("id",t),r.style.display="none",a.body.appendChild(r),r.contentWindow.document.open(),r.contentWindow.document.close(),n="",s=!1,i.checkerFunction=function(){if(s)return!1;s=!0;var t=i.getHash()||"",a=i.unescapeHash(r.contentWindow.document.location.hash)||"";return t!==u?(u=t,a!==t&&(n=a=t,r.contentWindow.document.open(),r.contentWindow.document.close(),r.contentWindow.document.location.hash=i.escapeHash(t)),i.Adapter.trigger(e,"hashchange")):a!==n&&(n=a,i.setHash(a,!1)),s=!1,!0}):i.checkerFunction=function(){var t=i.getHash();return t!==u&&(u=t,i.Adapter.trigger(e,"hashchange")),!0},i.intervalList.push(o(i.checkerFunction,i.options.hashChangeInterval)),!0},i.Adapter.onDomLoad(i.hashChangeInit)),i.emulated.pushState&&(i.onHashChange=function(t){var r,n=t&&t.newURL||a.location.href,o=i.getHashByUrl(n),s=null;return i.isLastHash(o)?(i.busy(!1),!1):(i.doubleCheckComplete(),i.saveHash(o),o&&i.isTraditionalAnchor(o)?(i.Adapter.trigger(e,"anchorchange"),i.busy(!1),!1):(s=i.extractState(i.getFullUrl(o||a.location.href,!1),!0),i.isLastSavedState(s)?(i.busy(!1),!1):(i.getHashByState(s),r=i.discardedState(s),r?(i.getHashByIndex(-2)===i.getHashByState(r.forwardState)?i.back(!1):i.forward(!1),!1):(i.pushState(s.data,s.title,s.url,!1),!0))))},i.Adapter.bind(e,"hashchange",i.onHashChange),i.pushState=function(t,r,n,o){if(i.getHashByUrl(n))throw new Error("History.js does not support states with fragement-identifiers (hashes/anchors).");if(!1!==o&&i.busy())return i.pushQueue({scope:i,callback:i.pushState,args:arguments,queue:o}),!1;i.busy(!0);var s=i.createStateObject(t,r,n),u=i.getHashByState(s),l=i.getState(!1),c=i.getHashByState(l),d=i.getHash();return i.storeState(s),i.expectedStateId=s.id,i.recycleState(s),i.setTitle(s),u===c?(i.busy(!1),!1):u!==d&&u!==i.getShortUrl(a.location.href)?(i.setHash(u,!1),!1):(i.saveState(s),i.Adapter.trigger(e,"statechange"),i.busy(!1),!0)},i.replaceState=function(e,t,a,r){if(i.getHashByUrl(a))throw new Error("History.js does not support states with fragement-identifiers (hashes/anchors).");if(!1!==r&&i.busy())return i.pushQueue({scope:i,callback:i.replaceState,args:arguments,queue:r}),!1;i.busy(!0);var n=i.createStateObject(e,t,a),o=i.getState(!1),s=i.getStateByIndex(-2);return i.discardState(o,n,s),i.pushState(n.data,n.title,n.url,!1),!0}),i.emulated.pushState&&i.getHash()&&!i.emulated.hashChange&&i.Adapter.onDomLoad(function(){i.Adapter.trigger(e,"hashchange")})},void 0!==i.init&&i.init()}(window),function(e,t){"use strict";var a=e.console||t,r=e.document,n=e.navigator,o=e.sessionStorage||!1,i=e.setTimeout,s=e.clearTimeout,u=e.setInterval,l=e.clearInterval,c=e.JSON,d=e.alert,h=e.History=e.History||{},p=e.history;if(c.stringify=c.stringify||c.encode,c.parse=c.parse||c.decode,void 0!==h.init)throw new Error("History.js Core has already been loaded...");h.init=function(){return void 0!==h.Adapter&&(void 0!==h.initCore&&h.initCore(),void 0!==h.initHtml4&&h.initHtml4(),!0)},h.initCore=function(){if(void 0!==h.initCore.initialized)return!1;if(h.initCore.initialized=!0,h.options=h.options||{},h.options.hashChangeInterval=h.options.hashChangeInterval||100,h.options.safariPollInterval=h.options.safariPollInterval||500,h.options.doubleCheckInterval=h.options.doubleCheckInterval||500,h.options.storeInterval=h.options.storeInterval||1e3,h.options.busyDelay=h.options.busyDelay||250,h.options.debug=h.options.debug||!1,h.options.initialTitle=h.options.initialTitle||r.title,h.intervalList=[],h.clearAllIntervals=function(){var e,t=h.intervalList;if(void 0!==t&&null!==t){for(e=0;e<t.length;e++)l(t[e]);h.intervalList=null}},h.debug=function(){(h.options.debug||!1)&&h.log.apply(h,arguments)},h.log=function(){var e,t,n,o,i,s=void 0!==a&&void 0!==a.log&&void 0!==a.log.apply,u=r.getElementById("log");for(s?(o=Array.prototype.slice.call(arguments),e=o.shift(),void 0!==a.debug?a.debug.apply(a,[e,o]):a.log.apply(a,[e,o])):e="\n"+arguments[0]+"\n",t=1,n=arguments.length;t<n;++t){if("object"==typeof(i=arguments[t])&&void 0!==c)try{i=c.stringify(i)}catch(e){}e+="\n"+i+"\n"}return u?(u.value+=e+"\n-----\n",u.scrollTop=u.scrollHeight-u.clientHeight):s||d(e),!0},h.getInternetExplorerMajorVersion=function(){return h.getInternetExplorerMajorVersion.cached=void 0!==h.getInternetExplorerMajorVersion.cached?h.getInternetExplorerMajorVersion.cached:function(){for(var e=3,t=r.createElement("div"),a=t.getElementsByTagName("i");(t.innerHTML="\x3c!--[if gt IE "+ ++e+"]><i></i><![endif]--\x3e")&&a[0];);return e>4&&e}()},h.isInternetExplorer=function(){return h.isInternetExplorer.cached=void 0!==h.isInternetExplorer.cached?h.isInternetExplorer.cached:Boolean(h.getInternetExplorerMajorVersion())},h.emulated={pushState:!Boolean(e.history&&e.history.pushState&&e.history.replaceState&&!/ Mobile\/([1-7][a-z]|(8([abcde]|f(1[0-8]))))/i.test(n.userAgent)&&!/AppleWebKit\/5([0-2]|3[0-2])/i.test(n.userAgent)),hashChange:Boolean(!("onhashchange"in e||"onhashchange"in r)||h.isInternetExplorer()&&h.getInternetExplorerMajorVersion()<8)},h.enabled=!h.emulated.pushState,h.bugs={setHash:Boolean(!h.emulated.pushState&&"Apple Computer, Inc."===n.vendor&&/AppleWebKit\/5([0-2]|3[0-3])/.test(n.userAgent)),safariPoll:Boolean(!h.emulated.pushState&&"Apple Computer, Inc."===n.vendor&&/AppleWebKit\/5([0-2]|3[0-3])/.test(n.userAgent)),ieDoubleCheck:Boolean(h.isInternetExplorer()&&h.getInternetExplorerMajorVersion()<8),hashEscape:Boolean(h.isInternetExplorer()&&h.getInternetExplorerMajorVersion()<7)},h.isEmptyObject=function(e){for(var t in e)return!1;return!0},h.cloneObject=function(e){var t,a;return e?(t=c.stringify(e),a=c.parse(t)):a={},a},h.getRootUrl=function(){var e=r.location.protocol+"//"+(r.location.hostname||r.location.host);return r.location.port&&(e+=":"+r.location.port),e+="/"},h.getBaseHref=function(){var e=r.getElementsByTagName("base"),t=null,a="";return 1===e.length&&(t=e[0],a=t.href.replace(/[^\/]+$/,"")),(a=a.replace(/\/+$/,""))&&(a+="/"),a},h.getBaseUrl=function(){return h.getBaseHref()||h.getBasePageUrl()||h.getRootUrl()},h.getPageUrl=function(){return((h.getState(!1,!1)||{}).url||r.location.href).replace(/\/+$/,"").replace(/[^\/]+$/,function(e,t,a){return/\./.test(e)?e:e+"/"})},h.getBasePageUrl=function(){return r.location.href.replace(/[#\?].*/,"").replace(/[^\/]+$/,function(e,t,a){return/[^\/]$/.test(e)?"":e}).replace(/\/+$/,"")+"/"},h.getFullUrl=function(e,t){var a=e,r=e.substring(0,1);return t=void 0===t||t,/[a-z]+\:\/\//.test(e)||(a="/"===r?h.getRootUrl()+e.replace(/^\/+/,""):"#"===r?h.getPageUrl().replace(/#.*/,"")+e:"?"===r?h.getPageUrl().replace(/[\?#].*/,"")+e:t?h.getBaseUrl()+e.replace(/^(\.\/)+/,""):h.getBasePageUrl()+e.replace(/^(\.\/)+/,"")),a.replace(/\#$/,"")},h.getShortUrl=function(e){var t=e,a=h.getBaseUrl(),r=h.getRootUrl();return h.emulated.pushState&&(t=t.replace(a,"")),t=t.replace(r,"/"),h.isTraditionalAnchor(t)&&(t="./"+t),t=t.replace(/^(\.\/)+/g,"./").replace(/\#$/,"")},h.store={},h.idToState=h.idToState||{},h.stateToId=h.stateToId||{},h.urlToId=h.urlToId||{},h.storedStates=h.storedStates||[],h.savedStates=h.savedStates||[],h.normalizeStore=function(){h.store.idToState=h.store.idToState||{},h.store.urlToId=h.store.urlToId||{},h.store.stateToId=h.store.stateToId||{}},h.getState=function(e,t){void 0===e&&(e=!0),void 0===t&&(t=!0);var a=h.getLastSavedState();return!a&&t&&(a=h.createStateObject()),e&&(a=h.cloneObject(a),a.url=a.cleanUrl||a.url),a},h.getIdByState=function(e){var t,a=h.extractId(e.url);if(!a)if(t=h.getStateString(e),void 0!==h.stateToId[t])a=h.stateToId[t];else if(void 0!==h.store.stateToId[t])a=h.store.stateToId[t];else{for(;a=(new Date).getTime()+String(Math.random()).replace(/\D/g,""),void 0!==h.idToState[a]||void 0!==h.store.idToState[a];);h.stateToId[t]=a,h.idToState[a]=e}return a},h.normalizeState=function(e){var t,a;return e&&"object"==typeof e||(e={}),void 0!==e.normalized?e:(e.data&&"object"==typeof e.data||(e.data={}),t={},t.normalized=!0,t.title=e.title||"",t.url=h.getFullUrl(h.unescapeString(e.url||r.location.href)),t.hash=h.getShortUrl(t.url),t.data=h.cloneObject(e.data),t.id=h.getIdByState(t),t.cleanUrl=t.url.replace(/\??\&_suid.*/,""),t.url=t.cleanUrl,a=!h.isEmptyObject(t.data),(t.title||a)&&(t.hash=h.getShortUrl(t.url).replace(/\??\&_suid.*/,""),/\?/.test(t.hash)||(t.hash+="?"),t.hash+="&_suid="+t.id),t.hashedUrl=h.getFullUrl(t.hash),(h.emulated.pushState||h.bugs.safariPoll)&&h.hasUrlDuplicate(t)&&(t.url=t.hashedUrl),t)},h.createStateObject=function(e,t,a){var r={data:e,title:t,url:a};return r=h.normalizeState(r)},h.getStateById=function(e){return e=String(e),h.idToState[e]||h.store.idToState[e]||t},h.getStateString=function(e){var t,a;return t=h.normalizeState(e),a={data:t.data,title:e.title,url:e.url},c.stringify(a)},h.getStateId=function(e){var t;return t=h.normalizeState(e),t.id},h.getHashByState=function(e){var t;return t=h.normalizeState(e),t.hash},h.extractId=function(e){var t;return t=/(.*)\&_suid=([0-9]+)$/.exec(e),t?t[1]||e:e,(t?String(t[2]||""):"")||!1},h.isTraditionalAnchor=function(e){return!/[\/\?\.]/.test(e)},h.extractState=function(e,t){var a,r,n=null;return t=t||!1,(a=h.extractId(e))&&(n=h.getStateById(a)),n||(r=h.getFullUrl(e),(a=h.getIdByUrl(r)||!1)&&(n=h.getStateById(a)),!n&&t&&!h.isTraditionalAnchor(e)&&(n=h.createStateObject(null,null,r))),n},h.getIdByUrl=function(e){return h.urlToId[e]||h.store.urlToId[e]||t},h.getLastSavedState=function(){return h.savedStates[h.savedStates.length-1]||t},h.getLastStoredState=function(){return h.storedStates[h.storedStates.length-1]||t},h.hasUrlDuplicate=function(e){var t;return t=h.extractState(e.url),t&&t.id!==e.id},h.storeState=function(e){return h.urlToId[e.url]=e.id,h.storedStates.push(h.cloneObject(e)),e},h.isLastSavedState=function(e){var t,a,r,n=!1;return h.savedStates.length&&(t=e.id,a=h.getLastSavedState(),r=a.id,n=t===r),n},h.saveState=function(e){return!h.isLastSavedState(e)&&(h.savedStates.push(h.cloneObject(e)),!0)},h.getStateByIndex=function(e){return void 0===e?h.savedStates[h.savedStates.length-1]:e<0?h.savedStates[h.savedStates.length+e]:h.savedStates[e]},h.getHash=function(){return h.unescapeHash(r.location.hash)},h.unescapeString=function(t){for(var a,r=t;(a=e.unescape(r))!==r;)r=a;return r},h.unescapeHash=function(e){var t=h.normalizeHash(e);return t=h.unescapeString(t)},h.normalizeHash=function(e){return e.replace(/[^#]*#/,"").replace(/#.*/,"")},h.setHash=function(e,t){var a,n,o;return!1!==t&&h.busy()?(h.pushQueue({scope:h,callback:h.setHash,args:arguments,queue:t}),!1):(a=h.escapeHash(e),h.busy(!0),n=h.extractState(e,!0),n&&!h.emulated.pushState?h.pushState(n.data,n.title,n.url,!1):r.location.hash!==a&&(h.bugs.setHash?(o=h.getPageUrl(),h.pushState(null,null,o+"#"+a,!1)):r.location.hash=a),h)},h.escapeHash=function(t){var a=h.normalizeHash(t);return a=e.escape(a),h.bugs.hashEscape||(a=a.replace(/\%21/g,"!").replace(/\%26/g,"&").replace(/\%3D/g,"=").replace(/\%3F/g,"?")),a},h.getHashByUrl=function(e){var t=String(e).replace(/([^#]*)#?([^#]*)#?(.*)/,"$2");return t=h.unescapeHash(t)},h.setTitle=function(e){var t,a=e.title;a||(t=h.getStateByIndex(0))&&t.url===e.url&&(a=t.title||h.options.initialTitle);try{r.getElementsByTagName("title")[0].innerHTML=a.replace("<","&lt;").replace(">","&gt;").replace(" & "," &amp; ")}catch(e){}return r.title=a,h},h.queues=[],h.busy=function(e){if(void 0!==e?h.busy.flag=e:void 0===h.busy.flag&&(h.busy.flag=!1),!h.busy.flag){s(h.busy.timeout);var t=function(){var e,a,r;if(!h.busy.flag)for(e=h.queues.length-1;e>=0;--e)0!==(a=h.queues[e]).length&&(r=a.shift(),h.fireQueueItem(r),h.busy.timeout=i(t,h.options.busyDelay))};h.busy.timeout=i(t,h.options.busyDelay)}return h.busy.flag},h.busy.flag=!1,h.fireQueueItem=function(e){return e.callback.apply(e.scope||h,e.args||[])},h.pushQueue=function(e){return h.queues[e.queue||0]=h.queues[e.queue||0]||[],h.queues[e.queue||0].push(e),h},h.queue=function(e,t){return"function"==typeof e&&(e={callback:e}),void 0!==t&&(e.queue=t),h.busy()?h.pushQueue(e):h.fireQueueItem(e),h},h.clearQueue=function(){return h.busy.flag=!1,h.queues=[],h},h.stateChanged=!1,h.doubleChecker=!1,h.doubleCheckComplete=function(){return h.stateChanged=!0,h.doubleCheckClear(),h},h.doubleCheckClear=function(){return h.doubleChecker&&(s(h.doubleChecker),h.doubleChecker=!1),h},h.doubleCheck=function(e){return h.stateChanged=!1,h.doubleCheckClear(),h.bugs.ieDoubleCheck&&(h.doubleChecker=i(function(){return h.doubleCheckClear(),h.stateChanged||e(),!0},h.options.doubleCheckInterval)),h},h.safariStatePoll=function(){var t=h.extractState(r.location.href);if(!h.isLastSavedState(t))return t||h.createStateObject(),h.Adapter.trigger(e,"popstate"),h},h.back=function(e){return!1!==e&&h.busy()?(h.pushQueue({scope:h,callback:h.back,args:arguments,queue:e}),!1):(h.busy(!0),h.doubleCheck(function(){h.back(!1)}),p.go(-1),!0)},h.forward=function(e){return!1!==e&&h.busy()?(h.pushQueue({scope:h,callback:h.forward,args:arguments,queue:e}),!1):(h.busy(!0),h.doubleCheck(function(){h.forward(!1)}),p.go(1),!0)},h.go=function(e,t){var a;if(e>0)for(a=1;a<=e;++a)h.forward(t);else{if(!(e<0))throw new Error("History.go: History.go requires a positive or negative integer passed.");for(a=-1;a>=e;--a)h.back(t)}return h},h.emulated.pushState){var f=function(){};h.pushState=h.pushState||f,h.replaceState=h.replaceState||f}else h.onPopState=function(t,a){var n,o,i=!1,s=!1;return h.doubleCheckComplete(),n=h.getHash(),n?(o=h.extractState(n||r.location.href,!0),o?h.replaceState(o.data,o.title,o.url,!1):(h.Adapter.trigger(e,"anchorchange"),h.busy(!1)),h.expectedStateId=!1,!1):(i=h.Adapter.extractEventData("state",t,a)||!1,(s=i?h.getStateById(i):h.expectedStateId?h.getStateById(h.expectedStateId):h.extractState(r.location.href))||(s=h.createStateObject(null,null,r.location.href)),h.expectedStateId=!1,h.isLastSavedState(s)?(h.busy(!1),!1):(h.storeState(s),h.saveState(s),h.setTitle(s),h.Adapter.trigger(e,"statechange"),h.busy(!1),!0))},h.Adapter.bind(e,"popstate",h.onPopState),h.pushState=function(t,a,r,n){if(h.getHashByUrl(r)&&h.emulated.pushState)throw new Error("History.js does not support states with fragement-identifiers (hashes/anchors).");if(!1!==n&&h.busy())return h.pushQueue({scope:h,callback:h.pushState,args:arguments,queue:n}),!1;h.busy(!0);var o=h.createStateObject(t,a,r);return h.isLastSavedState(o)?h.busy(!1):(h.storeState(o),h.expectedStateId=o.id,p.pushState(o.id,o.title,o.url),h.Adapter.trigger(e,"popstate")),!0},h.replaceState=function(t,a,r,n){if(h.getHashByUrl(r)&&h.emulated.pushState)throw new Error("History.js does not support states with fragement-identifiers (hashes/anchors).");if(!1!==n&&h.busy())return h.pushQueue({scope:h,callback:h.replaceState,args:arguments,queue:n}),!1;h.busy(!0);var o=h.createStateObject(t,a,r);return h.isLastSavedState(o)?h.busy(!1):(h.storeState(o),h.expectedStateId=o.id,p.replaceState(o.id,o.title,o.url),h.Adapter.trigger(e,"popstate")),!0};if(o){try{h.store=c.parse(o.getItem("History.store"))||{}}catch(e){h.store={}}h.normalizeStore()}else h.store={},h.normalizeStore();h.Adapter.bind(e,"beforeunload",h.clearAllIntervals),h.Adapter.bind(e,"unload",h.clearAllIntervals),h.saveState(h.storeState(h.extractState(r.location.href,!0))),o&&(h.onUnload=function(){var e,t;try{e=c.parse(o.getItem("History.store"))||{}}catch(t){e={}}e.idToState=e.idToState||{},e.urlToId=e.urlToId||{},e.stateToId=e.stateToId||{};for(t in h.idToState)h.idToState.hasOwnProperty(t)&&(e.idToState[t]=h.idToState[t]);for(t in h.urlToId)h.urlToId.hasOwnProperty(t)&&(e.urlToId[t]=h.urlToId[t]);for(t in h.stateToId)h.stateToId.hasOwnProperty(t)&&(e.stateToId[t]=h.stateToId[t]);h.store=e,h.normalizeStore(),o.setItem("History.store",c.stringify(e))},h.intervalList.push(u(h.onUnload,h.options.storeInterval)),h.Adapter.bind(e,"beforeunload",h.onUnload),h.Adapter.bind(e,"unload",h.onUnload)),h.emulated.pushState||(h.bugs.safariPoll&&h.intervalList.push(u(h.safariStatePoll,h.options.safariPollInterval)),"Apple Computer, Inc."!==n.vendor&&"Mozilla"!==(n.appCodeName||"")||(h.Adapter.bind(e,"hashchange",function(){h.Adapter.trigger(e,"popstate")}),h.getHash()&&h.Adapter.onDomLoad(function(){h.Adapter.trigger(e,"hashchange")})))},h.init()}(window)

/**
 * 
 * Codevz Ajax 1.0.3 - 10 July, 2018
 * @copyright: http://Codevz.com/
 * 
 */
!function( $, window, document, undefined ) {
	'use strict';

	$( document ).ready(function() {

		var History = window.History;

		if ( !History.enabled || $( window.parent.document.body ).hasClass( 'vc_editor' ) ) {
			return false;
		}

		/* cz_ajax */
		$.fn.cz_ajax = function() {

			var $t = $( this );

			// Exclude from AJAX
			// .not( '[href*="' + window.location.host + '"]' )
			$( '[href^="mailto:"],.rev_slider_wrapper a,' +
				'[href*="wp-login"],[id^="wpadminb"] a,[href*="wp-admin"],' +
				'.add_to_cart_button,.cart_list .remove,a[target="_blank"],' +
				'[href*="#"],[href*=".jpg"],[href*=".png"],[href*=".mp3"],' +
				'[href*=".gif"],[href*=".jpeg"],[href*=".JPG"],[href*=".avi"],' +
				'[href*=".zip"],[href*=".rar"],[href*=".mp4"],[data-rel^="prettyPhoto"],' +
				'.comment-reply-link' ).addClass( 'no-ajax' );

			$t.on('click', 'a:not(.no-ajax)', function( e ) {
				var $t = $(this),
					url = $t.attr('href'),
					title = $t.attr('title') || null;

				/* Continue as normal for cmd clicks etc */
				if ( e.which == 2 || e.metaKey ) { return true; }

				History.pushState( null, title, url );
				e.preventDefault();

				$( 'html' ).animate({scrollTop: 0}, 500, 'swing');

				return false;
			});

			/* Demo purpose */
			if ( window.location.search.indexOf( 'ajax' ) > -1 ) {
				$( '#layout a[href*="' + window.location.hostname + '"]:not(.no-ajax)' ).each(function() {
					var href = $(this).attr('href');
					if ( href ) {
						href = href.replace(/\?.+/, '');
						href += ( href.match(/\?/) ? '&' : '?' ) + 'ajax';
						$(this).attr('href', href);
					}
				});
			}

			/* Ajax add comment */
			if ( $('.single').length ) {
				var commentform = $('#commentform');
				commentform.prepend('<div id="comment-status"></div>');
				var statusdiv = $('#comment-status');
				commentform.submit(function(){
					var formdata = commentform.serialize();
					statusdiv.html('<pre>...</pre>');
					var formurl = commentform.attr('action');
					$.ajax({
						type: 'post',
						url: formurl,
						data: formdata,
						error: function(XMLHttpRequest, textStatus, errorThrown){
							statusdiv.html('<pre>You might have left one of the fields blank, or be posting too quickly</pre>');
						},
						success: function(data, textStatus) {
							if ( data ) {
								statusdiv.html('<pre>Thanks for your comment. We will review and approve it soon.</pre>');
								commentform.find('textarea[name=comment]').val('');
							}
						}
					});

					return false;
				});
			}

			return $t;
		};
		$( 'body' ).cz_ajax();

		/* Ajax Prepare */
		var selector 	= '#page_content',
			$selector 	= $( selector ),
			loader 		= $( '.cz_ajax_loader' ),
			rootUrl 	= History.getRootUrl();

		/* Hook into StateChange */
		$(window).bind('statechange',function(){

			var State 	= History.getState(),
				url 	= State.url,
				relativeUrl = url.replace( rootUrl, '' );

			$selector.animate( { opacity: 0 }, 500 );
			$('.page_cover ').animate( { opacity: 0 }, 1000 );
			loader.fadeIn();

			$.ajax({
				url: url,
				cache: false,
				success: function( data, textStatus, jqXHR ) {

					var $data 		= $( data ),
						Scripts 	= $data.filter( 'script' ),
						Styles 		= $data.filter( 'link[rel="stylesheet"]' ),
						inStyles 	= $data.filter( 'style' ),
						found 		= false;

					/* Fetch content */
					if ( ! $data ) {
						console.log( 'Success but no data found' );
						document.location.href = url;
						return false;
					}

					/* Scripts */
					if ( ! $( '#inline_js' ).length ) {
						$( 'body' ).append( '<div id="inline_js"></div>' );
					} else {
						$( '#inline_js' ).html('');
					}
					Scripts.each(function(i){
						var script 	= $( Scripts[i].outerHTML ),
							src 	= script.attr('src');

						if ( src ) {
							$( 'script[src]' ).each(function(){
								if ( $(this).attr('src') === src ) {
									found = true;
									return found;
								}
							});
							if ( found === false ) {

								var newScript;
								newScript = document.createElement('script');
								newScript.type = 'text/javascript';
								newScript.src = src;
								document.getElementsByTagName("body")[0].appendChild(newScript);

							} else if ( src.match( /(js_composer|custom.js|codevzplus.js|grid.js|slick.js|modernizer.js|tooltips.js|animated_text.js|360_degree.js|woocommerce|addthis|ratings)/ ) ) {
								if ( window.addthis ) {
									window.addthis = null;
								}

								$( 'script[src="' + src + '"]' ).remove();
								var newScript;
								newScript = document.createElement( 'script' );
								newScript.type = 'text/javascript';
								newScript.src = src;
								document.getElementsByTagName("body")[0].appendChild( newScript );
							}
							found = false;
						} else { 
							$( '#inline_js' ).append( script );
						}
					});

					/* Fix plugins */
					setTimeout(function(){
						if ( typeof embedVars !== 'undefined' && typeof DISQUS !== 'undefined' ) {
							$( '#dsq-content' ).remove();
							DISQUS.reset({
								reload: true,
								config: function () {  
									this.page.identifier = embedVars.disqusIdentifier;  
									this.page.url = embedVars.disqusUrl + '#!newthread';
								}
							});
						}

						if ( typeof a2a !== 'undefined' ) {
							a2a.init('page');
						}
					}, 4000 );

					/* Styles */
					Styles.each(function(i){
						var style 	= $( Styles[i].outerHTML ),
							href 	= style.attr('href');

						if ( href ) {
							$( 'link[rel="stylesheet"]' ).each(function(){
								if ( $(this).attr('href') === href ) {
									found = true;
									return found;
								}
							});
							if ( found === false ) {
								$( style ).insertBefore( '#codevz-plugin-css' );
							}
							found = false;
						}
					});

					/* inline Styles */
					$( '[data-type="vc_custom-css"], #codevz-plugin-inline-css' ).detach();
					inStyles.each(function(i){
						$( 'style' ).each(function(){
							if ( $( this ).html() === $( inStyles[i] ).html() ) {
								found = true;
								return found;
							}
						});
						if ( found === false ) {
							$( 'head' ).append( $( inStyles[i].outerHTML ) );
						}
						found = false;
					});

					/* HTML Classes */
					var matches = data.match(/<html.*class=["']([^"']*)["'].*>/);
					$( 'html' ).removeClass().addClass( matches && matches[1] );

					/* Body Classes */
					var matches = data.match(/<body.*class=["']([^"']*)["'].*>/);
					$( 'body' ).removeClass().addClass( matches && matches[1] );

					/* HF */
					$( 'header' ).html( $data.find( 'header' ).html() );
					$( 'footer' ).html( $data.find( 'footer' ).html() );

					/* Cover */
					$( '.page_cover' ).detach();
					$( $( '.page_cover', $data ) ).insertBefore( selector );

					/* Content */
					$selector.removeClass().attr( 'class', $data.find( selector ).attr( 'class' ) ).html( $data.find( selector ).html() ).animate( { opacity: 1 }, 500 );
					$( '.page_cover ' ).animate( { opacity: 1 }, 1000 );
					$( '.cz_ajax_loader' ).fadeOut();

					/* Title */
					try {
						document.getElementsByTagName('title')[0].innerHTML = $data.filter( 'title' ).html();
					} catch (e) {}

					/* Complete changes */
					$(window).trigger( 'statechangecomplete' );

					/* Inform Google Analytics of the change */
					if ( typeof _gaq !== 'undefined' ) {
						_gaq.push(['_trackPageview', relativeUrl]);
						console.log('Google analytics push', relativeUrl );
					}
					if ( typeof ga !== 'undefined' ) {
						ga('set', 'page', relativeUrl );
						ga('send', 'pageview', {
							'page': relativeUrl,
							'title': $data.filter( 'title' ).html()
						});
						//console.log('Google analytics set', relativeUrl );
					}

					/* Inform ReInvigorate of a state change */
					if ( typeof reinvigorate !== 'undefined' && typeof reinvigorate.ajax_track !== 'undefined' ) {
						reinvigorate.ajax_track(url);
					}

					/* cz_ajax new links */
					$( window ).scroll().resize();
					$( 'body' ).cz_ajax().addClass( 'ajax_loaded' );
				},
				error: function( jqXHR, textStatus, errorThrown ) {
					console.log( 'errorThrown_CD_AJAX' );
					console.log( errorThrown );
					document.location.href = url;
					return false;
				}

			}); /* ajax */

		}); /* onStateChange */

	}); /* Ready */

}( jQuery,window,document );