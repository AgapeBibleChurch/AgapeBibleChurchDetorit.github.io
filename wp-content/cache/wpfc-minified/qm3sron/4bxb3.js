// source --> //localhost/wordpress/wp-content/plugins/a3-lazy-load/assets/js/jquery.lazyloadxt.min.js?ver=1.8.9 
(function(e,t,n,r){function w(e,t){return e[t]===r?l[t]:e[t]}function E(){var e=t.pageYOffset;return e===r?a.scrollTop:e}function S(e,t){var n=l["on"+e];if(n){if(p(n)){n.call(t[0])}else{if(n.addClass){t.addClass(n.addClass)}if(n.removeClass){t.removeClass(n.removeClass)}}}t.trigger("lazy"+e,[t]);C()}function x(t){S(t.type,e(this).off(o,x))}function T(n){if(!g.length){return}n=n||l.forceLoad;y=Infinity;var r=E(),s=t.innerHeight||a.clientHeight,u=t.innerWidth||a.clientWidth,f,c;for(f=0,c=g.length;f<c;f++){var h=g[f],d=h[0],v=h[i],b=false,w=n,T;if(!m(a,d)){b=true}else if(n||!v.visibleOnly||d.offsetWidth||d.offsetHeight){if(!w){var N=d.getBoundingClientRect(),C=v.edgeX,k=v.edgeY;T=N.top+r-k-s;w=T<=r&&N.bottom>-k&&N.left<=u+C&&N.right>-C}if(w){S("show",h);var L=v.srcAttr,A=p(L)?L(h):d.getAttribute(L);if(A){h.on(o,x);d.src=A}b=true}else{if(T<y){y=T}}}if(b){g.splice(f--,1);c--}}if(!c){S("complete",e(a))}}function N(){if(b>1){b=1;T();setTimeout(N,l.throttle)}else{b=0}}function C(e){if(!g.length){return}if(e&&e.type==="scroll"&&e.currentTarget===t){if(y>=E()){return}}if(!b){setTimeout(N,0)}b=2}function k(){h.lazyLoadXT()}function L(){T(true)}var i="lazyLoadXT",s="lazied",o="load error",u="lazy-hidden",a=n.documentElement||n.body,f=t.onscroll===r||!!t.operamini||!a.getBoundingClientRect,l={autoInit:true,selector:"img[data-src]",blankImage:"data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7",throttle:99,forceLoad:f,loadEvent:"pageshow",updateEvent:"load orientationchange resize scroll touchmove focus",forceEvent:"",oninit:{removeClass:"lazy"},onshow:{addClass:u},onload:{removeClass:u,addClass:"lazy-loaded"},onerror:{removeClass:u},checkDuplicates:true},c={srcAttr:"data-src",edgeX:0,edgeY:0,visibleOnly:true},h=e(t),p=e.isFunction,d=e.extend,v=e.data||function(t,n){return e(t).data(n)},m=e.contains||function(e,t){while(t=t.parentNode){if(t===e){return true}}return false},g=[],y=0,b=0;e[i]=d(l,c,e[i]);e.fn[i]=function(n){n=n||{};var r=w(n,"blankImage"),o=w(n,"checkDuplicates"),u=w(n,"scrollContainer"),a={},f;e(u).on("scroll",C);for(f in c){a[f]=w(n,f)}return this.each(function(u,f){if(f===t){e(l.selector).lazyLoadXT(n)}else{if(o&&v(f,s)){return}var c=e(f).data(s,1);if(r&&f.tagName==="IMG"&&!f.src){f.src=r}c[i]=d({},a);S("init",c);g.push(c)}})};e(n).ready(function(){S("start",h);h.on(l.loadEvent,k).on(l.updateEvent,C).on(l.forceEvent,L);e(n).on(l.updateEvent,C);if(l.autoInit){k()}})})(window.jQuery||window.Zepto||window.$,window,document);(function(e){var t=e.lazyLoadXT;t.selector+=",video,iframe[data-src],embed[data-src]";t.videoPoster="data-poster";e(document).on("lazyshow","video",function(n,r){var i=r.lazyLoadXT.srcAttr,s=e.isFunction(i);r.attr("poster",r.attr(t.videoPoster)).children("source,track").each(function(t,n){var r=e(n);r.attr("src",s?i(r):r.attr(i))});if(typeof e(this).attr('preload')!=='undefined'&&'none'!=e(this).attr('preload')){this.load()}e(this).removeClass("lazy-hidden")});e(document).on("lazyshow","embed",function(t,n){e(this).removeClass("lazy-hidden")})})(window.jQuery||window.Zepto||window.$);
// source --> //localhost/wordpress/wp-content/plugins/a3-lazy-load/assets/js/jquery.lazyloadxt.srcset.min.js?ver=1.8.9 
/* Lazy Load XT 1.0.6 | MIT License */
!function(t,r,e,n){function s(r,e){return Math[e].apply(null,t.map(r,function(t){return t[o]}))}function a(t){return t[o]>=g[o]||t[o]===d}function c(t){return t[o]===d}function i(n){var i=n.attr(u.srcsetAttr);if(!i)return!1;var l=t.map(i.split(","),function(t){return{url:x.exec(t)[1],w:parseFloat((f.exec(t)||p)[1]),h:parseFloat((w.exec(t)||p)[1]),x:parseFloat((h.exec(t)||m)[1])}});if(!l.length)return!1;var A,v,E=e.documentElement;g={w:r.innerWidth||E.clientWidth,h:r.innerHeight||E.clientHeight,x:r.devicePixelRatio||1};for(A in g)o=A,d=s(l,"max"),l=t.grep(l,a);for(A in g)o=A,d=s(l,"min"),l=t.grep(l,c);return v=l[0].url,u.srcsetExtended&&(v=(n.attr(u.srcsetBaseAttr)||"")+v+(n.attr(u.srcsetExtAttr)||"")),v}var o,d,u=t.lazyLoadXT,l=function(){return"srcset"in new Image}(),x=/^\s*(\S*)/,f=/\S\s+(\d+)w/,w=/\S\s+(\d+)h/,h=/\S\s+([\d\.]+)x/,p=[0,1/0],m=[0,1],A={srcsetAttr:"data-srcset",srcsetExtended:!1,srcsetBaseAttr:"data-srcset-base",srcsetExtAttr:"data-srcset-ext"},g={w:0,h:0,x:0};for(o in A)u[o]===n&&(u[o]=A[o]);u.selector+=",img["+u.srcsetAttr+"]",t(e).on("lazyshow","img",function(t,r){var e=r.attr(u.srcsetAttr);e&&(!u.srcsetExtended&&l?(r.attr("srcset",e),r.attr("data-srcset","")):r.lazyLoadXT.srcAttr=i)})}(window.jQuery||window.Zepto||window.$,window,document);