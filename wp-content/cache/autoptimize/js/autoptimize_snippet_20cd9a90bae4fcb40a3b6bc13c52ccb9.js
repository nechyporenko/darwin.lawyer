!function(a){"use strict";var b=a(".mpc-column--sticky");b.each(function(){a(this).before('<div class="mpc-column--spacer"></div>')}),_mpc_vars.$window.on("mpc.resize",function(){_mpc_vars.breakpoints.custom("(max-width: 992px)")&&a.each(b,function(){var b=a(this);b.removeAttr("style"),b.prev(".mpc-column--spacer").removeClass("mpc-active")})}),_mpc_vars.$window.on("scroll",function(){b.each(function(){var b,c=a(this),d=c.parents(".mpc-row"),e=""!=c.data("offset")?parseInt(c.data("offset")):0,f=window.pageYOffset;if(_mpc_vars.breakpoints.custom("(max-width: 992px)"))return c.removeAttr("style"),c.prev(".mpc-column--spacer").removeClass("mpc-active"),"";b=f-d.offset().top+e>0?f-d.offset().top+e:0,c.outerHeight()+b>=d.height()?(b=d.height()-c.outerHeight(),c.removeAttr("style").css("top",b),c.prev(".mpc-column--spacer").removeClass("mpc-active")):0==b?(c.removeAttr("style"),c.prev(".mpc-column--spacer").removeClass("mpc-active")):(c.css({position:"fixed",top:e,left:c.offset().left,width:c.outerWidth(!0)}),c.prev(".mpc-column--spacer").css("width",c.outerWidth(!0)).addClass("mpc-active"))})})}(jQuery);