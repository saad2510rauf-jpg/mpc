/**
 * Homepage flash-sale countdown.
 *
 * The end date comes from the Customizer (Homepage Hero → flash sale end
 * date). If it's missing or already past, PHP never renders the countdown,
 * so this script simply finds nothing to do.
 */
( function () {
	document.addEventListener( 'DOMContentLoaded', function () {
		var root = document.getElementById( 'mpc-countdown' );
		if ( ! root || typeof mpcLanding === 'undefined' || ! mpcLanding.saleEnd ) {
			return;
		}

		var end = new Date( mpcLanding.saleEnd.replace( ' ', 'T' ) ).getTime();
		if ( isNaN( end ) ) {
			return;
		}

		var dEl = document.getElementById( 'mpc-cd-d' );
		var hEl = document.getElementById( 'mpc-cd-h' );
		var mEl = document.getElementById( 'mpc-cd-m' );
		var sEl = document.getElementById( 'mpc-cd-s' );

		function pad( n ) {
			return n < 10 ? '0' + n : '' + n;
		}

		function tick() {
			var diff = end - Date.now();
			if ( diff <= 0 ) {
				root.hidden = true;
				clearInterval( timer );
				return;
			}
			var total = Math.floor( diff / 1000 );
			dEl.textContent = pad( Math.floor( total / 86400 ) );
			hEl.textContent = pad( Math.floor( ( total % 86400 ) / 3600 ) );
			mEl.textContent = pad( Math.floor( ( total % 3600 ) / 60 ) );
			sEl.textContent = pad( total % 60 );
		}

		tick();
		var timer = setInterval( tick, 1000 );
	} );
} )();
