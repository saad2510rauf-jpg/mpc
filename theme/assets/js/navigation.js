/**
 * Mobile menu toggle.
 */
( function () {
	document.addEventListener( 'DOMContentLoaded', function () {
		var toggle = document.querySelector( '.menu-toggle' );
		var menu = document.getElementById( 'mobile-menu' );
		if ( ! toggle || ! menu ) {
			return;
		}
		toggle.addEventListener( 'click', function () {
			var expanded = toggle.getAttribute( 'aria-expanded' ) === 'true';
			toggle.setAttribute( 'aria-expanded', String( ! expanded ) );
			menu.hidden = expanded;
		} );
	} );
} )();
