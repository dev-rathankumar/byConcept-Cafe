/**
 * navigation.js
 *
 * Handles toggling the navigation menu for small screens and adds a focus class to parent li's for accessibility.
 */
(function( $ ){

	$.fn.dropdown_menu_toggle = function() {
		
		// Multi level dropdown trigger
		$(this).find('ul.menu li.menu-item-has-children > a').each(function(event){
			var $childIndicator = $('<span class="child-indicator"></span>');

			$childIndicator.on( 'click', function(e){
				e.preventDefault();
				e.stopPropagation();
				if ( $(this).closest('li.menu-item').hasClass('open') ) {
					$(this).closest('li.menu-item').removeClass('open');
				} else {
					$(this).closest('li.menu-item').removeClass('open');
					$(this).closest('li.menu-item').addClass('open');
				}
				return false;
			});
			$(this).append($childIndicator);
		});

	};

	// Add dropdown trigger to handheld navigation
	$('.handheld-navigation').dropdown_menu_toggle();

	if ( jQuery( window ).width() > 1024 ) {
		$('.pizzaro-sidebar-header .primary-navigation ul li.menu-item-has-children, .pizzaro-sidebar-header .secondary-navigation ul li.menu-item-has-children').on('mouseover', function() {
			var $menuItem = $(this),
			$submenuWrapper = $('> ul.sub-menu', $menuItem);

			// grab the menu item's position relative to its positioned parent
			var menuItemPos = $menuItem.offset();

			var $pos_left = menuItemPos.left + Math.round( $menuItem.outerWidth() );
			var $pos_top = menuItemPos.top - $(document).scrollTop();
			var $pos_bottom = $(window).height() - $pos_top - $menuItem.height();

			if( $submenuWrapper.outerHeight() > $pos_bottom ) {
				$pos_top = $pos_top - ( $submenuWrapper.outerHeight() - $pos_bottom );
			}

			// place the submenu in the correct position relevant to the menu item
			if ( $menuItem.css( 'direction' ) === 'rtl' ) {
				$submenuWrapper.css({
					position: 'fixed',
					top: $pos_top,
					right: $pos_left
				});
			} else {
				$submenuWrapper.css({
					position: 'fixed',
					top: $pos_top,
					left: $pos_left
				});
			}
		});
	}

	$( window ).load( function() {

		$( '.phm-close' ).on( 'click', function() {
			$( '.menu-toggle' ).trigger( 'click' );
		} );

		$( document ).click( function( event ) {
			var menuContainer = $( '.main-navigation' );

			if ( $( '.main-navigation' ).hasClass( 'toggled' ) ) {
				if ( ! menuContainer.is( event.target ) && 0 === menuContainer.has( event.target ).length ) {
					$( '.menu-toggle' ).trigger( 'click' );
				}
			}
		} );

	} );

})( jQuery );

( function() {
	var container, button, menu;

	container = document.getElementById( 'site-navigation' );
	if ( ! container ) {
		return;
	}

	button = container.getElementsByTagName( 'button' )[0];
	if ( 'undefined' === typeof button ) {
		return;
	}

	menu = container.getElementsByTagName( 'ul' )[0];

	// Hide menu toggle button if menu is empty and return early.
	if ( 'undefined' === typeof menu ) {
		button.style.display = 'none';
		return;
	}

	menu.setAttribute( 'aria-expanded', 'false' );

	if ( -1 === menu.className.indexOf( 'nav-menu' ) ) {
		menu.className += ' nav-menu';
	}

	button.onclick = function() {
		if ( -1 !== container.className.indexOf( 'toggled' ) ) {
			container.className = container.className.replace( ' toggled', '' );
			button.setAttribute( 'aria-expanded', 'false' );
			menu.setAttribute( 'aria-expanded', 'false' );
		} else {
			container.className += ' toggled';
			button.setAttribute( 'aria-expanded', 'true' );
			menu.setAttribute( 'aria-expanded', 'true' );
		}
	};

	// Add focus class to li
	jQuery( '.main-navigation, .secondary-navigation' ).find( 'a' ).on( 'focus.pizzaro blur.pizzaro', function() {
		jQuery( this ).parents().toggleClass( 'focus' );
	});

	// Add focus to cart dropdown
	jQuery( window ).load( function() {
		jQuery( '.site-header-cart' ).find( 'a' ).on( 'focus.pizzaro blur.pizzaro', function() {
			jQuery( this ).parents().toggleClass( 'focus' );
		});
	});

	// Add class to footer search when clicked
	jQuery( window ).load( function() {
		jQuery( '.pizzaro-handheld-footer-bar .search > a' ).click( function(e) {
			jQuery( this ).parent().toggleClass( 'active' );
			e.preventDefault();
		});
	});
} )();
