/**
 *
 * Custom codevz menu
 * @version 1.7.6
 *
 */
!function(e,s){"use strict";var o=function(){var o={bcClass:"sf-breadcrumb",menuClass:"sf-js-enabled",anchorClass:"sf-with-ul",menuArrowClass:"sf-arrows"},n=function(){var s=/^(?![\w\W]*Windows Phone)[\w\W]*(iPhone|iPad|iPod)/i.test(navigator.userAgent);return s&&e("html").css("cursor","pointer").on("click",e.noop),s}(),t=function(){var e=document.documentElement.style;return"behavior"in e&&"fill"in e&&/iemobile/i.test(navigator.userAgent)}(),i=function(){return!!s.PointerEvent}(),r=function(e,s){var n=o.menuClass;s.cssArrows&&(n+=" "+o.menuArrowClass),e.toggleClass(n)},a=function(s,n){return s.find("li."+n.pathClass).slice(0,n.pathLevels).addClass(n.hoverClass+" "+o.bcClass).filter(function(){return e(this).children(n.popUpSelector).hide().show().length}).removeClass(n.pathClass)},l=function(e){e.children("a").toggleClass(o.anchorClass)},h=function(e){var s=e.css("ms-touch-action"),o=e.css("touch-action");o=o||s,o="pan-y"===o?"auto":"pan-y",e.css({"ms-touch-action":o,"touch-action":o})},u=function(e){return e.closest("."+o.menuClass)},p=function(e){return u(e).data("sf-options")},c=function(){var s=e(this),o=p(s);clearTimeout(o.sfTimer),s.siblings().codevzMenu("hide").end().codevzMenu("show")},f=function(s){s.retainPath=e.inArray(this[0],s.$path)>-1,this.codevzMenu("hide"),this.parents("."+s.hoverClass).length||(s.onIdle.call(u(this)),s.$path.length&&e.proxy(c,s.$path)())},d=function(){var s=e(this),o=p(s);n?e.proxy(f,s,o)():(clearTimeout(o.sfTimer),o.sfTimer=setTimeout(e.proxy(f,s,o),o.delay))},v=function(s){var o=e(this),n=p(o),t=o.siblings(s.data.popUpSelector);return n.onHandleTouch.call(t)===!1?this:void(t.length>0&&t.is(":hidden")&&(o.one("click.codevzMenu",!1),"MSPointerDown"===s.type||"pointerdown"===s.type?o.trigger("focus"):e.proxy(c,o.parent("li"))()))},m=function(s,o){var r="li:has("+o.popUpSelector+")";e.fn.hoverIntent&&!o.disableHI?s.hoverIntent(c,d,r):s.on("mouseenter.codevzMenu",r,c).on("mouseleave.codevzMenu",r,d);var a="MSPointerDown.codevzMenu";i&&(a="pointerdown.codevzMenu"),n||(a+=" touchend.codevzMenu"),t&&(a+=" mousedown.codevzMenu"),s.on("focusin.codevzMenu","li",c).on("focusout.codevzMenu","li",d).on(a,"a",o,v)};return{hide:function(s){if(this.length){var o=this,n=p(o);if(!n)return this;var t=n.retainPath===!0?n.$path:"",i=o.find("li."+n.hoverClass).add(this).not(t).removeClass(n.hoverClass).children(n.popUpSelector),r=n.speedOut;if(s&&(i.show(),r=0),n.retainPath=!1,n.onBeforeHide.call(i)===!1)return this;i.hide()}return this},show:function(){var e=p(this);if(!e)return this;var s=this.addClass(e.hoverClass),o=s.children(e.popUpSelector);return e.onBeforeShow.call(o)===!1?this:(o.show(),this)},destroy:function(){return this.each(function(){var s,n=e(this),t=n.data("sf-options");return t?(s=n.find(t.popUpSelector).parent("li"),clearTimeout(t.sfTimer),r(n,t),l(s),h(n),n.off(".codevzMenu").off(".hoverIntent"),s.children(t.popUpSelector).attr("style",function(e,s){return s.replace(/display[^;]+;?/g,"")}),t.$path.removeClass(t.hoverClass+" "+o.bcClass).addClass(t.pathClass),n.find("."+t.hoverClass).removeClass(t.hoverClass),t.onDestroy.call(n),void n.removeData("sf-options")):!1})},init:function(s){return this.each(function(){var n=e(this);if(n.data("sf-options"))return!1;var t=e.extend({},e.fn.codevzMenu.defaults,s),i=n.find(t.popUpSelector).parent("li");t.$path=a(n,t),n.data("sf-options",t),r(n,t),l(i),h(n),m(n,t),i.not("."+o.bcClass).codevzMenu("hide",!0),t.onInit.call(this)})}}}();e.fn.codevzMenu=function(s,n){return o[s]?o[s].apply(this,Array.prototype.slice.call(arguments,1)):"object"!=typeof s&&s?e.error("Method "+s+" does not exist on jQuery.fn.codevzMenu"):o.init.apply(this,arguments)},e.fn.codevzMenu.defaults={popUpSelector:"ul,.sf-mega",hoverClass:"sfHover",pathClass:"overrideThisToUse",pathLevels:1,delay:300,easing:'linear',animation:{opacity:"show"},animationOut:{opacity:"hide"},speed:100,speedOut:100,cssArrows:!0,disableHI:!1,onInit:e.noop,onBeforeShow:e.noop,onShow:e.noop,onBeforeHide:e.noop,onHide:e.noop,onIdle:e.noop,onDestroy:e.noop,onHandleTouch:e.noop}}(jQuery,window);

/**
 *
 * Custom theme scripts
 *
 * @author Codevz
 * @website www.codevz.com
 *
 */
var Codevz = (function($) {
	"use strict";

	$.fn.codevz = function( n, i ) {
		$( this ).each(function( a ) {
			var e = $( this );

			if ( e.data( 'codevz' ) !== n || $( '.vc_editor' ).length ) {
				i.apply( e.data( 'codevz', n ), [ a ] );
			}
		});
	}

	var body = $( 'body' ),
		wind = $( window ),
		inla = $( '.inner_layout' ),
		abar = ( $( '.admin-bar' ).length ? 32 : 0 );

	return {
		init: function() {
			this.search();
			this.loading();
			this.header_shape();

			// Posts equality
			$( '.cz_default_loop_grid' ).closest( '.cz_posts_container' ).each(function() {
				$( this ).addClass( 'cz_posts_equal' );
			});

			// RTL preview
			if ( window.location.search.indexOf( 'rtl' ) >= 1 ) {
				$( "a[target!='_blank']" ).each(function() {
					if ( ! (/rtl/.test( this.href ) ) ) {
						this.href += (/\?/.test( this.href ) ? '&' : '?') + 'rtl';
					}
				});
			}

			// Fixed Footer
			$( '.cz_fixed_footer' ).codevz( 'fixed_footer', function() {
				wind.on( 'resize', function() {
					body.css( 'margin-bottom', $( '.cz_fixed_footer' ).height() );
				});

				// Temp fix
				setTimeout(function() {
					body.css( 'margin-bottom', $( '.cz_fixed_footer' ).height() );
				}, 1000 );
			});

			// Header line full height
			$( '.header_line_1' ).each(function() {
				$( this ).height( $( this ).closest( '.row' ).height() );
			});

			// Menus
			$( '.sf-menu' ).codevz( 'sf_menu', function() {
				var disMenu 	= $( this ),
					indicator 	= disMenu.data( 'indicator' ),
					default_ind = disMenu.hasClass( 'offcanvas_menu' ) ? 'fa fa-angle-down' : '',
					indicator 	= indicator ? indicator : default_ind,
					indicator2 	= disMenu.data( 'indicator2' ),
					indicator2 	= indicator2 ? indicator2 : default_ind,
					opa = $( '.page_content, .page_cover, footer' );

				// Super Fish
				disMenu.codevzMenu({
					onInit: function() {

						// Menu Indicators
						$( '.sf-with-ul, h6', this ).each(function() {
							var en = $( this );
							if ( ! $( '.cz_indicator', en ).length ) {
								if ( $( '.cz_menu_subtitle', en ).length ) {
									$( '.cz_menu_subtitle', en ).before( '<i class="cz_indicator"></i>' );
								} else {
									en.append( '<i class="cz_indicator"></i>' );
								}
							}
							if ( ( indicator && indicator.length ) || ( indicator2 && indicator2.length ) ) {
								$( '.cz_indicator', en ).addClass( ( en.parent().parent().hasClass( 'sf-menu' ) ? indicator : indicator2 ) );
							}
						});

						// Fix menus width
						Codevz.fixMenusWidthBreaks();
					},
					onBeforeShow: function() {
						var dis = $( this );

						if ( ! dis.is(':visible') ) {
							//Codevz.showOneByOne( $( '> .cz', this ), 8, 'left' );
							//Codevz.showOneByOne( $( '> .cz .cz', this ), 8, 'left' );
						}

						if ( dis.hasClass('sub-menu') ) {
							var ul_offset = 100;

							// Check if mega menu is fullwide
							if ( dis.parent().hasClass( 'cz_megamenu_width_fullwide' ) ) {
								dis.css( 'cssText', 'width: ' + wind.width() + 'px !important' );
								ul_offset = 0;
							}

							// Sub-menu styling
							if ( dis.parent().data( 'sub-menu' ) ) {
								setTimeout(function() {
									dis.attr( 'style', dis.attr( 'style' ) + dis.parent().data( 'sub-menu' ) );
								}, 50 );
							}

							// Megamenu
							if ( dis.parent().hasClass( 'cz_parent_megamenu' ) ) {
								dis.addClass( 'cz_megamenu_' + $( '> .cz', dis ).length ).find( 'ul' ).addClass( 'cz_megamenu_inner_ul clr' );
							}

							// Megamenu width offset of window
							var parent_li_offset = wind.width() - dis.parent().offset().left,
								dis_ul_width = dis.width() + ul_offset;
							if ( parent_li_offset < dis_ul_width ) {
								var new_ul_offset = dis_ul_width - parent_li_offset;
								dis.css( 'left', -new_ul_offset + 'px' );
								if ( dis.parent().parent().hasClass('sub-menu') ) {
									dis.addClass( 'cz_open_menu_reverse' ).css('left', '');
								}
							} else {
								dis.removeClass( 'cz_open_menu_reverse' );
							}

							// Megamenu full row
							if ( dis.parent().hasClass( 'cz_megamenu_width_full_row' ) ) {
								var megamenu_row = $( '.row' ),
									megamenu_row_offset = megamenu_row.offset().left;
								dis.attr( 'style', dis.attr( 'style' ) + 'width: ' + megamenu_row.width() + 'px;left:' + ( megamenu_row_offset - dis.parent().offset().left ) + 'px' );
								ul_offset = 0;
							}

						}

						if ( dis.closest('.fixed_side').length ) {
							var pwidth = dis.parent().closest( '.sub-menu' ).length ? '.sub-menu' : '.sf-menu',
								ff_pos = $( '.fixed_side' ).hasClass( 'fixed_side_left' ) ? 'left' : 'right';
							dis.css( ff_pos, dis.closest( pwidth ).width() );
						}
					}
				});

				// Fullscreen Menu
				$( '.icon_fullscreen_menu' ).codevz( 'fulls_menu', function() {
					$( this ).off( 'click' ).on( 'click', function() {
						var sf_f = $( '.fullscreen_menu' );
						sf_f.fadeIn( 'fast', function() {
							body.addClass( 'cz_noscroll' );
						}).on( 'click', function() {
							$( this ).fadeOut( 'fast', function() {
								body.removeClass( 'cz_noscroll' );
							});
						});
						if ( sf_f.is(':visible') ) {
							Codevz.showOneByOne( $( '> .cz', sf_f ), 150 );
						}
						wind.on( 'resize', function() {
							sf_f.css( 'padding-top', ( wind.height() / 2 - sf_f.height() / 4 ) );
						});
					});
				});

				// Fullscreen
				$( 'ul.fullscreen_menu' ).codevz( 'ul_fulls_menu', function() {
					$( '.cz', this ).on( 'hover', function(e) {
						e.stopPropagation();
					}).off( 'click' ).on( 'click', function(e) {
						if ( $( e.target ).hasClass( 'cz_indicator' ) ) {
							$( this ).closest( 'li' ).find('> ul').fadeToggle( 'fast' );
							e.preventDefault();
							e.stopPropagation();
						}
					});
				});

				// Dropdown Menu
				$( '.icon_dropdown_menu' ).codevz( 'dropdown_menu', function() {
					$( this ).off( 'click' ).on( 'click', function(e) {
						var dis = $( this ),
							pos = dis.position(),
							nav = dis.next().next('.sf-menu'),
							row = $( this ).closest('.row').height(),
							offset = ( ( inla.outerWidth() + inla.offset().left ) - dis.offset().left );

						if ( nav.is(':visible') ) {
							nav.fadeOut( 'fast' );
							return;
						}

						nav.fadeToggle( 'fast' );

						body.on( 'click.cz_idm', function(e) {
							nav.fadeOut( 'fast' );
							body.off( 'click.cz_idm' );
						});

						$( '.cz', nav ).on( 'hover', function(e) {
							e.stopPropagation();
						}).off( 'click' ).on( 'click', function(e) {
							if ( $( e.target ).hasClass( 'cz_indicator' ) ) {
								$( this ).closest( 'li' ).find('> ul').fadeToggle( 'fast' );
								e.preventDefault();
								e.stopPropagation();
							}
						});

						e.stopPropagation();
					});
				});

				// Open Menu Horizontal
				$( '.icon_open_horizontal' ).codevz( 'iohor', function() {
					$( this ).off( 'click' ).on( 'click', function(e) {
						var dis = $( this ),
							pos = dis.position(),
							nav = dis.next().next('.sf-menu'),
							row = $( this ).closest('.row').height(),
							offset = ( ( inla.outerWidth() + inla.offset().left ) - dis.offset().left );

						if ( nav.is(':visible') ) {
							nav.fadeOut( 'fast' );
							return;
						}

						nav.fadeToggle( 'fast' );
						Codevz.showOneByOne( $( '> .cz', nav ), 100, ( nav.hasClass( 'inview_left' ) ? 'left' : 'right' ) );

						body.on( 'click.cz_ioh', function(e) {
							nav.fadeOut( 'fast' );
							body.off( 'click.cz_ioh' );
						});

						e.stopPropagation();
					});
				});

				// Mobile Menu
				disMenu.prev( 'i.icon_mobile_offcanvas_menu' ).codevz( 'imom', function() {
					var en = $( this );

					en.removeClass( 'hide' ).on( 'click', function() {
						if ( ! $( this ).hasClass( 'done' ) ) {
							$( this ).addClass( 'done' );
							Codevz.offCanvas( $( this ), 1 );
							
							// Add mobile menus indicator
							if ( indicator.length || indicator2.length ) {
								$( this ).next( '.sf-menu' ).find( '.sf-with-ul' ).each(function() {
									$( '.cz_indicator', this ).addClass( ( $( this ).parent().parent().hasClass( 'sf-menu' ) ? indicator : indicator2 ) );
								});
							}

							var ul_offcanvas = $( 'ul.offcanvas_area' );
							$( '.sf-with-ul, .cz > h6', ul_offcanvas ).on( 'click', function(e) {
								if ( $( e.target ).hasClass( 'cz_indicator' ) ) {
									$( this ).next().slideToggle( 'fast' );
									e.preventDefault();
								}
							});
						}
					});
				});
			});

			// OffCanvas
			$( '.offcanvas_container > i' ).codevz( 'offcanvas_i', function() {
				$( this ).on( 'click', function() {
					if ( ! $( this ).hasClass( 'done' ) ) {
						$( this ).addClass( 'done' );
						Codevz.offCanvas( $( this ), 1 );
					}
				});
			});

			// WPML
			$( '.cz_language_switcher' ).codevz( 'lang_switcher', function() {
				var dis = $( this );
				$( '.cz_current_language', dis ).prependTo( dis );
			});

			// Fix WPML widgets
			$( '.footer_widget, .widget' ).each(function() {
				if ( $( '> .clr', this ).html() === '' ) {
					$( this ).remove();
				}
			});

			// Hidden fullwidth content
			$( '.hf_elm_icon' ).codevz( 'hf_elm_icon', function() {
				$( this ).on( 'click', function(e) {
					var dis = $( this );

					dis.next( '.hf_elm_area' ).slideToggle( 'fast' ).css({
						width: inla.outerWidth(),
						left: inla.offset().left,
						top: dis.offset().top + dis.outerHeight()
					});

					e.preventDefault();
					e.stopPropagation();
				});

				body.on( 'click', function() {
					$( '.hf_elm_area' ).slideUp( 'fast' );
				});
			});

			// Header on title
			if ( $( '.header_onthe_cover' ).length ) {
				Codevz.header_on_title();
				wind.on( 'resize', function() {
					Codevz.header_on_title();
				});

				if ( $.fn.imagesLoaded ) {
					$( '.page_header' ).imagesLoaded().progress(function( imgLoad, image ) {
						Codevz.header_on_title();
					});
				} else {
					$( '.page_header img' ).on( 'load', function() {
						setTimeout( function() {
							Codevz.header_on_title();
						}, 250 );
					});
				}

				Codevz.heightChanged( '.page_header', function() {
					Codevz.header_on_title();
				});
			}

			// iframes auto size
			$( '.cz_iframe, .single .cz_post_content iframe, object, embed' ).not('.wp-embedded-content').codevz( 'cz_iframe', function() {
				var en = $( this ), newWidth;
				wind.on( 'resize', function() {
					en.attr( 'data-aspectRatio', en.height() / en.width() ).removeAttr( 'height width' );
					newWidth = en.parent().width();
					en.width( newWidth ).height( newWidth * en.attr( 'data-aspectRatio' ) );
				});
			});

			// Extra
			$( '.tagcloud' ).length && $( '.tagcloud' ).addClass( 'clr' );

			// Input buttons to button tag
			$( '.form-submit .submit, input.search-submit, .wpcf7-submit' ).codevz( 'button', function() {
				var en = $( this );
				$('<button name="submit" type="submit" class="' + en.attr('class') + '">' + en.val() + '</button>').insertAfter( en );
				en.detach();
			});

			/* Sticky */
			$( '.header_is_sticky' ).codevz( 'header_sticky', function(n) {
				var header_sticky = $( this ),
					header_5 = $( '.header_5' ),
					lastScrollTop = 0,
					st, stickyNav, sticky_func, 
					scrollTop = header_sticky.offset().top,
					smart_sticky = function( scroll ) {
						if ( header_sticky.hasClass( 'smart_sticky' ) ) {
							st = scroll.scrollTop();

							if ( st > 300 && st > lastScrollTop ) {
								header_sticky.addClass( 'smart_sticky_on' );
							} else if ( st < lastScrollTop ) {
								header_sticky.removeClass( 'smart_sticky_on' );
							}

							lastScrollTop = st;
						}
					};

				if ( header_5.length ) {
					
					header_5.addClass( 'onSticky' );
					wind.on( 'scroll', function(e){
						if ( wind.scrollTop() >= $( '.page_header' ).height() ) {
							header_5.css( 'transform', 'translateY(0)' );
						} else {
							header_5.css( 'transform', 'translateY(-120%)' );
						}

						smart_sticky( $( this ) );
					});

				} else if ( header_sticky.length ) {

					/* Add corpse */
					if ( ! header_sticky.prev( '.Corpse_Sticky').length ) {
						header_sticky.before( '<div class="Corpse_Sticky' + ( header_sticky.hasClass( 'header_4' ) ? ' cz_sticky_corpse_for_header_4' : '' ) + '"></div>' );
					}

					var scroll_down,
						new_scrollTop,
						cz_sticky_h12 = $( '.cz_sticky_h12' ).length,
						cz_sticky_h13 = $( '.cz_sticky_h13' ).length,
						cz_sticky_h23 = $( '.cz_sticky_h23' ).length,
						cz_sticky_h123 = $( '.cz_sticky_h123' ).length;

					sticky_func = function(e) {
						if ( header_sticky.hasClass( 'header_4' ) && header_sticky.css( 'display' ) == 'none' ) {
							return;
						}

						new_scrollTop = scrollTop;
						if ( cz_sticky_h12 && header_sticky.hasClass( 'header_2' ) ) {
							new_scrollTop = scrollTop+1 - $( '.header_1' ).outerHeight();
						} else if ( cz_sticky_h13 && header_sticky.hasClass( 'header_3' ) ) {
							new_scrollTop = scrollTop+1 - $( '.header_1' ).outerHeight();
						} else if ( cz_sticky_h23 && header_sticky.hasClass( 'header_3' ) ) {
							new_scrollTop = scrollTop+1 - $( '.header_2' ).outerHeight();
						} else if ( cz_sticky_h123 ) {
							if ( header_sticky.hasClass( 'header_2' ) ) {
								new_scrollTop = scrollTop+1 - $( '.header_1' ).outerHeight();
							}
							if ( header_sticky.hasClass( 'header_3' ) ) {
								new_scrollTop = scrollTop+1 - ( $( '.header_1' ).outerHeight() + $( '.header_2' ).outerHeight() );
							}
						}

						scroll_down = ( wind.scrollTop() + abar ) > new_scrollTop;

						if ( scroll_down && cz_sticky_h12 && header_sticky.hasClass( 'header_2' ) ) {
							$( '.header_2' ).css( 'marginTop', $( '.header_1' ).outerHeight() );
						} else if ( scroll_down && cz_sticky_h13 && header_sticky.hasClass( 'header_3' ) ) {
							$( '.header_3' ).css( 'marginTop', $( '.header_1' ).outerHeight() );
						} else if ( scroll_down && cz_sticky_h23 && header_sticky.hasClass( 'header_3' ) ) {
							$( '.header_3' ).css( 'marginTop', $( '.header_2' ).outerHeight() );
						} else if ( cz_sticky_h123 ) {
							if ( scroll_down && header_sticky.hasClass( 'header_2' ) ) {
								$( '.header_2' ).css( 'marginTop', $( '.header_1' ).outerHeight() );
							}
							if ( scroll_down && header_sticky.hasClass( 'header_3' ) ) {
								$( '.header_3' ).css( 'marginTop', ( $( '.header_1' ).outerHeight() + $( '.header_2' ).outerHeight() ) );
							}
						}

						if ( scroll_down ) {
							header_sticky.addClass( 'onSticky' ).prev( '.Corpse_Sticky' ).css( 'height', header_sticky.outerHeight() + 'px' );
						} else {
							header_sticky.removeClass( 'onSticky' ).prev( '.Corpse_Sticky').css( 'height', 'auto' );
							header_sticky.css( 'marginTop', '' );
						}

						smart_sticky( $( this ) );
						header_sticky.css( 'width', inla.width() + 'px' );
					};

					wind.off( 'scroll.cz_sticky_' + n ).on( 'scroll.cz_sticky_' + n, sticky_func );
					wind.off( 'resize.cz_sticky_' + n ).on( 'resize.cz_sticky_' + n, sticky_func );
				}
			});

			this.menu_anchor();
		},

		/*
		*   Header and title position fix
		*/
		header_on_title: function() {
			var en 		= $( '.header_onthe_cover' ),
				margin  = $( '.header_after_cover' ).length ? 'margin-bottom' : 'margin-top';

			en.css( margin, - $( '.page_header' ).outerHeight() ).css( 'opacity', '1' );
		},

		/*
		*   Menu Anchor
		*/
		menu_anchor: function() {
			var mPage = $( '.sf-menu' ),
				mLink = $( "a[href*='#']" ).not( '.cz_lrpr a, .wc-tabs a, .cz_edit_popup_link, .page-numbers a, #cancel-comment-reply-link, .vc_carousel-control, [data-vc-container],.comment-form-rating a' ),
				sticky = $( '.header_is_sticky' ).not( '.smart_sticky' ), t, offset, easing = null;

			if ( mLink.length ) {
				mLink.off( 'click.cz_manchor' ).on( 'click.cz_manchor', function(e) {
					if ( location.pathname.replace(/^\//,'') == this.pathname.replace(/^\//,'') && location.hostname == this.hostname ) {
						t = $( this.hash );
						if ( t.length ) {
							offset = t.offset().top;
							if ( $.easing != 'undefined' ) {
								easing = 'easeInOutExpo';
							}
							$( 'html, body' ).animate({ scrollTop: ( offset - abar ) - sticky.outerHeight() }, 1000, easing, function() {
								var new_sticky = $( '.onSticky' ).not( '.smart_sticky' );
								$( 'html, body' ).animate({ scrollTop: ( offset - abar ) - new_sticky.outerHeight() }, 100 );
							});
						}
					} else {
						location = $( this ).attr( 'href' );
					}
					e.preventDefault();
				});

				var mPageH = mPage.outerHeight() + 15,
					mItems = mPage.find( "a[href*='#']" ),
					sItems = mItems.map(function(){
						var item = $( $( this ).attr( "href" ).replace( /^.*\#(.*)$/, "#$1" ) );
						if ( item.length ) { return item; }
					});

				wind.on( 'scroll', function() {
					var ft = $( this ).scrollTop() + mPageH + sticky.outerHeight() + abar,
						cur = sItems.map(function() {
							if ( $(this).offset().top < ft )
								return this;
							});

					cur = cur[cur.length-1];
					var id = cur && cur.length ? cur[0].id : "";
					if ( id && !$( '#' + id + '.cz_popup_modal' ).length && $( '#' + id ).length ) {
						body.trigger( 'click' );
						mItems.parent().removeClass( "current_menu" ).end().filter( "[href*='#" + id + "']" ).parent().addClass( "current_menu" );
					} else {
						mItems.parent().removeClass( "current_menu" );
					}
				});
			}
		},

		/*
		*   Fix menus lenght related to parent width
		*/
		fixMenusWidthBreaks: function() {
			wind.off( 'resize.cz_fix_menu_width' ).on( 'resize.cz_fix_menu_width', function() {
				$( 'header .cz_menu_default' ).not( '#menu_header_4' ).each(function() {
					var en = $( this ), parent = en.parent(), parent_width, items_width = 0, need_move = 0, nw, nw_ul;

					// Multiple menu in one row
					if ( parent.parent().find( '.cz_menu_default' ).length >= 2 ) {
						return;
					}

					// Reset
					en.append( en.find( '.cz-extra-menus > .sub-menu > li' ) ).find( '.cz-extra-menus' ).remove();

					// Parent width
					parent_width = parent.parent().outerWidth() - 50 - ( parseInt( parent.css( 'marginLeft' ) ) + parseInt( parent.css( 'marginRight' ) ) );
					if ( en.closest( '.have_center' ).length ) {
						parent_width = parent.parent().parent().outerWidth() - 50 - ( parseInt( parent.css( 'marginLeft' ) ) + parseInt( parent.css( 'marginRight' ) ) );
					}

					// Decrease each width
					parent.parent().find( '> div' ).each(function() {
						var enn = $( this );
						if ( enn.attr( 'class' ) !== parent.attr( 'class' ) ) {
							parent_width = parent_width - enn.outerWidth() - ( parseInt( enn.parent().css( 'marginLeft' ) ) + parseInt( enn.parent().css( 'marginRight' ) ) );
						}
					});

					// Add icon dots
					if ( ! en.find( '.cz-extra-menus' ).length ) {
						en.append( '<li class="cz-extra-menus cz"><a href="#" class="sf-with-ul"><span>&nbsp;<i class="fa fa-ellipsis-v"></i>&nbsp;</span></a><ul class="sub-menu"></ul></li>');
					}

					nw = en.find( '.cz-extra-menus' ), nw_ul = nw.find( '> ul' );

					en.find( '> li' ).each(function( i ) {
						var li = $(this);
						li.data( 'w', li.outerWidth() ).data( 'i', i );
						items_width += li.hasClass( 'cz-extra-menus' ) ? 0 : li.outerWidth();
						need_move = ( items_width > parent_width ) ? 1 : 0;
					});

					if ( need_move ) {
						items_width = nw.outerWidth();
						en.find( '> li' ).not( '.cz-extra-menus' ).each(function( i ) {
							var li = $( this ), li_width = li.outerWidth();
							if ( items_width <= parent_width ) {
								items_width += li_width;
							}
							if ( items_width > parent_width ) {
								var moved = 0;
								nw_ul.find( '> li' ).each(function() {
									if ( ! moved && Number( $( this ).data( 'i' ) ) > i ) {
										li.data( 'w', li_width ).insertBefore( $( this ) );
										moved = true;
									}
								});

								if ( ! moved ) {
									li.data( 'w', li_width ).appendTo( nw_ul );
								}
							}
						});
						nw.show();
						
					} else {
						var items = nw_ul.find( '> li' ), ii = 0, need_move = true;

						items.each(function() {
							if ( ! need_move ) {
								return;
							}
							if ( items.length - ii == 1 ) {
								items_width -= nw.outerWidth();
							}
							items_width += parseFloat( $( this ).data( 'w' ) );
							if ( items_width < parent_width ) {
								$( this ).insertBefore( nw );
								ii++;
							} else {
								need_move = 0;
							}
						});
						if ( items.length - ii == 0 ) {
							nw.hide();
						}
					}
				});
			});
		},

		/*
		*   Height changed = run callback
		*/
		heightChanged: function( elm, callback ) {
			var elm = ( typeof elm == 'string' ) ? $( elm ) : elm,
				lastHeight = elm.outerHeight(), newHeight;

			// First
			callback();

			// Height detection
			(function run() {
				newHeight = elm.outerHeight();
				if ( lastHeight != newHeight ) {
					callback();
					lastHeight = newHeight;
				}

				if ( elm.onElementHeightChangeTimer ) {
					clearTimeout( elm.onElementHeightChangeTimer );
				}

				elm.onElementHeightChangeTimer = setTimeout( run, 100 );
			})();
		},

		/*
		*   Ajax Search
		*/
		search: function() {
			var time = false;

			// Input changes
			if ( $( '.cz_ajax_search' ).length ) {
				$( '.cz_ajax_search' ).on('keyup', '[name="s"]', function() {
					clearTimeout( time );
					var form    = $( this ).parent(),
						results = form.next( '.ajax_search_results' ),
						icon 	= $( 'button i', form ).attr( 'class' ),
						ajax 	= $( '#intro' ).data( 'ajax' ),
						iLoader = 'fa fa-superpowers fa-pulse';

					if ( $( this ).val().length < 3 ) {
						$( '.ajax_search_results' ).slideUp( 'fast' );
						$( 'button i', form ).addClass( icon ).removeClass( iLoader );
						return;
					}
					$( 'button i', form ).removeClass( icon ).addClass( iLoader );

					// Send request
					time = setTimeout(
						function() {
							$.ajax({
								type: "GET",
								url: ajax,
								data: "action=codevz_ajax_search&" + form.serialize(),
								success: function( data ) {
									results.html( data ).slideDown( 'fast' );
									$( 'button i', form ).removeClass( iLoader ).addClass( icon );
								},
								error: function( xhr, status, error ) {
									results.html( '<b class="ajax_search_error">' + error + '</b>' ).slideDown( 'fast' );
									$( 'button i', form ).removeClass( iLoader ).addClass( icon );
									console.log( xhr, status, error );
								}
							});
						},
						500
					);
				});
			}

			// Search icon
			$( '.search_with_icon' ).codevz( 'search_wi', function() {
				$( this ).on( 'click', function(e) {
					e.stopPropagation();
				}).on( 'click', '[name="s"]', function() {
					if ( $( this ).val() ) {
						$( '.ajax_search_results' ).slideDown( 'fast' );
					}
				});
			});

			// Search dropdown and shop quick cart
			$( '.search_style_icon_dropdown, .elms_shop_cart' ).codevz( 'dr_search_cart', function() {
				var en = $( this );

				if ( ( wind.width() / 2 ) > ( en.offset().left + 300 ) ) {
					en.addClass( 'inview_right' );
				}
			});

			// Search dropdown and full row
			$( '.search_style_icon_dropdown > i, .search_style_icon_fullrow > i' ).codevz( 'sdr_fullwor', function() {
				$( this ).on( 'click', function(e) {
					var dis     = $( this ),
						outer   = dis.parent().find('.outer_search'),
						fullrow = dis.parent().hasClass( 'search_style_icon_fullrow' ),
						row_h   = dis.closest('.row').height(),
						clr     = dis.closest('.clr');

					if ( outer.is( ':visible' ) ) {
						outer.fadeOut( 'fast' );
					} else {
						outer.fadeIn( 'fast' ).find('input').focus();
					}

					if ( fullrow ) {
						outer.css({
							'top': clr.offset().top,
							'left': clr.offset().left,
							'width': clr.width(),
							'height': row_h
						});
						clr.css('height', row_h);
						clr.find('> div').css( 'z-index', 'initial' );
						dis.fadeOut( 'fast' );
						clr.find('> div').children().not('.search_header_1, .search_header_2, .search_header_3, .search_header_4, .search_header_5').animate({opacity: 1});
					}
				});
			});

			// Search fullscreen
			$( '.search_style_icon_full > i' ).codevz( 'ssifi', function() {
				$( this ).on( 'click', function() {
					//$( this ).closest( '.header_1,.header_2,.header_3' ).css( 'z-index', '9999' );
					$( this ).parent().find( '.outer_search' ).fadeIn( 'fast' ).find('input').focus();
					wind.off( 'resize.cz_search_full' ).on( 'resize.cz_search_full', function() {
						var w = wind.width(),
							h = wind.height(),
							s = $( this ).find('.outer_search .search');
						s.css({
							'top': h / 4 - s.height() / 2,
							'left': w / 2 - s.width() / 2
						});
					});
				});
			});

			$( 'body, .outer_search' ).on( 'click', function(e) {
				if ( $( e.target ).closest('.outer_search .search').length || $( e.target ).closest('.search_style_icon_fullrow').length ) {
					return;
				}

				$('.ajax_search_results').fadeOut( 'fast' );
				$( '.search_style_icon_dropdown, .search_style_icon_fullrow, .search_style_icon_full' ).find('.outer_search').fadeOut( 'fast' );

				if ( $( '.search_style_icon_fullrow' ).length ) {
					$( '.search_style_icon_fullrow > i' ).fadeIn( 'fast' );
					$( '.search_style_icon_fullrow > i' ).closest('.clr').find('> div').children().animate({opacity: 1});
				}
			});

		},

		/*
		*   Loading
		*/
		loading: function() {
			var p = $( '.pageloader' );

			if ( p.length ) {
				wind.on( 'load', function() {
					p.fadeOut( 'fast', 'linear' );
				});

				// Custom time
				if ( p.data( 'time' ) ) {
					setTimeout( function(){
						p.fadeOut( 'fast', 'linear' );
					}, p.data( 'time' ) );
				}

				// Loading on click
				if ( p.data( 'out' ) ) {
					$('a[href*="//"]').not( '.cz_language_switcher,.add_to_cart_button,.cart_list .remove,a[target="_blank"],[href^="#"],[href*="wp-login"],[id^="wpadminb"] a,[href*="wp-admin"],[data-rel^="prettyPhoto"],a[href$=".jpg"],a[href$=".jpeg"],a[href$=".png"],a[href$=".gif"],a[href$=".mp3"],a[href$=".zip"],a[href$=".rar"],a[href$=".mov"],a[href$=".mp4"],a[href$=".pdf"],a[href$=".mpeg"],.comment-reply-link' ).on( 'click', function(e) {
						$( '.pageloader' ).fadeIn( 'fast' );
					});
				}
			}
		},

		/*
		*   offCanvas area
		*/
		offCanvas: function( selector, click ) {
			var parent  = selector.parent(),
				area    = selector.next(),
				layout  = $('#layout'),
				overlay = '.cz_overlay',
				isRight, i;

			if ( area.length ) {
				var area = area.clone(),
					isRight = area.hasClass( 'inview_right' ),
					new_class = area.hasClass('sf-menu') ? 'sf-menu offcanvas_area' : 'offcanvas_area offcanvas_original';

				body.prepend( area.removeClass().addClass( 'sidebar_offcanvas_area' ).addClass( new_class + ( isRight ? ' inview_right' : ' inview_left' ) ) );
				var area_w = area.width() + 80;

				$( '.sub-menu', area ).hide();
			} else {
				return;
			}

			selector.on( 'click', function(e) {
				if ( area.hasClass( 'active_offcanvas' ) ) {
					body.trigger( 'click' );
				} else {
					body.addClass( 'active_offcanvas' + ( isRight ? '' : ' cz_offcanvas_left' ) );
					area.addClass( 'active_offcanvas' );
					$( overlay ).fadeIn();
					e.stopPropagation();
				}
			});

			if ( click ) {
				selector.trigger( 'click' );
			}

			area.on( 'click', function(e) {
				e.stopPropagation();
			});

			// reCall anchors
			this.menu_anchor();

			// Close icon
			area.before( '<i class="fa czico-198-cancel offcanvas-close"></i>' );
			area.prev( '.offcanvas-close' ).css( ( isRight ? 'right' : 'left' ), area.outerWidth() ).on( 'click', function(e) {
				body.trigger( 'click' );
			});

			body.on( 'click', function(e) {
				if ( $( '.active_offcanvas' ).length && e.target.className.indexOf( 'fa' ) !== 0 ) {
					body.removeClass( 'active_offcanvas' );
					area.removeClass( 'active_offcanvas' );
					
					$( overlay ).fadeOut();
					setTimeout(function() {
						wind.trigger( 'resize' );
					}, 1500 );
				}
			});

			// reload codevzplus script
			if ( typeof cz_scripts.cp != 'undefined' && ! area.hasClass( 'cz_plus_done' ) ) {
				i = document.createElement("script");
				i.type = "text/javascript";
				i.src = cz_scripts.cp;
				document.getElementsByTagName("body")[0].appendChild(i);
				area.addClass( 'cz_plus_done' );
			}
		},

		/*
		*   Show one by one with delay
		*/
		showOneByOne: function( e, s, d ) {
			var e = ( d == 'left' ) ? $( e.get().reverse() ) : e,
				b = ( d == 'left' ) ? {opacity:0,left:10} : {opacity: 0,left:-10};

			e.css( b ).each(function( i ) {
				$( this ).delay( s * i ).animate({opacity:1,left:0});
			});
		},

		/*
		*   Header shape size
		*/
		header_shape: function() {
			$( 'div[class*="cz_row_shape_"]' ).codevz( 'row_shape', function() {
				var en = $( this ), cls, css, hei;
				Codevz.heightChanged( en, function() {
					cls = en.attr( 'class' ) || 'cz_no_class',
					cls = '.' + cls.replace(/  /g, '.').replace(/ /g, '.'),
					hei = en.height();

					if ( ! $( '> style', en ).length ) {
						en.append('<style></style>');
					}
					$( '> style', en ).html( cls + ' .row:before,' + cls + ' .row:after{width:' + hei + 'px}.elms_row ' + cls + ':before, .elms_row ' + cls + ':after{width:' + hei + 'px}' );
				});
			});
		},

	};
})(jQuery);

jQuery(document).ready(function($) {
	Codevz.init();
});