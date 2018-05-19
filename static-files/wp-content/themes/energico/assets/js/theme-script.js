(function( $ ) {
	"use strict";

	CherryJsCore.utilites.namespace( 'theme_script' );
	CherryJsCore.theme_script = {
		init: function() {
			// Document ready event check
			if ( CherryJsCore.status.is_ready ) {
				this.document_ready_render( this );
			} else {
				CherryJsCore.variable.$document.on( 'ready', this.document_ready_render.bind( this ) );
			}

			// Windows load event check
			if ( CherryJsCore.status.on_load ) {
				this.page_preloader_init( this );
			} else {
				CherryJsCore.variable.$window.on( 'load', this.page_preloader_init.bind( this ) );
			}
		},

		document_ready_render: function() {
			this.swiper_carousel_init( this );
			this.post_formats_custom_init( this );
			this.navbar_init( this );
			this.subscribe_init( this );
			this.main_menu( this, $( '.main-navigation' ) );
			this.to_top_init( this );
			this.search_trigger( this );
			this.mobile_menu( this );
		},

		search_trigger: function() {
			var toggle = $( ".search_toggle" ),
				container = $( ".top-panel__inner_wrap" ),
				field = $( ".top-panel__inner_wrap input.search-field" );

			toggle.on( "click", function() {
				setTimeout( function() {
					field.focus();
				}, 300 );
				container.toggleClass( "srch-on" );
			} ).on( 'click touchstart touchend', function( event ) {
				event.stopPropagation();
			} );

			$( document ).on( "click", function( event ) {
				if ( $( event.target ).closest( toggle ).length || $( event.target ).closest( container ).length )
					return;

				if ( container.hasClass( "srch-on" ) )
					container.removeClass( "srch-on" );

				event.stopPropagation();
			} );

		},

		swiper_carousel_init: function( self ) {

			// Enable swiper carousels
			jQuery( '.energico-carousel' ).each( function() {
				var swiper = null,
					uniqId = jQuery( this ).data( 'uniq-id' ),
					slidesPerView = parseFloat( jQuery( this ).data( 'slides-per-view' ) ),
					slidesPerGroup = parseFloat( jQuery( this ).data( 'slides-per-group' ) ),
					slidesPerColumn = parseFloat( jQuery( this ).data( 'slides-per-column' ) ),
					spaceBetweenSlides = parseFloat( jQuery( this ).data( 'space-between-slides' ) ),
					durationSpeed = parseFloat( jQuery( this ).data( 'duration-speed' ) ),
					swiperLoop = jQuery( this ).data( 'swiper-loop' ),
					freeMode = jQuery( this ).data( 'free-mode' ),
					grabCursor = jQuery( this ).data( 'grab-cursor' ),
					mouseWheel = jQuery( this ).data( 'mouse-wheel' ),
					breakpointsSettings = {
						1200: {
							slidesPerView: Math.floor( slidesPerView * 0.75 ),
							spaceBetween: Math.floor( spaceBetweenSlides * 0.75 )
						},
						992: {
							slidesPerView: Math.floor( slidesPerView * 0.5 ),
							spaceBetween: Math.floor( spaceBetweenSlides * 0.5 )
						},
						769: {
							slidesPerView: ( 0 !== Math.floor( slidesPerView * 0.25 ) ) ? Math.floor( slidesPerView * 0.25 ) : 1
						},
					};

				if ( 1 == slidesPerView ) {
					breakpointsSettings = {}
				}

				var swiper = new Swiper( '#' + uniqId, {
						slidesPerView: slidesPerView,
						slidesPerGroup: slidesPerGroup,
						slidesPerColumn: slidesPerColumn,
						spaceBetween: spaceBetweenSlides,
						speed: durationSpeed,
						loop: swiperLoop,
						freeMode: freeMode,
						grabCursor: grabCursor,
						mousewheelControl: mouseWheel,
						paginationClickable: true,
						nextButton: '#' + uniqId + '-next',
						prevButton: '#' + uniqId + '-prev',
						pagination: '#' + uniqId + '-pagination',
						onInit: function() {
							$( '#' + uniqId + '-next' ).css( { 'display': 'block' } );
							$( '#' + uniqId + '-prev' ).css( { 'display': 'block' } );
						},
						breakpoints: breakpointsSettings
					}
				);
			} );
		},

		post_formats_custom_init: function( self ) {
			CherryJsCore.variable.$document.on( 'cherry-post-formats-custom-init', function( event ) {

				if ( 'slider' !== event.object ) {
					return;
				}

				var uniqId = '#' + event.item.attr( 'id' ),
					swiper = new Swiper( uniqId, {
						pagination: uniqId + ' .swiper-pagination',
						paginationClickable: true,
						nextButton: uniqId + ' .swiper-button-next',
						prevButton: uniqId + ' .swiper-button-prev',
						spaceBetween: 30,
						onInit: function() {
							$( uniqId + ' .swiper-button-next' ).css( { 'display': 'block' } );
							$( uniqId + ' .swiper-button-prev' ).css( { 'display': 'block' } );
						},
					} );

				event.item.data( 'initalized', true );
			} );

			var items = [];

			$( '.mini-gallery .post-thumbnail__link' ).on( 'click', function( event ) {
				event.preventDefault();

				$( this ).parents( '.mini-gallery' ).find( '.post-gallery__slides > a[href]' ).each( function() {
					items.push( {
						src: $( this ).attr( 'href' ),
						type: 'image'
					} );
				} );

				$.magnificPopup.open( {
					items: items,
					gallery: {
						enabled: true
					}
				} );
			} );
		},

		navbar_init: function( self ) {
			$( window ).on( 'load', function() {

				var $navbar = $( '.main-navigation' );

				if ( !$.isFunction( jQuery.fn.stickUp ) || !$navbar.length ) {
					return !1;
				}

				$navbar.stickUp( {
					correctionSelector: '#wpadminbar',
					listenSelector: '.listenSelector',
					pseudo: true,
					active: true
				} );
				CherryJsCore.variable.$document.trigger( 'scroll.stickUp' );

			} );
		},

		subscribe_init: function( self ) {
			CherryJsCore.variable.$document.on( 'click', '.subscribe-block__submit', function( event ) {

				event.preventDefault();

				var $this = $( this ),
					form = $this.parents( 'form' ),
					nonce = form.find( 'input[name="energico_subscribe"]' ).val(),
					mail_input = form.find( 'input[name="subscribe-mail"]' ),
					mail = mail_input.val(),
					error = form.find( '.subscribe-block__error' ),
					success = form.find( '.subscribe-block__success' ),
					hidden = 'hidden';

				if ( '' == mail ) {
					mail_input.addClass( 'error' );
					return !1;
				}

				if ( $this.hasClass( 'processing' ) ) {
					return !1;
				}

				$this.addClass( 'processing' );
				error.empty();

				if ( !error.hasClass( hidden ) ) {
					error.addClass( hidden );
				}

				if ( !success.hasClass( hidden ) ) {
					success.addClass( hidden );
				}

				$.ajax( {
					url: energico.ajaxurl,
					type: 'post',
					dataType: 'json',
					data: {
						action: 'energico_subscribe',
						mail: mail,
						nonce: nonce
					},
					error: function() {
						$this.removeClass( 'processing' );
					}
				} ).done( function( response ) {

					$this.removeClass( 'processing' );

					if ( true === response.success ) {
						success.removeClass( hidden );
						mail_input.val( '' );
						return 1;
					}

					error.removeClass( hidden ).html( response.data.message );
					return !1;

				} );

			} )
		},

		main_menu: function( self, $mainNavigation ) {

			var transitionend = 'transitionend oTransitionEnd webkitTransitionEnd',
				moreMenuContent = '&middot;&middot;&middot;',
				imgurl = '',
				srcset = '',
				hasimg = false,
				hasicon = false,
				hasprop = Object.prototype.hasOwnProperty,
				$menuToggle = $( '.menu-toggle[aria-controls="main-menu"]' ),
				$body = $( 'body' ),
				$parentNode,
				menuItem,
				subMenu,
				index = -1;

			if ( hasprop.call( window, 'energico' ) &&
				hasprop.call( window.energico, 'more_button_options' ) &&
				hasprop.call( window.energico.more_button_options, 'more_button_type' ) ) {
				switch ( window.energico.more_button_options.more_button_type ) {
					case 'image':
						imgurl = window.energico.more_button_options.more_button_image_url;
						if ( window.energico.more_button_options.retina_more_button_image_url ) {
							srcset = ' srcset="' + window.energico.more_button_options.retina_more_button_image_url + ' 2x"';
						}
						moreMenuContent = '<img src="' + imgurl + '"' + srcset + ' alt="' + moreMenuContent + '">';
						hasimg = true;
						break;
					case 'icon':
						moreMenuContent = '<i class="fa ' + window.energico.more_button_options.more_button_icon + '"></i>';
						hasicon = true;
						break;
					case 'text':
					default:
						moreMenuContent = window.energico.more_button_options.more_button_text || moreMenuContent;
						hasimg = false;
						hasicon = false;
						break;
				}
			}

			$mainNavigation.superGuacamole( {
				threshold: 768, // Minimal menu width, when this plugin activates
				minChildren: 3, // Minimal visible children count
				childrenFilter: '.menu-item', // Child elements selector
				menuTitle: moreMenuContent, // Menu title
				menuUrl: '#',
				templates: {
					menu: '<li id="%5$s" class="%1$s' + ( hasimg ? ' super-guacamole__menu-with-image' : '' ) +
					( hasicon ? ' super-guacamole__menu-with-icon' : '' ) + '"><a href="%2$s">%3$s</a><ul class="sub-menu">%4$s</ul></li>',
					child_wrap: '<ul class="%1$s">%2$s</ul>',
					child: '<li id="%5$s" class="%1$s"><a href="%2$s">%3$s</a><ul class="sub-menu">%4$s</ul></li>'
				}
			} );

			function hideSubMenu( menuItem, $event ) {
				var subMenus = $( '.sub-menu', menuItem );

				menuItem
					.removeData( 'index' )
					.removeClass( 'menu-hover' );

				subMenus.addClass( 'in-transition' );

				subMenus
					.one( transitionend, function() {
						subMenus.removeClass( 'in-transition' );
					} );
			}

			function handleMenuItemHover( $event ) {
				menuItem = $( $event.target ).parents( '.menu-item' );
				subMenu = menuItem.children( '.sub-menu' ).first();
				var subMenus = $( '.sub-menu', menuItem ),
					maxWidth,
					subMenuOffset;

				if ( !menuItem.hasClass( 'menu-item-has-children' ) ) {
					menuItem = $event.target.tagName === 'LI' ?
						$( $event.target ) :
						$( $event.target ).parents().filter( '.menu-item' );
				}

				switch ( $event.type ) {
					case 'mouseenter':
					case 'mouseover':
						if ( 0 < subMenu.length ) {
							maxWidth = $body.outerWidth( true );
							subMenuOffset = subMenu.offset().left + subMenu.outerWidth( true );
							menuItem.addClass( 'menu-hover' );
							subMenus.addClass( 'in-transition' );
							if ( maxWidth <= subMenuOffset ) {
								subMenu.addClass( 'left-side' );
								subMenu.find( '.sub-menu' ).addClass( 'left-side' );
							} else if ( 0 > subMenu.offset().left ) {
								subMenu.removeClass( 'left-side' );
								subMenu.find( '.sub-menu' ).removeClass( 'left-side' );
							}
							subMenus
								.one( transitionend, function() {
									subMenus.removeClass( 'in-transition' );
								} );
						}
						break;
					case 'mouseleave':
						hideSubMenu( menuItem, $event );
						break;
				}
			}

			CherryJsCore.variable.$window.on( 'orientationchange resize', function() {
				$( '.menu-item', $mainNavigation ).removeClass( 'menu-hover' );
				$( '.sub-menu.left-side', $mainNavigation ).removeClass( 'left-side' );
			} );

			$mainNavigation.on( 'mouseenter mouseover mouseleave', '.menu-item', handleMenuItemHover );

			function doubleClickMenu( $jqEvent ) {
				var menuIndex;

				$parentNode = $( this );
				menuIndex = $parentNode.index();

				if ( menuIndex !== parseInt( $parentNode.data( 'index' ), 10 ) ) {
					$jqEvent.preventDefault();
				}

				$parentNode.data( 'index', menuIndex );
			}

			// Check if touch events supported
			if ( 'ontouchend' in window ) {

				// Reset index on touchend event
				CherryJsCore.variable.$document.on( 'touchend', function( $jqEvent ) {
					$parentNode = $( $jqEvent.target ).parents().filter( '.menu-item:first' );

					if ( $parentNode.hasClass( 'menu-hover' ) === false ) {
						hideSubMenu( $parentNode, $jqEvent );

						index = $parentNode.data( 'index' );

						if ( index ) {
							$parentNode.data( 'index', parseInt( index, 10 ) - 1 );
						}
					}
				} );
			}

			$menuToggle.on( 'click', function( $event ) {
				$event.preventDefault();

				setTimeout( function() {
					if ( !$mainNavigation.hasClass( 'animate' ) ) {
						$mainNavigation.addClass( 'animate' );
					}
					$mainNavigation.toggleClass( 'show' );
					$( 'html' ).toggleClass( 'mobile-menu-active' );
				}, 10 );

				$menuToggle.attr( 'aria-expanded', !$menuToggle.hasClass( 'toggled' ) );
				$menuToggle.toggleClass( 'toggled' );
			} );
		},

		mobile_menu: function( self ) {
			var $mainNavigation = $( '.main-navigation' ),
				$menuToggle = $( '.menu-toggle[aria-controls="main-menu"]' );

			$mainNavigation
				.find( 'li.menu-item-has-children > a' )
				.after( '<a href="#" class="sub-menu-toggle"></a>' );

			/**
			 * Debounce the function call
			 *
			 * @param  {number}   threshold The delay.
			 * @param  {Function} callback  The function.
			 */
			function debounce( threshold, callback ) {
				var timeout;

				return function debounced( $event ) {
					function delayed() {
						callback( $event );
						timeout = null;
					}

					if ( timeout ) {
						clearTimeout( timeout );
					}

					timeout = setTimeout( delayed, threshold );
				};
			}

			/**
			 * Resize event handler.
			 *
			 * @param {jqEvent} jQuery event.
			 */
			function resizeHandler( $event ) {
				var $window = CherryJsCore.variable.$window,
					width = $window.outerWidth( true );

				if ( 768 <= width ) {
					$mainNavigation.removeClass( 'mobile-menu' );
				} else {
					$mainNavigation.addClass( 'mobile-menu' );
				}
			}

			/**
			 * Toggle sub-menus.
			 *
			 * @param  {jqEvent} $event jQuery event.
			 */
			function toggleSubMenuHandler( $event ) {
				var $subMenu = $( this );

				$subMenu.toggleClass( 'active' );
				$subMenu.parent().toggleClass( 'sub-menu-open' );
			}

			/**
			 * Toggle menu.
			 *
			 * @param  {jqEvent} $event jQuery event.
			 */
			function toggleMenuHandler( $event ) {
				var $toggle = $( this );

				$event.preventDefault();

				$mainNavigation.toggleClass( 'active' );

				if ( $toggle.hasClass( 'active' ) ) {
					$toggle.removeClass( 'active' );
					$mainNavigation.find( '.sub-menu-open' ).removeClass( 'sub-menu-open' );
				}
			}

			resizeHandler();
			CherryJsCore.variable.$window.on( 'resize orientationchange', debounce( 500, resizeHandler ) );
			$mainNavigation.on( 'click', '.sub-menu-toggle', toggleSubMenuHandler );
			$menuToggle.on( 'click', toggleMenuHandler );
		},


		page_preloader_init: function( self ) {

			if ( $( '.page-preloader-cover' )[0] ) {
				$( '.page-preloader-cover' ).delay( 500 ).fadeTo( 500, 0, function() {
					$( this ).remove();
				} );
			}
		},

		to_top_init: function( self ) {
			if ( $.isFunction( jQuery.fn.UItoTop ) ) {
				$().UItoTop( {
					text: energico.labels.totop_button,
					scrollSpeed: 600
				} );
			}
		}
	}
	CherryJsCore.theme_script.init();
}( jQuery ));
