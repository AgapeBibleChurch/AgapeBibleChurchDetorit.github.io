// source --> https://localhost/wordpress/wp-content/plugins/siteorigin-premium/addons/plugin/tooltip/js/so-premium-tooltip.min.js?ver=1.4.0 
jQuery(function(t){t(".so-widget-sow-image, .so-widget-sow-image-grid, .so-widget-sow-simple-masonry").each(function(o,e){var i=t(e);if(i.data("tooltip-enabled")){var s=i.data("tooltip-theme");i.find("img").each(function(o,e){var i=t(e),r=i.attr("title");if(r){i.attr("title",null);var a=t('<div class="so-premium-tooltip">'+r+'<div class="callout"></div></div>');a.css("visibility","hidden"),a.addClass("theme-"+s),a.css("max-width",i.outerWidth());var n=i.parent(),l=n.is(".sow-masonry-grid-item");l?n.parent().append(a):n.append(a);var u={top:0,left:0},d=a.find(".callout"),m=function(){var t=l?n.position():i.position(),o={top:t.top+u.top-a.outerHeight(),left:t.left+u.left-.5*a.outerWidth()};a.css(o)},p=function(){m(),a.fadeIn(100)};"follow_cursor"===soPremiumTooltipOptions.position&&a.css("pointer-events","none");var f;a.hide(),a.css("visibility","visible"),n.on(soPremiumTooltipOptions.show_trigger,function(o){var e=a.get(0);if(a.is(":visible")||o.target===e||o.relatedTarget===e||o.relatedTarget===i.get(0))return!1;var s=l?n:i;d.removeClass("bottom").addClass("top"),d.css("pointer-events","none");var r=.5*d.outerHeight();switch(soPremiumTooltipOptions.position){case"follow_cursor":n.on("mousemove",function(t){u.top=t.offsetY-r;var o=i.outerWidth(),e=n.outerWidth(),s=o>e?.5*(o-e):0;u.left=t.offsetX-s,m()});break;case"center":u.top=.5*s.outerHeight()-r,u.left=.5*s.outerWidth();break;case"top":u.top=0-r,u.left=.5*s.outerWidth();break;case"bottom":d.removeClass("top").addClass("bottom"),u.top=s.outerHeight()+a.outerHeight()+r,u.left=.5*s.outerWidth()}if("mouseover"===soPremiumTooltipOptions.show_trigger&&soPremiumTooltipOptions.show_delay&&soPremiumTooltipOptions.show_delay>0?(f&&clearTimeout(f),f=setTimeout(function(){f=null,p()},soPremiumTooltipOptions.show_delay)):p(),"click"===soPremiumTooltipOptions.hide_trigger){var c=function(){a.fadeOut(100),t(window).off("click",c)};setTimeout(function(){t(window).on("click",c)},100)}}),"mouseout"===soPremiumTooltipOptions.hide_trigger&&(n.on("mouseout",function(){f&&clearTimeout(f),event.relatedTarget===a.get(0)||t.contains(n.get(0),event.relatedTarget)||(a.fadeOut(100),n.off("mousemove",m))}),l&&a.on("mouseout",function(t){t.relatedTarget!==i.get(0)&&(a.fadeOut(100),n.off("mousemove",m))}))}})}})});
// source --> https://localhost/wordpress/wp-content/plugins/so-widgets-bundle/widgets/accordion/js/accordion.min.js?ver=1.13.0 
var sowb=window.sowb||{};jQuery(function(o){sowb.setupAccordion=function(){o(".sow-accordion").each(function(n,a){var i=o(this).closest(".so-widget-sow-accordion");if(i.data("initialized"))return o(this);var e=i.data("useAnchorTags"),t=i.data("initialScrollPanel"),c=o(a).find("> .sow-accordion-panel");c.not(".sow-accordion-panel-open").find(".sow-accordion-panel-content").hide();var s=c.filter(".sow-accordion-panel-open").toArray(),r=function(){},d=function(n,a){var i=o(n);i.is(".sow-accordion-panel-open")||(i.find("> .sow-accordion-panel-content").slideDown(function(){o(this).trigger("show"),o(sowb).trigger("setup_widgets")}),i.addClass("sow-accordion-panel-open"),s.push(n),a||r())},w=function(n,a){var i=o(n);i.is(".sow-accordion-panel-open")&&(i.find("> .sow-accordion-panel-content").slideUp(function(){o(this).trigger("hide")}),i.removeClass("sow-accordion-panel-open"),s.splice(s.indexOf(n),1),a||r())};if(c.find("> .sow-accordion-panel-header").click(function(){var n=o(this),a=i.data("maxOpenPanels"),e=n.closest(".sow-accordion-panel");e.is(".sow-accordion-panel-open")?w(e.get(0)):d(e.get(0)),!isNaN(a)&&a>0&&s.length>a&&w(s[0])}),e){r=function(){for(var n=[],a=0;a<s.length;a++){var i=o(s[a]).data("anchor");i&&(n[a]=i)}n&&n.length?window.location.hash=n.join(","):window.history.pushState("",document.title,window.location.pathname+window.location.search)};var l=function(){for(var n=c.toArray(),a=0;a<n.length;a++){panel=n[a];var i=o(panel).data("anchor");i&&window.location.hash.indexOf(i)>-1?d(panel,!0):w(panel,!0)}};if(o(window).on("hashchange",l),window.location.hash?l():r(),t>0){var p=t>c.length?c.last():c.eq(t-1);window.scrollTo(0,p.offset().top-90)}}i.data("initialized",!0)})},sowb.setupAccordion(),o(sowb).on("setup_widgets",sowb.setupAccordion)}),window.sowb=sowb;