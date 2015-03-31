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
		$this.siblings( '.js-icon-input' ).val( $this.data( 'iconname' ) ).change();
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

// model for a single person
proteusWidgets.SocialIcon = Backbone.Model.extend( {
	defaults: {
		'link': '',
		'icon': 'fa-facebook',
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

		var currentWidgetId = this.widgetId;

		// if the widget is in the initialize state (hidden), then do not append a new testimonial
		if ( '__i__' != currentWidgetId.slice( -5, currentWidgetId.length ) ) {
			this.$locations.append( renderedLocation.el );
		}

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

		var currentWidgetId = this.widgetId;

		// if the widget is in the initialize state (hidden), then do not append a new testimonial
		if ( '__i__' != currentWidgetId.slice( -5, currentWidgetId.length ) ) {
			this.$testimonials.append( renderedTestimonial.el );
		}

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
 * Backbone handling of the multiple people
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

// view of all people, but associated with each individual widget
proteusWidgets.peopleView = Backbone.View.extend( {
	events: {
		'click .js-pt-add-person': 'addNew'
	},

	initialize: function ( params ) {
		this.widgetId = params.widgetId;

		// cached reference to the element in the DOM
		this.$people = this.$( '.people' );

		// collection of people, local to each instance of proteusWidgets.peopleView
		this.people = new Backbone.Collection( [], {
			model: proteusWidgets.Person,
		} );

		// listen to adding of the new testimonials
		this.listenTo( this.people, 'add', this.appendOne );

		return this;
	},

	addNew: function ( ev ) {
		ev.preventDefault();

		// default, if there is no testimonials added yet
		var personId = 0;

		if ( ! this.people.isEmpty() ) {
			var peopleWithMaxId = this.people.max( function ( person ) {
				return parseInt( person.id, 10 );
			} );

			personId = parseInt( peopleWithMaxId.id, 10 ) + 1;
		}

		this.people.add( new proteusWidgets.Person( {
			id: personId,
		} ) );

		return this;
	},

	appendOne: function ( person ) {
		var renderedPerson = new proteusWidgets.personView( {
			model:        person,
			templateHTML: jQuery( '#js-pt-person-' + this.widgetId ).html(),
		} ).render();

		var currentWidgetId = this.widgetId;

		// if the widget is in the initialize state (hidden), then do not append a new testimonial
		if ( '__i__' != currentWidgetId.slice( -5, currentWidgetId.length ) ) {
			this.$people.append( renderedPerson.el );
		}

		return this;
	}
} );


/**
 * Function which adds the existing testimonials to the DOM
 * @param  {json} testimonialsJSON
 * @param  {string} widgetId ID of widget from PHP $this->id
 * @return {void}
 */
var repopulatePeople = function ( peopleJSON, widgetId ) {
	// view of all testimonials
	var peopleView = new proteusWidgets.peopleView( {
		el:       '#people-' + widgetId,
		widgetId: widgetId,
	} );

	// convert to array if needed
	if ( _( peopleJSON ).isObject() ) {
		peopleJSON = _( peopleJSON ).values();
	};

	// add all testimonials to collection of newly created view
	peopleView.people.add( peopleJSON, { parse: true } );

	window.people = peopleView;
};



/**
 * Backbone handling of the multiple social icons
 */

// view of a single social icon
proteusWidgets.socialIconView = Backbone.View.extend( {
	className: 'pt-widget-single-social-icon',

	events: {
		'click .js-pt-remove-social-icon': 'destroy'
	},

	initialize: function ( params ) {
		this.templateHTML = params.templateHTML;

		return this;
	},

	render: function () {
		this.$el.html( Mustache.render( this.templateHTML, this.model.attributes ) );

		this.$( 'select.js-icon' ).val( this.model.get( 'icon' ) );

		return this;
	},

	destroy: function ( ev ) {
		ev.preventDefault();

		this.remove();
		this.model.trigger( 'destroy' );
	},
} );

// view of all social icons, but associated with each individual widget
proteusWidgets.socialIconsView = Backbone.View.extend( {
	events: {
		'click .js-pt-add-social-icon': 'addNew'
	},

	initialize: function ( params ) {

		this.widgetId = params.widgetId;

		// cached reference to the element in the DOM
		this.$socialIcons = this.$( '.social-icons' );

		// collection of social icons, local to each instance of proteusWidgets.socialIconsView
		this.socialIcons = new Backbone.Collection( [], {
			model: proteusWidgets.SocialIcon,
		} );

		// listen to adding of the new social icons
		this.listenTo( this.socialIcons, 'add', this.appendOne );

		return this;
	},

	addNew: function ( ev ) {
		ev.preventDefault();

		// default, if there is no social icons added yet
		var socialIconId = 0;

		if ( ! this.socialIcons.isEmpty() ) {
			var socialIconsWithMaxId = this.socialIcons.max( function ( socialIcon ) {
				return parseInt( socialIcon.id, 10 );
			} );

			socialIconId = parseInt( socialIconsWithMaxId.id, 10 ) + 1;
		}

		this.socialIcons.add( new proteusWidgets.SocialIcon( {
			id: socialIconId,
		} ) );

		return this;
	},

	appendOne: function ( socialIcon ) {
		var renderedSocialIcon = new proteusWidgets.socialIconView( {
			model:        socialIcon,
			templateHTML: jQuery( '#js-pt-social-icon-' + this.widgetId ).html(),
		} ).render();

		var currentWidgetId = this.widgetId;

		// if the widget is in the initialize state (hidden), then do not append a new social icon
		if ( '__i__' != currentWidgetId.slice( -5, currentWidgetId.length ) ) {
			this.$socialIcons.append( renderedSocialIcon.el );
		}

		return this;
	}
} );

/**
 * Function which adds the existing social icons to the DOM
 * @param  {json} socialIconsJSON
 * @param  {string} widgetId ID of widget from PHP $this->id
 * @return {void}
 */
var repopulateSocialIcons = function ( socialIconsJSON, widgetId ) {
	// view of all social icons
	var socialIconsView = new proteusWidgets.socialIconsView( {
		el:       '#social-icons-' + widgetId,
		widgetId: widgetId,
	} );

	// convert to array if needed
	if ( _( socialIconsJSON ).isObject() ) {
		socialIconsJSON = _( socialIconsJSON ).values();
	};

	// add all social icons to collection of newly created view
	socialIconsView.socialIcons.add( socialIconsJSON, { parse: true } );

	window.socialIcons = socialIconsView;
};