/**
 * Header behaviour: mobile menu, header search, and the nav dropdowns.
 *
 * Dropdowns open on hover via CSS alone, so they still work with JavaScript
 * unavailable. This adds click/keyboard operation on top, which is what
 * touch devices and keyboard users need.
 */
( function () {
	document.addEventListener( 'DOMContentLoaded', function () {
		setupToggle( '.menu-toggle', 'mobile-menu' );
		setupToggle( '.search-toggle', 'mpc-search-form', function () {
			var input = document.getElementById( 'mpc-search-input' );
			if ( input ) {
				input.focus();
			}
		} );
		setupDropdowns();
	} );

	/**
	 * Wire a button that shows/hides a panel by id.
	 */
	function setupToggle( selector, panelId, onOpen ) {
		var button = document.querySelector( selector );
		var panel = document.getElementById( panelId );
		if ( ! button || ! panel ) {
			return;
		}

		button.addEventListener( 'click', function () {
			var expanded = button.getAttribute( 'aria-expanded' ) === 'true';
			button.setAttribute( 'aria-expanded', String( ! expanded ) );
			panel.hidden = expanded;
			if ( ! expanded && typeof onOpen === 'function' ) {
				onOpen();
			}
		} );
	}

	function setupDropdowns() {
		var toggles = Array.prototype.slice.call(
			document.querySelectorAll( '.header-pill .dropdown-toggle' )
		);
		if ( ! toggles.length ) {
			return;
		}

		function closeAll( except ) {
			toggles.forEach( function ( t ) {
				if ( t === except ) {
					return;
				}
				t.setAttribute( 'aria-expanded', 'false' );
				var panel = panelFor( t );
				if ( panel ) {
					panel.classList.remove( 'is-open' );
				}
			} );
		}

		toggles.forEach( function ( toggle ) {
			toggle.addEventListener( 'click', function ( event ) {
				event.preventDefault();
				event.stopPropagation();

				var panel = panelFor( toggle );
				if ( ! panel ) {
					return;
				}

				var isOpen = toggle.getAttribute( 'aria-expanded' ) === 'true';
				closeAll( toggle );
				toggle.setAttribute( 'aria-expanded', String( ! isOpen ) );
				panel.classList.toggle( 'is-open', ! isOpen );
			} );
		} );

		// Clicking anywhere else, or pressing Escape, closes the menus.
		document.addEventListener( 'click', function ( event ) {
			if ( ! event.target.closest( '.has-dropdown, .mpc-lang' ) ) {
				closeAll();
			}
		} );

		document.addEventListener( 'keydown', function ( event ) {
			if ( 'Escape' !== event.key ) {
				return;
			}
			var open = document.querySelector( '.dropdown-toggle[aria-expanded="true"]' );
			closeAll();
			if ( open ) {
				open.focus();
			}
		} );
	}

	function panelFor( toggle ) {
		var id = toggle.getAttribute( 'aria-controls' );
		return id ? document.getElementById( id ) : null;
	}
} )();
