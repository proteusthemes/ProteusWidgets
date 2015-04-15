/**
 * Utilities for the admin dashboard
 */

jQuery( document ).ready( function( $ ) {
	'use strict';

	// Select Icon on Click
	$( 'body' ).on( 'click', '.js-selectable-icon', function ( ev ) {
		ev.preventDefault();
		var $this = $( this );
		$this.siblings( '.js-icon-input' ).val( $this.data( 'iconname' ) ).change();
	} );

} );


/********************************************************
 			Backbone code for repeating fields in widgets
********************************************************/

/**
 ******************** Backbone Models *******************
 */

// Namespacing for Backbone modules
window.ProteusWidgets = {
	Models: {},
	Collections: {},
	Views: {}
};

// Model for a single location
ProteusWidgets.Models.Location = Backbone.Model.extend( {
	defaults: {
		'title':          'My Business LLC',
		'locationlatlng': '',
		'custompinimage': ''
	}
} );

// Model for a single testimonial
ProteusWidgets.Models.Testimonial = Backbone.Model.extend( {
	defaults: {
		'quote':  '',
		'author': '',
		'rating': 5,
		'author_description': ''
	}
} );

// Model for a single person
ProteusWidgets.Models.Person = Backbone.Model.extend( {
	defaults: {
		'tag': 'ABOUT US',
		'image': '',
		'name': '',
		'description': '',
		'link': ''
	}
} );

// Model for a single social icon
ProteusWidgets.Models.SocialIcon = Backbone.Model.extend( {
	defaults: {
		'link': '',
		'icon': 'fa-facebook'
	}
} );


/**
 ******************** Backbone Views *******************
 */

// Generic single view that others can extend from
ProteusWidgets.Views.Generic = Backbone.View.extend( {
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
	}
} );

// View of a single location
ProteusWidgets.Views.Location = ProteusWidgets.Views.Generic.extend( {
	className: 'pt-widget-single-location',

	events: {
		'click .js-pt-remove-location': 'destroy'
	}
} );

// View of a single testimonial
ProteusWidgets.Views.Testimonial = ProteusWidgets.Views.Generic.extend( {
	className: 'pt-widget-single-testimonial',

	events: {
		'click .js-pt-remove-testimonial': 'destroy'
	},

	render: function () {
		this.$el.html( Mustache.render( this.templateHTML, this.model.attributes ) );

		this.$( 'select.js-rating' ).val( this.model.get( 'rating' ) );

		return this;
	}
} );

// View of a single person
ProteusWidgets.Views.Person = ProteusWidgets.Views.Generic.extend( {
	className: 'pt-widget-single-person',

	events: {
		'click .js-pt-remove-person': 'destroy'
	}
} );

// View of a single social icon
ProteusWidgets.Views.SocialIcon = ProteusWidgets.Views.Generic.extend( {
	className: 'pt-widget-single-social-icon',

	events: {
		'click .js-pt-remove-social-icon': 'destroy'
	},

	render: function () {
		this.$el.html( Mustache.render( this.templateHTML, this.model.attributes ) );

		this.$( 'select.js-icon' ).val( this.model.get( 'icon' ) );

		return this;
	}
} );


/**
 ******************** Backbone Collections *******************
 */

ProteusWidgets.Collections.Generic = Backbone.View.extend( {

	initialize: function ( params ) {
		this.widgetId = params.widgetId;
		this.itemsModel = params.itemsModel;
		this.itemView = params.itemView;
		this.itemTemplate = params.itemTemplate;

		// Cached reference to the element in the DOM
		this.$items = this.$( params.itemsClass );

		// Collection of items(locations, people, testimonials,...),
		this.items = new Backbone.Collection( [], {
			model: this.itemsModel
		} );

		// Listen to adding of the new items
		this.listenTo( this.items, 'add', this.appendOne );

		return this;
	},

	addNew: function ( ev ) {
		ev.preventDefault();

		// Default, if there are no items added yet
		var itemId = 0;

		if ( ! this.items.isEmpty() ) {
			var itemsWithMaxId = this.items.max( function ( item ) {
				return parseInt( item.id, 10 );
			} );

			itemId = parseInt( itemsWithMaxId.id, 10 ) + 1;
		}

		this.items.add( new this.itemsModel( {
			id: itemId
		} ) );

		return this;
	},

	appendOne: function ( item ) {
		var renderedItem = new this.itemView( {
			model:    item,
			templateHTML: jQuery( this.itemTemplate + this.widgetId ).html()
		} ).render();

		var currentWidgetId = this.widgetId;

		// If the widget is in the initialize state (hidden), then do not append a new item
		if ( '__i__' != currentWidgetId.slice( -5, currentWidgetId.length ) ) {
			this.$items.append( renderedItem.el );
		}

		return this;
	}
} );

// Collection of all locations, but associated with each individual widget
ProteusWidgets.Collections.Locations = ProteusWidgets.Collections.Generic.extend( {
	events: {
		'click .js-pt-add-location': 'addNew'
	}
} );


// Collection of all testimonials, but associated with each individual widget
ProteusWidgets.Collections.Testimonials = ProteusWidgets.Collections.Generic.extend( {
	events: {
		'click .js-pt-add-testimonial': 'addNew'
	}
} );

// Collection of all people, but associated with each individual widget
ProteusWidgets.Collections.People = ProteusWidgets.Collections.Generic.extend( {
	events: {
		'click .js-pt-add-person': 'addNew'
	}
} );

// Collection of all social icons, but associated with each individual widget
ProteusWidgets.Collections.SocialIcons = ProteusWidgets.Collections.Generic.extend( {
	events: {
		'click .js-pt-add-social-icon': 'addNew'
	}
} );


/**
 ******************** Repopulate Functions *******************
 */


/**
 * Function which adds the existing locations to the DOM
 * @param  {json} locationsJSON
 * @param  {string} widgetId ID of widget from PHP $this->id
 * @return {void}
 */
var repopulateLocations = function ( locationsJSON, widgetId ) {
	// Collection of all locations
	var locationsCollection = new ProteusWidgets.Collections.Locations( {
		el:       '#locations-' + widgetId,
		widgetId: widgetId,
		itemsClass: '.locations',
		itemTemplate: '#js-pt-location-',
		itemsModel: ProteusWidgets.Models.Location,
		itemView: ProteusWidgets.Views.Location
	} );

	// Convert to array if needed
	if ( _( locationsJSON ).isObject() ) {
		locationsJSON = _( locationsJSON ).values();
	}

	// Add all locations to collection of newly created view
	locationsCollection.items.add( locationsJSON, { parse: true } );
};

/**
 * Function which adds the existing testimonials to the DOM
 * @param  {json} testimonialsJSON
 * @param  {string} widgetId ID of widget from PHP $this->id
 * @return {void}
 */
var repopulateTestimonials = function ( testimonialsJSON, widgetId ) {
	// Collection of all testimonials
	var testimonialsCollection = new ProteusWidgets.Collections.Testimonials( {
		el:       '#testimonials-' + widgetId,
		widgetId: widgetId,
		itemsClass: '.testimonials',
		itemTemplate: '#js-pt-testimonial-',
		itemsModel: ProteusWidgets.Models.Testimonial,
		itemView: ProteusWidgets.Views.Testimonial
	} );

	// Convert to array if needed
	if ( _( testimonialsJSON ).isObject() ) {
		testimonialsJSON = _( testimonialsJSON ).values();
	}

	// Add all testimonials to collection of newly created view
	testimonialsCollection.items.add( testimonialsJSON, { parse: true } );
};

/**
 * Function which adds the existing people to the DOM
 * @param  {json} peopleJSON
 * @param  {string} widgetId ID of widget from PHP $this->id
 * @return {void}
 */
var repopulatePeople = function ( peopleJSON, widgetId ) {
	// Collection of people
	var peopleCollection = new ProteusWidgets.Collections.People( {
		el:       '#people-' + widgetId,
		widgetId: widgetId,
		itemsClass: '.people',
		itemTemplate: '#js-pt-person-',
		itemsModel: ProteusWidgets.Models.Person,
		itemView: ProteusWidgets.Views.Person
	} );

	// Convert to array if needed
	if ( _( peopleJSON ).isObject() ) {
		peopleJSON = _( peopleJSON ).values();
	}

	// Add people to collection of newly created view
	peopleCollection.items.add( peopleJSON, { parse: true } );
};

/**
 * Function which adds the existing social icons to the DOM
 * @param  {json} socialIconsJSON
 * @param  {string} widgetId ID of widget from PHP $this->id
 * @return {void}
 */
var repopulateSocialIcons = function ( socialIconsJSON, widgetId ) {
	// Collection of all social icons
	var socialIconsCollection = new ProteusWidgets.Collections.SocialIcons( {
		el:       '#social-icons-' + widgetId,
		widgetId: widgetId,
		itemsClass: '.social-icons',
		itemTemplate: '#js-pt-social-icon-',
		itemsModel: ProteusWidgets.Models.SocialIcon,
		itemView: ProteusWidgets.Views.SocialIcon
	} );

	// Convert to array if needed
	if ( _( socialIconsJSON ).isObject() ) {
		socialIconsJSON = _( socialIconsJSON ).values();
	}

	// Add all social icons to collection of newly created view
	socialIconsCollection.items.add( socialIconsJSON, { parse: true } );
};