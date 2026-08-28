/**
 * Homepage flash-sale countdown.
 *
 * Two modes, set in the Customizer (Appearance → Customize → Homepage Hero):
 *
 *  1. Fixed end date  — counts down to the same moment for every visitor.
 *  2. Rolling window  — when no fixed date is set, the countdown runs for N
 *     hours from the visitor's first visit. The deadline is stored in
 *     localStorage so it keeps ticking down across page loads and does not
 *     restart on every refresh.
 */
( function () {
	var STORAGE_KEY = 'mpcSaleDeadline';

	document.addEventListener( 'DOMContentLoaded', function () {
		var root = document.getElementById( 'mpc-countdown' );
		if ( ! root || typeof mpcLanding === 'undefined' ) {
			return;
		}

		var end = resolveDeadline();
		if ( ! end ) {
			root.hidden = true;
			return;
		}

		var dEl = document.getElementById( 'mpc-cd-d' );
		var hEl = document.getElementById( 'mpc-cd-h' );
		var mEl = document.getElementById( 'mpc-cd-m' );
		var sEl = document.getElementById( 'mpc-cd-s' );
		var timer;

		function pad( n ) {
			return n < 10 ? '0' + n : '' + n;
		}

		function tick() {
			var diff = end - Date.now();
			if ( diff <= 0 ) {
				dEl.textContent = hEl.textContent = mEl.textContent = sEl.textContent = '00';
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
		timer = setInterval( tick, 1000 );
	} );

	/**
	 * Fixed date wins; otherwise fall back to the persisted rolling deadline.
	 * Returns a timestamp in ms, or null when there is nothing to count to.
	 */
	function resolveDeadline() {
		if ( mpcLanding.saleEnd ) {
			var fixed = new Date( mpcLanding.saleEnd ).getTime();
			if ( ! isNaN( fixed ) && fixed > Date.now() ) {
				return fixed;
			}
		}

		var hours = parseInt( mpcLanding.rollingHours, 10 );
		if ( ! hours || hours <= 0 ) {
			return null;
		}

		var windowMs = hours * 3600 * 1000;
		var stored = readStored();

		// Reuse the stored deadline while it is still in the future and still
		// within the configured window (so shortening the window takes effect).
		if ( stored && stored > Date.now() && stored - Date.now() <= windowMs ) {
			return stored;
		}

		var deadline = Date.now() + windowMs;
		writeStored( deadline );
		return deadline;
	}

	function readStored() {
		try {
			var raw = window.localStorage.getItem( STORAGE_KEY );
			var val = raw ? parseInt( raw, 10 ) : NaN;
			return isNaN( val ) ? null : val;
		} catch ( e ) {
			return null;
		}
	}

	function writeStored( value ) {
		try {
			window.localStorage.setItem( STORAGE_KEY, String( value ) );
		} catch ( e ) {
			// Private mode / storage disabled — the countdown still runs for
			// this page view, it just cannot persist across loads.
		}
	}
} )();
