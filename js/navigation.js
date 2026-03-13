/**
 * File navigation.js.
 *
 * Handles toggling the navigation menu for small screens and enables TAB key
 * navigation support for dropdown menus.
 */
( function() {
	const siteNavigation = document.getElementById( 'site-navigation' );

	// Return early if the navigation doesn't exist.
	if ( ! siteNavigation ) {
		return;
	}

	const button = siteNavigation.getElementsByTagName( 'button' )[ 0 ];

	// Return early if the button doesn't exist.
	if ( 'undefined' === typeof button ) {
		return;
	}

	const menu = siteNavigation.getElementsByTagName( 'ul' )[ 0 ];

	// Hide menu toggle button if menu is empty and return early.
	if ( 'undefined' === typeof menu ) {
		button.style.display = 'none';
		return;
	}

	if ( ! menu.classList.contains( 'nav-menu' ) ) {
		menu.classList.add( 'nav-menu' );
	}

	const submenuButtons = menu.querySelectorAll( '.menu-item-has-children > .submenu-toggle, .page_item_has_children > .submenu-toggle' );

	/**
	 * Closes all expanded submenus in the current menu.
	 */
	function closeAllSubmenus() {
		for ( const submenuButton of submenuButtons ) {
			submenuButton.setAttribute( 'aria-expanded', 'false' );
			submenuButton.parentNode.classList.remove( 'focus' );
		}
	}

	// Toggle the .toggled class and the aria-expanded value each time the button is clicked.
	button.addEventListener( 'click', function() {
		siteNavigation.classList.toggle( 'toggled' );

		if ( button.getAttribute( 'aria-expanded' ) === 'true' ) {
			button.setAttribute( 'aria-expanded', 'false' );
			closeAllSubmenus();
		} else {
			button.setAttribute( 'aria-expanded', 'true' );
		}
	} );

	// Remove the .toggled class and set aria-expanded to false when the user clicks outside the navigation.
	document.addEventListener( 'click', function( event ) {
		const isClickInside = siteNavigation.contains( event.target );

		if ( ! isClickInside ) {
			siteNavigation.classList.remove( 'toggled' );
			button.setAttribute( 'aria-expanded', 'false' );
			closeAllSubmenus();
		}
	} );

	// Open/close submenu via dedicated marker button, while parent link remains a real link.
	for ( const submenuButton of submenuButtons ) {
		submenuButton.addEventListener( 'click', function( event ) {
			event.preventDefault();
			event.stopPropagation();

			const menuItem = this.parentNode;
			const parentList = menuItem.parentNode;
			const isExpanded = this.getAttribute( 'aria-expanded' ) === 'true';

			for ( const sibling of parentList.children ) {
				if ( sibling !== menuItem ) {
					let siblingButton = null;
					for ( const child of sibling.children ) {
						if ( child.classList && child.classList.contains( 'submenu-toggle' ) ) {
							siblingButton = child;
							break;
						}
					}
					if ( siblingButton ) {
						siblingButton.setAttribute( 'aria-expanded', 'false' );
					}
					sibling.classList.remove( 'focus' );
				}
			}

			this.setAttribute( 'aria-expanded', isExpanded ? 'false' : 'true' );
			menuItem.classList.toggle( 'focus', ! isExpanded );
		} );
	}

	// Get all the link elements within the menu.
	const links = menu.getElementsByTagName( 'a' );

	// Toggle focus each time a menu link is focused or blurred.
	for ( const link of links ) {
		link.addEventListener( 'focus', function( event ) {
			toggleFocus.call( this, event );
		}, true );
		link.addEventListener( 'blur', function( event ) {
			toggleFocus.call( this, event );
		}, true );
	}

	/**
	 * Sets or removes .focus class on an element.
	 */
	function toggleFocus( event ) {
		if ( event.type === 'focus' || event.type === 'blur' ) {
			const isMobileMenuOpen = siteNavigation.classList.contains( 'toggled' );
			const shouldAddFocus = event.type === 'focus';

			// In mobile menu mode, keep submenu state controlled by dedicated toggle buttons.
			if ( isMobileMenuOpen && ! shouldAddFocus ) {
				return;
			}

			let self = this;
			// Move up through the ancestors of the current link until we hit .nav-menu.
			while ( ! self.classList.contains( 'nav-menu' ) ) {
				// On li elements set/remove .focus deterministically.
				if ( 'li' === self.tagName.toLowerCase() ) {
					self.classList.toggle( 'focus', shouldAddFocus );
				}
				self = self.parentNode;
			}
		}

	}
}() );
