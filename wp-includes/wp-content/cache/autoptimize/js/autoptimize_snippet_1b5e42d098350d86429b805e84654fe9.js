!function(n){n.fn.UItoTop=function(o){var e={text:"To Top",min:200,inDelay:600,outDelay:400,containerID:"toTop",containerHoverID:"toTopHover",scrollSpeed:1200,easingType:"linear"},t=n.extend(e,o),i="#"+t.containerID,a="#"+t.containerHoverID;n("body").append('<a href="#" id="'+t.containerID+'"><span>'+t.text+"</span></a>"),n(i).hide().on("click.UItoTop",function(){return n("html, body").animate({scrollTop:0},t.scrollSpeed,t.easingType),n("#"+t.containerHoverID,this).stop().animate({opacity:0},t.inDelay,t.easingType),!1}).hover(function(){n(a,this).stop().animate({opacity:1},600,"linear")},function(){n(a,this).stop().animate({opacity:0},700,"linear")}),n(window).scroll(function(){var o=n(window).scrollTop();"undefined"==typeof document.body.style.maxHeight&&n(i).css({position:"absolute",top:o+n(window).height()-50}),o>t.min?n(i).fadeIn(t.inDelay):n(i).fadeOut(t.Outdelay)})}}(jQuery);