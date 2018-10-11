/* globals jQuery, soPremiumTooltipOptions */

jQuery( function ( $ ) {
	$( '.so-widget-sow-image, .so-widget-sow-image-grid, .so-widget-sow-simple-masonry' )
	.each( function ( index, element ) {
		var $wrapper = $( element );
		if ( ! $wrapper.data( 'tooltip-enabled' ) ) {
			return;
		}
		var theme = $wrapper.data( 'tooltip-theme' );
		
		$wrapper.find( 'img' ).each( function ( index, element ) {
			
			var $img = $( element );
			var tooltipText = $img.attr( 'title' );
			if ( tooltipText ) {
				$img.attr( 'title', null );
				var $tooltip = $( '<div class="so-premium-tooltip">' + tooltipText + '<div class="callout"></div></div>' );
				$tooltip.css( 'visibility', 'hidden' );
				$tooltip.addClass( 'theme-' + theme );
				$tooltip.css( 'max-width', $img.outerWidth() );
				var $imgContainer = $img.parent();
				var isMasonryImage = $imgContainer.is( '.sow-masonry-grid-item' );
				if ( isMasonryImage ) {
					$imgContainer.parent().append( $tooltip );
				} else {
					$imgContainer.append( $tooltip );
				}
				
				var tooltipRelativeOffset = { top: 0, left: 0 };
				var $callout = $tooltip.find( '.callout' );
				
				var updateTooltipPosition = function () {
					var imgPosition = isMasonryImage ? $imgContainer.position() : $img.position();
					var tooltipPosition = {
						top: imgPosition.top + tooltipRelativeOffset.top - $tooltip.outerHeight(),
						left: imgPosition.left + tooltipRelativeOffset.left - ( $tooltip.outerWidth() * 0.5 )
					};
					$tooltip.css( tooltipPosition );
				};
				
				var showTooltip = function () {
					updateTooltipPosition();
					$tooltip.fadeIn( 100 );
				};
				if ( soPremiumTooltipOptions.position === 'follow_cursor' ) {
					$tooltip.css( 'pointer-events', 'none' );
				}
				
				var showTimeoutId;
				$tooltip.hide();
				$tooltip.css( 'visibility', 'visible' );
				$imgContainer.on( soPremiumTooltipOptions.show_trigger, function ( event ) {
					var tooltipElement = $tooltip.get( 0 );
					// Make sure the show action isn't triggered when mouse moves from the image to the tooltip or back.
					if ( $tooltip.is( ':visible' ) || event.target === tooltipElement || event.relatedTarget === tooltipElement || event.relatedTarget === $img.get( 0 ) ) {
						return false;
					}
					var $sizingElement = isMasonryImage ? $imgContainer : $img;
					$callout.removeClass( 'bottom' ).addClass( 'top' );
					$callout.css( 'pointer-events', 'none' );
					var calloutOffset = $callout.outerHeight() * 0.5;
					switch ( soPremiumTooltipOptions.position ) {
						case 'follow_cursor':
							$imgContainer.on( 'mousemove', function( event ) {
								tooltipRelativeOffset.top = event.offsetY - calloutOffset;
								var imgWidth = $img.outerWidth();
								var imgContainerWidth = $imgContainer.outerWidth();
								// For cases where the image overflows it's container, e.g. masonry items, we need to subtract the overflow.
								var imgOverflow = ( imgWidth > imgContainerWidth ) ? ( imgWidth - imgContainerWidth ) * 0.5 : 0;
								tooltipRelativeOffset.left = event.offsetX - imgOverflow;
								updateTooltipPosition();
							} );
							break;
						case 'center':
							tooltipRelativeOffset.top = ( $sizingElement.outerHeight() * 0.5 ) - calloutOffset;
							tooltipRelativeOffset.left = $sizingElement.outerWidth() * 0.5;
							break;
						case 'top':
							tooltipRelativeOffset.top = 0 - calloutOffset;
							tooltipRelativeOffset.left = $sizingElement.outerWidth() * 0.5;
							break;
						case 'bottom':
							$callout.removeClass( 'top' ).addClass( 'bottom' );
							tooltipRelativeOffset.top = $sizingElement.outerHeight() + $tooltip.outerHeight() + calloutOffset;
							tooltipRelativeOffset.left = $sizingElement.outerWidth() * 0.5;
							break;
					}
					if ( soPremiumTooltipOptions.show_trigger === 'mouseover' &&
						soPremiumTooltipOptions.show_delay && soPremiumTooltipOptions.show_delay > 0 ) {
						if ( showTimeoutId ) {
							clearTimeout( showTimeoutId );
						}
						showTimeoutId = setTimeout( function() {
							showTimeoutId = null;
							showTooltip();
						}, soPremiumTooltipOptions.show_delay );
					} else {
						showTooltip();
					}
					
					if ( soPremiumTooltipOptions.hide_trigger === 'click' ) {
						var hideTooltip = function () {
							$tooltip.fadeOut( 100 );
							$( window ).off( 'click', hideTooltip );
						};
						setTimeout( function () {
							$( window ).on( 'click', hideTooltip );
						}, 100 );
					}
				} );
				
				if ( soPremiumTooltipOptions.hide_trigger === 'mouseout' ) {
					$imgContainer.on( 'mouseout', function () {
						if ( showTimeoutId ) {
							clearTimeout( showTimeoutId );
						}
						// Make sure the hide action isn't triggered when mouse moves from the image to the tooltip.
						if ( event.relatedTarget !== $tooltip.get( 0 ) &&
							! $.contains( $imgContainer.get( 0 ), event.relatedTarget ) ) {
							$tooltip.fadeOut( 100 );
							$imgContainer.off( 'mousemove', updateTooltipPosition );
						}
					} );
					if ( isMasonryImage ) {
						$tooltip.on( 'mouseout', function ( event ) {
							if ( event.relatedTarget !== $img.get( 0 ) ) {
								$tooltip.fadeOut( 100 );
								$imgContainer.off( 'mousemove', updateTooltipPosition );
							}
						} );
					}
				}
			}
		} );
	} );
} );
