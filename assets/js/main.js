/* global Modernizr */

// Config
require.config( {
	paths: {
		jquery:              'assets/js/fix.jquery',
		underscore:          'assets/js/fix.underscore',
		bootstrapCarousel:   'bower_components/bootstrap-sass/assets/javascripts/bootstrap/carousel',
	},
	shim: {
		bootstrapCarousel: {
			deps: [
				'jquery'
			]
		}
	}
} );

require.config( {
	baseUrl: PWVars.pathToPlugin
} );

require( [
	'jquery',
	'underscore',
	'bootstrapCarousel'
], function( $, _ ) {
	'use strict';

	// Get all number-counter__number elements inside the number counter widget
	var obj = $( '.widget-number-counters .number-counter__number' );

	obj.each(function( index, el ) {
		// No timer shorter than 50ms (not really visible any way)
		var minTimer = 50;

		// Get data from the element
		var speed = parseInt( el.dataset.speed );
		var countTo = parseInt( el.dataset.to );

		// Calculate the step time to show all intermediate values
		var stepTime = Math.abs( Math.floor( speed / countTo ) );

		// Never go below minTimer
		stepTime = Math.max( stepTime, minTimer );

		// Get current time and calculate desired end time
		var startTime = new Date().getTime();
		var endTime = startTime + speed;
		var timer;

		function run() {
			var now = new Date().getTime();
			var remaining = Math.max( ( endTime - now ) / speed, 0 );
			var value = Math.round( countTo - ( remaining * countTo ) );
			el.innerHTML = value;
			if ( value == countTo ) {
				clearInterval( timer );
			}
		}

		timer = setInterval( run, stepTime );
		run();
	});
} );