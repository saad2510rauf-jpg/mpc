/**
 * Homepage flash-sale countdown.
 *
 * The deadline is calculated server-side in the site's timezone and handed
 * over as an ISO timestamp, so every visitor counts down to the same moment
 * and nothing is stored per-browser.
 *
 * Configured under Appearance → Customize → Homepage Hero:
 *
 *  - Recurring weekly deadline — the same weekday and time each week
 *    (`repeatDays` is 7, so a page left open past zero rolls to next week).
 *  - One fixed end date — counts down once, then hides for good.
 *  - No countdown — PHP renders nothing and this script does nothing.
 */
( function () {
	document.addEventListener( 'DOMContentLoaded', function () {
		var root = document.getElementById( 'mpc-countdown' );
		if ( ! root || typeof mpcLanding === 'undefined' || ! mpcLanding.saleEnd ) {
			return;
		}

		var end = new Date( mpcLanding.saleEnd ).getTime();
		if ( isNaN( end ) ) {
			root.hidden = true;
			return;
		}

		var repeatMs = ( parseInt( mpcLanding.repeatDays, 10 ) || 0 ) * 86400000;
		var badge = document.querySelector( '.mpc-flash-badge' );
		var dEl = document.getElementById( 'mpc-cd-d' );
		var hEl = document.getElementById( 'mpc-cd-h' );
		var mEl = document.getElementById( 'mpc-cd-m' );
		var sEl = document.getElementById( 'mpc-cd-s' );
		var timer;

		function pad( n ) {
			return n < 10 ? '0' + n : '' + n;
		}

		function expire() {
			clearInterval( timer );
			root.hidden = true;
			if ( badge ) {
				badge.hidden = true;
			}
		}

		function tick() {
			var diff = end - Date.now();

			if ( diff <= 0 ) {
				if ( ! repeatMs ) {
					expire();
					return;
				}
				// Roll to the next occurrence. The server recalculates the
				// exact deadline on the next page load, which also corrects
				// for any daylight-saving shift.
				while ( diff <= 0 ) {
					end += repeatMs;
					diff = end - Date.now();
				}
			}

			var total = Math.floor( diff / 1000 );
			dEl.textContent = pad( Math.floor( total / 86400 ) );
			hEl.textContent = pad( Math.floor( ( total % 86400 ) / 3600 ) );
			mEl.textContent = pad( Math.floor( ( total % 3600 ) / 60 ) );
			sEl.textContent = pad( total % 60 );
		}

		tick();
		timer = setInterval( tick, 1000 );
	} );
} )();
