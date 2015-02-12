/**
 * Utilities for the administration when using ProteusThemes products
 */

jQuery( document ).ready( function( $ ) {
	'use strict';

	/**
	 * Select Icon on Click
	 */
	$( 'body' ).on( 'click', '.js-selectable-icon', function ( ev ) {
		ev.preventDefault();
		var $this = $( this );
		$this.siblings( '.js-icon-input' ).val( $this.data( 'iconname' ) );
	} );

} );

/**
 * Backbone handling of the multiple testimonials
 */

var ptTestimonials = ptTestimonials || {};

// model for a single testimonial
ptTestimonials.Testimonial = Backbone.Model.extend( {
	defaults: {
		'quote':  '',
		'author': '',
		'rating': 5,
	},

	parse: function ( attributes ) {
		// ID is always numeric
		attributes.id = parseInt( attributes.id, 10 );

		return attributes;
	},
} );

// view of a single testimonial
ptTestimonials.testimonialView = Backbone.View.extend( {
	className: 'pt-widget-single-testimonial',

	events: {
		'click .js-pt-remove-testimonial': 'destroy'
	},

	initialize: function ( params ) {
		this.templateHTML = params.templateHTML;

		return this;
	},

	render: function () {
		this.$el.html( Mustache.render( this.templateHTML, this.model.attributes ) );

		this.$( 'select.js-rating' ).val( this.model.get( 'rating' ) );

		return this;
	},

	destroy: function ( ev ) {
		ev.preventDefault();

		this.remove();
		this.model.trigger( 'destroy' );
	},
} );

// view of all testimonials, but associated with each individual widget
ptTestimonials.testimonialsView = Backbone.View.extend( {
	events: {
		'click .js-pt-add-testimonial': 'addNew'
	},

	initialize: function ( params ) {
		this.widgetId = params.widgetId;

		// cached reference to the element in the DOM
		this.$testimonials = this.$( '.testimonials' );

		// collection of testimonials, local to each instance of ptTestimonials.testimonialsView
		this.testimonials = new Backbone.Collection( [], {
			model: ptTestimonials.Testimonial,
		} );

		// listen to adding of the new testimonials
		this.listenTo( this.testimonials, 'add', this.appendOne );

		return this;
	},

	addNew: function ( ev ) {
		ev.preventDefault();

		// default, if there is no testimonials added yet
		var testimonialId = 0;

		if ( ! this.testimonials.isEmpty() ) {
			var testimonialsWithMaxId = this.testimonials.max( function ( testimonial ) {
				return parseInt( testimonial.id, 10 );
			} );

			testimonialId = parseInt( testimonialsWithMaxId.id, 10 ) + 1;
		}


		this.testimonials.add( new ptTestimonials.Testimonial( {
			id: testimonialId,
		} ) );

		return this;
	},

	appendOne: function ( testimonial ) {
		var renderedTestimonial = new ptTestimonials.testimonialView( {
			model:        testimonial,
			templateHTML: jQuery( '#js-pt-testimonial-' + this.widgetId ).html(),
		} ).render();

		this.$testimonials.append( renderedTestimonial.el );

		return this;
	}
} );


/**
 * Function which adds the existing testimonials to the DOM
 * @param  {json} testimonialsJSON
 * @param  {string} widgetId ID of widget from PHP $this->id
 * @return {void}
 */
var repopulateTestimonials = function ( testimonialsJSON, widgetId ) {
	// view of all testimonials
	var testimonialsView = new ptTestimonials.testimonialsView( {
		el:       '#testimonials-' + widgetId,
		widgetId: widgetId,
	} );

	// convert to array if needed
	if ( _( testimonialsJSON ).isObject() ) {
		testimonialsJSON = _( testimonialsJSON ).values();
	};

	// add all testimonials to collection of newly created view
	testimonialsView.testimonials.add( testimonialsJSON, { parse: true } );

	window.testimonialss = testimonialsView;
};