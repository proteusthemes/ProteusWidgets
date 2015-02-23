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
 * Backbone models
 */

var proteusWidgets = proteusWidgets || {};

// model for a single location
proteusWidgets.Location = Backbone.Model.extend( {
	defaults: {
		'title':          'My Business LLC',
		'locationlatlng': '',
		'custompinimage': '',
	},
} );

// model for a single testimonial
proteusWidgets.Testimonial = Backbone.Model.extend( {
	defaults: {
		'quote':  '',
		'author': '',
		'rating': 5,
		'author_description': '',
	},
} );

// model for a single person
proteusWidgets.Person = Backbone.Model.extend( {
	defaults: {
		'tag': 'ABOUT US',
		'image': '',
		'name': '',
		'description': '',
		'link': '',
	},
} );


/**
 * Backbone handling of the multiple locations in the maps widget
 */

// view of a single location
proteusWidgets.locationView = Backbone.View.extend( {
	className: 'pt-widget-single-location',

	events: {
		'click .js-pt-remove-location': 'destroy'
	},

	initialize: function ( params ) {
		this.templateHTML = params.templateHTML;

		return this;
	},

	render: function () {
		this.$el.html( Mustache.render( this.templateHTML, this.model.attributes ) );

		return this;
	},

	destroy: function ( ev ) {
		ev.preventDefault();

		this.remove();
		this.model.trigger( 'destroy' );
	},
} );

// view of all locations, but associated with each individual widget
proteusWidgets.locationsView = Backbone.View.extend( {
	events: {
		'click .js-pt-add-location': 'addNew'
	},

	initialize: function ( params ) {
		this.widgetId = params.widgetId;

		// cached reference to the element in the DOM
		this.$locations = this.$( '.locations' );

		// collection of locations, local to each instance of proteusWidgets.locationsView
		this.locations = new Backbone.Collection( [], {
			model: proteusWidgets.Location,
		} );

		// listen to adding of the new locations
		this.listenTo( this.locations, 'add', this.appendOne );

		return this;
	},

	addNew: function ( ev ) {
		ev.preventDefault();

		// default, if there is no locations added yet
		var locationId = 0;

		if ( ! this.locations.isEmpty() ) {
			var locationsWithMaxId = this.locations.max( function ( location ) {
				return parseInt( location.id, 10 );
			} );

			locationId = parseInt( locationsWithMaxId.id, 10 ) + 1;
		}

		this.locations.add( new proteusWidgets.Location( {
			id: locationId,
		} ) );

		return this;
	},

	appendOne: function ( location ) {
		var renderedLocation = new proteusWidgets.locationView( {
			model:    location,
			templateHTML: jQuery( '#js-pt-location-' + this.widgetId ).html(),
		} ).render();

		this.$locations.append( renderedLocation.el );

		return this;
	}
} );

/**
 * Function which adds the existing locations to the DOM
 * @param  {json} locationsJSON
 * @param  {string} widgetId ID of widget from PHP $this->id
 * @return {void}
 */
var repopulateLocations = function ( locationsJSON, widgetId ) {
	// view of all locations
	var locationsView = new proteusWidgets.locationsView( {
		el:       '#locations-' + widgetId,
		widgetId: widgetId,
	} );

	// convert to array if needed
	if ( _( locationsJSON ).isObject() ) {
		locationsJSON = _( locationsJSON ).values();
	};

	// add all locations to collection of newly created view
	locationsView.locations.add( locationsJSON, { parse: true } );
};



/**
 * Backbone handling of the multiple testimonials
 */

// view of a single testimonial
proteusWidgets.testimonialView = Backbone.View.extend( {
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
proteusWidgets.testimonialsView = Backbone.View.extend( {
	events: {
		'click .js-pt-add-testimonial': 'addNew'
	},

	initialize: function ( params ) {
		this.widgetId = params.widgetId;

		// cached reference to the element in the DOM
		this.$testimonials = this.$( '.testimonials' );

		// collection of testimonials, local to each instance of proteusWidgets.testimonialsView
		this.testimonials = new Backbone.Collection( [], {
			model: proteusWidgets.Testimonial,
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


		this.testimonials.add( new proteusWidgets.Testimonial( {
			id: testimonialId,
		} ) );

		return this;
	},

	appendOne: function ( testimonial ) {
		var renderedTestimonial = new proteusWidgets.testimonialView( {
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
	var testimonialsView = new proteusWidgets.testimonialsView( {
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



/**
 * Backbone handling of the multiple persons
 */

// view of a single person
proteusWidgets.personView = Backbone.View.extend( {
	className: 'pt-widget-single-person',

	events: {
		'click .js-pt-remove-person': 'destroy'
	},

	initialize: function ( params ) {
		this.templateHTML = params.templateHTML;

		return this;
	},

	render: function () {
		this.$el.html( Mustache.render( this.templateHTML, this.model.attributes ) );

		return this;
	},

	destroy: function ( ev ) {
		ev.preventDefault();

		this.remove();
		this.model.trigger( 'destroy' );
	},
} );

// view of all persons, but associated with each individual widget
proteusWidgets.personsView = Backbone.View.extend( {
	events: {
		'click .js-pt-add-person': 'addNew'
	},

	initialize: function ( params ) {
		this.widgetId = params.widgetId;

		// cached reference to the element in the DOM
		this.$persons = this.$( '.persons' );

		// collection of persons, local to each instance of proteusWidgets.personsView
		this.persons = new Backbone.Collection( [], {
			model: proteusWidgets.Person,
		} );

		// listen to adding of the new testimonials
		this.listenTo( this.persons, 'add', this.appendOne );

		return this;
	},

	addNew: function ( ev ) {
		ev.preventDefault();

		// default, if there is no testimonials added yet
		var personId = 0;

		if ( ! this.persons.isEmpty() ) {
			var personsWithMaxId = this.persons.max( function ( person ) {
				return parseInt( person.id, 10 );
			} );

			personId = parseInt( personsWithMaxId.id, 10 ) + 1;
		}


		this.persons.add( new proteusWidgets.Person( {
			id: personId,
		} ) );

		return this;
	},

	appendOne: function ( person ) {
		var renderedPerson = new proteusWidgets.personView( {
			model:        person,
			templateHTML: jQuery( '#js-pt-person-' + this.widgetId ).html(),
		} ).render();

		this.$persons.append( renderedPerson.el );

		return this;
	}
} );


/**
 * Function which adds the existing testimonials to the DOM
 * @param  {json} testimonialsJSON
 * @param  {string} widgetId ID of widget from PHP $this->id
 * @return {void}
 */
var repopulatePersons = function ( personsJSON, widgetId ) {
	// view of all testimonials
	var personsView = new proteusWidgets.personsView( {
		el:       '#persons-' + widgetId,
		widgetId: widgetId,
	} );

	// convert to array if needed
	if ( _( personsJSON ).isObject() ) {
		personsJSON = _( personsJSON ).values();
	};

	// add all testimonials to collection of newly created view
	personsView.persons.add( personsJSON, { parse: true } );

	window.persons = personsView;
};