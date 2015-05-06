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

	function numberCounterAnimateValue(element, start, end, speed) {
		// Assumes integer values for start, end and speed and DOM element for the element parameter

		// The number counter animation has already been started. So skip it!
		if ( $(element).data('numberCounterFinished') ) {
			return null;
		}

		var range = end - start;
		// No timer shorter than 50ms (not really visible any way)
		var minTimer = 50;
		// Calculate step time to show all intermediate values
		var stepTime = Math.abs(Math.floor(speed / range));

		// Never go below minTimer
		stepTime = Math.max(stepTime, minTimer);

		// Get current time and calculate desired end time
		var startTime = new Date().getTime();
		var endTime = startTime + speed;
		var timer;

		function run() {
			var now = new Date().getTime();
			var remaining = Math.max((endTime - now) / speed, 0);
			var value = Math.round(end - (remaining * range));
			element.innerHTML = value;
			if (value == end) {
				clearInterval(timer);
			}
		}

		timer = setInterval(run, stepTime);
		run();
	}

	// Function for detecting if the element is visible on screen
	function numberCounterIsScrolledIntoView(element) {
		var docViewTop = $(window).scrollTop();
    var docViewBottom = docViewTop + $(window).height();

    var elemTop = $(element).offset().top;
    var elemBottom = elemTop + $(element).height();

    return ((elemBottom <= docViewBottom) && (elemTop >= docViewTop))
	}

	// Get all number counter widgets
	var numberCounterWidget = $( '.widget-number-counters' );

	// Run numberCounterAnimateValue for each counter, but just once, when the widget is visible on the screen
	$(document).scroll( function(){
		numberCounterWidget.each(function(index, el) {
		if( numberCounterIsScrolledIntoView( $(el) ) ) {
			$(el).find('.number-counter__number').each(function(childIndex, childEl) {
				numberCounterAnimateValue(childEl, 0, parseInt(childEl.dataset.to, 10), parseInt(el.dataset.speed, 10));
				$(childEl).data('numberCounterFinished', true);
			});
		}
		});
	});

} );