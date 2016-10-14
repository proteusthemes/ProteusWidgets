/**
 * Number Counter Widget
 */
define( ['jquery', 'underscore'], function( $, _ ){
	'use strict';

	var config = {
		eventNS:              'widgetCounter',
		numberContainerClass: '.js-number',
	};

	var NumberCounter = function( $widgetElement ){
		this.$widgetElement = $widgetElement;
		this.uniqueNS = _.uniqueId( config.eventNS );

		this.registerListeners();

		$( window ).trigger( 'scroll.' + this.uniqueNS );

		return this;
	};

	// Helper: Add leading zeros for counting numbers
	var leadingZeros = function( num, size ) {
		var output = '' + num;

		while ( output.length < size ) {
			output = '0' + output;
		}

		return output;
	};

	_.extend( NumberCounter.prototype, {
		/**
		 * Register dom listeners.
		 */
		registerListeners: function () {
			$( window ).on( 'scroll.' + this.uniqueNS, _.throttle( _.bind( function() {
				if ( this.widgetScrolledIntoView() ) {
					this.triggerCounting();
				}
			}, this ), 500 ) );

			return this;
		},

		/**
		 * Destroy all listeners registered in the registerListeners()
		 */
		destroyListeners: function () {
			$( window ).off( 'scroll.' + this.uniqueNS );

			return this;
		},

		/**
		 * Trigger counting for all the numbers in a single widget
		 */
		triggerCounting: function () {
			_.each( this.getSingleNumbersInWidget(), function ( $singleNumber ) {
				this.animateValue( $singleNumber, 0, $singleNumber.data( 'to' ), this.$widgetElement.data( 'speed' ) );
			}, this );

			this.destroyListeners();
		},

		/**
		 * Get all single number containers in a Number Widget.
		 * @return {array} array of cached jQuery elements
		 */
		getSingleNumbersInWidget: function () {
			var singleNumbers = [];

			this.$widgetElement.find( config.numberContainerClass ).each( function() {
				singleNumbers.push( $( this ) );
			} );

			return singleNumbers;
		},


		/**
		 * Animate counting
		 * Assumes integer values for start, end and speed and DOM element for the element parameter
		 */
		animateValue: function( $element, start, end, speed ) {
			var numLength = end.toString().length;

			$( { num: start } ).animate( { num: end }, {
				duration: speed,
				easing: 'easeInOutQuad',
				complete: function () {
					$element.text( end.toString() );
				},
				step: function () {
					$element.text( leadingZeros( Math.ceil( this.num ) , numLength ) );
				}
			} );
		},

		/**
		 * Function for detecting if the element is visible on screen
		 */
		widgetScrolledIntoView: function() {
			var docViewTop  = $( window ).scrollTop(),
				docViewBottom = docViewTop + $( window ).height(),
				elemTop       = this.$widgetElement.children( '.number-counter' ).first().offset().top,
				elemBottom    = elemTop + this.$widgetElement.children( '.number-counter' ).first().height();

			return ( ( elemBottom <= docViewBottom ) && ( elemTop >= docViewTop ) );
		},
	});

	return NumberCounter;
});