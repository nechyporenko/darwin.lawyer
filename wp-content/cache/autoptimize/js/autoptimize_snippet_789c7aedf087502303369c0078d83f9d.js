!function(a){"use strict";function b(a){mpc_init_lightbox(a,!1),a.trigger("mpc.inited")}void 0!==window.InlineShortcodeView&&(window.InlineShortcodeView_mpc_image=window.InlineShortcodeView.extend({rendered:function(){var a=this.$el.find(".mpc-image"),c=a.closest(".vc_element");a.addClass("mpc-waypoint--init"),_mpc_vars.$body.trigger("mpc.icon-loaded",[c]),_mpc_vars.$body.trigger("mpc.font-loaded",[c]),_mpc_vars.$body.trigger("mpc.inited",[c]),setTimeout(function(){b(a)},250),window.InlineShortcodeView_mpc_image.__super__.rendered.call(this)}})),a(".mpc-image").each(function(){var c=a(this);c.one("mpc.init",function(){b(c)})})}(jQuery);