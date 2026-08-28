/**
 * Mobile menu + header search toggles.
 */
( function () {
	document.addEventListener( 'DOMContentLoaded', function () {
		var menuToggle = document.querySelector( '.menu-toggle' );
		var menu = document.getElementById( 'mobile-menu' );
		if ( menuToggle && menu ) {
			menuToggle.addEventListener( 'click', function () {
				var expanded = menuToggle.getAttribute( 'aria-expanded' ) === 'true';
				menuToggle.setAttribute( 'aria-expanded', String( ! expanded ) );
				menu.hidden = expanded;
			} );
		}

		var searchToggle = document.querySelector( '.search-toggle' );
		var searchForm = document.getElementById( 'mpc-search-form' );
		if ( searchToggle && searchForm ) {
			searchToggle.addEventListener( 'click', function () {
				var expanded = searchToggle.getAttribute( 'aria-expanded' ) === 'true';
				searchToggle.setAttribute( 'aria-expanded', String( ! expanded ) );
				searchForm.hidden = expanded;
				if ( ! expanded ) {
					var input = document.getElementById( 'mpc-search-input' );
					if ( input ) {
						input.focus();
					}
				}
			} );
		}
	} );
} )();
