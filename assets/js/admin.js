/**
 * Utilities for the admin dashboard
 */

jQuery( document ).ready( function( $ ) {
	'use strict';

	// Only replace the text if it is allowed (can be disabled via a WP filter: pw/proteus_themes_text_replacement_enabled )
	if ( ProteusWidgetsAdminVars.ptTextReplacementEnabled ) {
		/**
		 * Replace 'ProteusThemes: ' with the logo image in the titles of the widgets
		 */
		var ptSearchReplace = function ( $el, searchFor ) {
			if ( _.isUndefined( searchFor ) ) {
				searchFor = 'ProteusThemes: ';
			}

			var expression = new RegExp( searchFor );

			$el.html(
				$el.html().replace(
					expression,
					'<img src="' + ProteusWidgetsAdminVars.urlToPlugin + '/assets/images/pt.svg" width="15" height="15" alt="PT" class="proteusthemes-widget-logo" style="position: relative; top: 2px; margin-right: 4px;" /> '
				)
			);
		};

		// For appearance > widgets only
		$( '.widget-title > h3' ).each( function() {
			ptSearchReplace( $( this ) );
		} );

		// Same, but inside page builder
		$( document ).on( 'panels_setup panelsopen', function() {
			$( this ).find( '#siteorigin-panels-metabox .title > h4, .so-title-bar .widget-name' ).each( function () {
				ptSearchReplace( $( this ) );
			} );
		} );

		// Same, but inside appearance > customize > widgets
		$( document ).on( 'widget-added', function() {
			$( this ).find( '.widget-title > h3' ).each( function () {
				ptSearchReplace( $( this ) );
			} );
		} );

		// Same, but inside customizer: [PT] Theme Options title
		$( 'body' ).ready( function () {
			$( '.accordion-section > .accordion-section-title' ).each( function () {
				ptSearchReplace( $( this ), '\\[PT\\] ' );
			} );
		} );
	}

	// Select Icon on Click
	$( 'body' ).on( 'click', '.js-selectable-icon', function ( ev ) {
		ev.preventDefault();
		var $this = $( this );
		$this.siblings( '.js-icon-input' ).val( $this.data( 'iconname' ) ).change();
	} );

	// Make sortable settings foldable.
	$(document).on( 'click', '.pt-sortable-setting__toggle', function() {
		$( this ).toggleClass( 'dashicons-minus dashicons-plus' );
		$( this ).closest( '.pt-sortable-setting' ).find( '.pt-sortable-setting__content' ).toggle();
	});

	// Update sortable setting header on tab title change.
	$(document).on( 'change', '.js-pt-sortable-setting-title', function() {
		$( this ).closest( '.pt-sortable-setting' ).find( '.pt-sortable-setting__header-title' ).text( $( this ).val() );
	});

	// Featured Page widget backend settings.
	$(document).on( 'change', '.js-featured-page-settings__select-layout', function(){
		if ('block' === this.value ) {
			$( this ).parent().siblings('.js-featured-page-settings__additional-block-settings').show();
		}
		else {
			$( this ).parent().siblings('.js-featured-page-settings__additional-block-settings').hide();
		}
	});

} );


/********************************************************
 			Backbone code for repeating fields in widgets
********************************************************/

// Namespace for Backbone elements
window.ProteusWidgets = {
	Models:    {},
	ListViews: {},
	Views:     {},
	Utils:     {},
};


/**
 ******************** Backbone Models *******************
 */

_.extend( ProteusWidgets.Models, {
	Location: Backbone.Model.extend( {
		defaults: {
			'title':          'My Business LLC',
			'locationlatlng': '',
			'custompinimage': '',
		},
	} ),

	Testimonial: Backbone.Model.extend( {
		defaults: {
			'quote':              '',
			'author':             '',
			'rating':             5,
			'author_description': '',
			'author_avatar': '',
		},
	} ),

	Person: Backbone.Model.extend( {
		defaults: {
			'tag':         'ABOUT US',
			'image':       '',
			'name':        '',
			'description': '',
			'link':        '',
		},
	} ),

	SocialIcon: Backbone.Model.extend( {
		defaults: {
			'link': '',
			'icon': ProteusWidgetsAdminVars.defaultSocialIcon,
		},
	} ),

	Counter: Backbone.Model.extend( {
		defaults: {
			'title': '',
			'number': '',
			'icon': '',
			'progress_bar_value': '',
		},
	} ),

	AccordionItem: Backbone.Model.extend( {
		defaults: {
			'title': '',
			'content': '',
		}
	} ),

	Step: Backbone.Model.extend( {
		defaults: {
			'title': '',
			'icon': 'fa-mobile',
			'content': '',
			'step': '',
		}
	} ),

	PricingListItem: Backbone.Model.extend( {
		defaults: {
			'badge':       '',
			'title':       '',
			'price':       '',
			'description': '',
		}
	} ),

	Skill: Backbone.Model.extend( {
		defaults: {
			'name':    '',
			'rating': 5,
		}
	} ),

	CarouselItem: Backbone.Model.extend( {
		defaults: {
			'type': 'image',
			'url':  '',
		}
	} ),
} );



/**
 ******************** Backbone Views *******************
 */

// Generic single view that others can extend from
ProteusWidgets.Views.Abstract = Backbone.View.extend( {
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

_.extend( ProteusWidgets.Views, {

	// View of a single location
	Location: ProteusWidgets.Views.Abstract.extend( {
		className: 'pt-widget-single-location',

		events: {
			'click .js-pt-remove-location': 'destroy',
		},
	} ),

	// View of a single testimonial
	Testimonial: ProteusWidgets.Views.Abstract.extend( {
		className: 'pt-widget-single-testimonial',

		events: {
			'click .js-pt-remove-testimonial': 'destroy',
		},

		render: function () {
			this.$el.html( Mustache.render( this.templateHTML, this.model.attributes ) );

			this.$( 'select.js-rating' ).val( this.model.get( 'rating' ) );

			return this;
		},
	} ),

	// View of a single person
	Person: ProteusWidgets.Views.Abstract.extend( {
		className: 'pt-widget-single-person',

		events: {
			'click .js-pt-remove-person': 'destroy',
		},
	} ),

	// View of a single social icon
	SocialIcon: ProteusWidgets.Views.Abstract.extend( {
		className: 'pt-widget-single-social-icon',

		events: {
			'click .js-pt-remove-social-icon': 'destroy',
		},

		render: function () {
			this.$el.html( Mustache.render( this.templateHTML, this.model.attributes ) );

			var icon = ( this.model.has( 'icon' ) && ! _.isEmpty( this.model.get( 'icon' ) ) ) ? this.model.get( 'icon' ) : this.model.defaults.icon;
			this.$( '.js-icon-input' ).val( icon );

			return this;
		},
	} ),

	// View of a single counter
	Counter: ProteusWidgets.Views.Abstract.extend( {
		className: 'pt-widget-single-counter',

		events: {
			'click .js-pt-remove-counter': 'destroy',
		},

		render: function () {
			this.$el.html( Mustache.render( this.templateHTML, this.model.attributes ) );
			return this;
		},
	} ),

	// View of a single accordion item
	AccordionItem: ProteusWidgets.Views.Abstract.extend( {
		className: 'pt-widget-single-accordion-item',

		events: {
			'click .js-pt-remove-accordion-item': 'destroy',
		},

		render: function () {
			this.$el.html( Mustache.render( this.templateHTML, this.model.attributes ) );
			return this;
		},
	} ),

	// View of a single step
	Step: ProteusWidgets.Views.Abstract.extend( {
		className: 'pt-widget-single-step',

		events: {
			'click .js-pt-remove-step-item': 'destroy',
		},

		render: function () {
			this.$el.html( Mustache.render( this.templateHTML, this.model.attributes ) );

			var icon = ( this.model.has( 'icon' ) && ! _.isEmpty( this.model.get( 'icon' ) ) ) ? this.model.get( 'icon' ) : this.model.defaults.icon;
			this.$( 'input.js-icon-input' ).val( icon );

			return this;
		},
	} ),

	// View of a single pricing list item
	PricingListItem: ProteusWidgets.Views.Abstract.extend( {
		className: 'pt-widget-single-pricing-list-item',

		events: {
			'click .js-pt-remove-pricing-list-item': 'destroy',
		},

		render: function () {
			this.$el.html( Mustache.render( this.templateHTML, this.model.attributes ) );
			return this;
		},
	} ),

	// View of a single skill
	Skill: ProteusWidgets.Views.Abstract.extend( {
		className: 'pt-widget-single-skill',

		events: {
			'click .js-pt-remove-skill': 'destroy',
		},

		render: function () {
			this.$el.html( Mustache.render( this.templateHTML, this.model.attributes ) );

			this.$( 'select.js-rating' ).val( this.model.get( 'rating' ) );

			return this;
		},
	} ),

	// View of a single carousel item
	CarouselItem: ProteusWidgets.Views.Abstract.extend( {
		className: 'pt-widget-single-carousel-item',

		events: {
			'click .js-pt-remove-carousel-item': 'destroy',
		},

		render: function () {
			this.$el.html( Mustache.render( this.templateHTML, this.model.attributes ) );

			this.$( 'select.js-media-type' ).val( this.model.get( 'type' ) );

			return this;
		},
	} ),

} );



/**
 ******************** Backbone ListViews *******************
 *
 * Parent container for multiple view nodes.
 */

ProteusWidgets.ListViews.Abstract = Backbone.View.extend( {

	initialize: function ( params ) {
		this.widgetId     = params.widgetId;
		this.itemsModel   = params.itemsModel;
		this.itemView     = params.itemView;
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

		var currentMaxId = this.getMaxId();

		this.items.add( new this.itemsModel( {
			id: (currentMaxId + 1)
		} ) );

		return this;
	},

	getMaxId: function () {
		if ( this.items.isEmpty() ) {
			return -1;
		}
		else {
			var itemWithMaxId = this.items.max( function ( item ) {
				return parseInt( item.id, 10 );
			} );

			return parseInt( itemWithMaxId.id, 10 );
		}
	},

	appendOne: function ( item ) {
		var renderedItem = new this.itemView( {
			model:        item,
			templateHTML: jQuery( this.itemTemplate + this.widgetId ).html()
		} ).render();

		var currentWidgetId = this.widgetId;

		// If the widget is in the initialize state (hidden), then do not append a new item
		if ( '__i__' !== currentWidgetId.slice( -5 ) ) {
			this.$items.append( renderedItem.el );
		}

		return this;
	}
} );

// Collection of all locations, but associated with each individual widget
_.extend( ProteusWidgets.ListViews, {

	// Collection of all locations, but associated with each individual widget
	Locations: ProteusWidgets.ListViews.Abstract.extend( {
		events: {
			'click .js-pt-add-location': 'addNew'
		}
	} ),

	// Collection of all testimonials, but associated with each individual widget
	Testimonials: ProteusWidgets.ListViews.Abstract.extend( {
		events: {
			'click .js-pt-add-testimonial': 'addNew'
		}
	} ),

	// Collection of all people, but associated with each individual widget
	People: ProteusWidgets.ListViews.Abstract.extend( {
		events: {
			'click .js-pt-add-person': 'addNew'
		}
	} ),

	// Collection of all social icons, but associated with each individual widget
	SocialIcons: ProteusWidgets.ListViews.Abstract.extend( {
		events: {
			'click .js-pt-add-social-icon': 'addNew'
		}
	} ),

	// Collection of all counters, but associated with each individual widget
	Counters: ProteusWidgets.ListViews.Abstract.extend( {
		events: {
			'click .js-pt-add-counter': 'addNew'
		}
	} ),

	// Collection of all accordion items, but associated with each individual widget
	AccordionItems: ProteusWidgets.ListViews.Abstract.extend( {
		events: {
			'click .js-pt-add-accordion-item': 'addNew'
		}
	} ),

	// Collection of all steps, but associated with each individual widget
	Steps: ProteusWidgets.ListViews.Abstract.extend( {
		events: {
			'click .js-pt-add-step-item': 'addNew'
		}
	} ),

	// Collection of all pricing list items, but associated with each individual widget
	PricingListItems: ProteusWidgets.ListViews.Abstract.extend( {
		events: {
			'click .js-pt-add-pricing-list-item': 'addNew'
		}
	} ),

	// Collection of all skills, but associated with each individual widget
	Skills: ProteusWidgets.ListViews.Abstract.extend( {
		events: {
			'click .js-pt-add-skill': 'addNew'
		}
	} ),

	// Collection of all carousel items, but associated with each individual widget
	CarouselItems: ProteusWidgets.ListViews.Abstract.extend( {
		events: {
			'click .js-pt-add-carousel-item': 'addNew'
		}
	} ),
} );



/**
 ******************** Repopulate Functions *******************
 */


_.extend( ProteusWidgets.Utils, {
	// Generic repopulation function used in all repopulate functions
	repopulateGeneric: function ( collectionType, parameters, json, widgetId ) {
		var collection = new collectionType( parameters );

		// Convert to array if needed
		if ( _( json ).isObject() ) {
			json = _( json ).values();
		}

		// Add all items to collection of newly created view
		collection.items.add( json, { parse: true } );
	},

	/**
	 * Function which adds the existing locations to the DOM
	 * @param  {json} locationsJSON
	 * @param  {string} widgetId ID of widget from PHP $this->id
	 * @return {void}
	 */
	repopulateLocations: function ( locationsJSON, widgetId ) {
		var parameters = {
			el:           '#locations-' + widgetId,
			widgetId:     widgetId,
			itemsClass:   '.locations',
			itemTemplate: '#js-pt-location-',
			itemsModel:   ProteusWidgets.Models.Location,
			itemView:     ProteusWidgets.Views.Location,
		};

		this.repopulateGeneric( ProteusWidgets.ListViews.Locations, parameters, locationsJSON, widgetId );
	},


	/**
	 * Function which adds the existing testimonials to the DOM
	 * @param  {json} testimonialsJSON
	 * @param  {string} widgetId ID of widget from PHP $this->id
	 * @return {void}
	 */
	repopulateTestimonials: function ( testimonialsJSON, widgetId ) {
		var parameters = {
			el:           '#testimonials-' + widgetId,
			widgetId:     widgetId,
			itemsClass:   '.testimonials',
			itemTemplate: '#js-pt-testimonial-',
			itemsModel:   ProteusWidgets.Models.Testimonial,
			itemView:     ProteusWidgets.Views.Testimonial,
		};

		this.repopulateGeneric( ProteusWidgets.ListViews.Testimonials, parameters, testimonialsJSON, widgetId );
	},


	/**
	 * Function which adds the existing people to the DOM
	 * @param  {json} peopleJSON
	 * @param  {string} widgetId ID of widget from PHP $this->id
	 * @return {void}
	 */
	repopulatePeople: function ( peopleJSON, widgetId ) {
		var parameters = {
			el:           '#people-' + widgetId,
			widgetId:     widgetId,
			itemsClass:   '.people',
			itemTemplate: '#js-pt-person-',
			itemsModel:   ProteusWidgets.Models.Person,
			itemView:     ProteusWidgets.Views.Person,
		};

		this.repopulateGeneric( ProteusWidgets.ListViews.People, parameters, peopleJSON, widgetId );
	},


	/**
	 * Function which adds the existing social icons to the DOM
	 * @param  {json} socialIconsJSON
	 * @param  {string} widgetId ID of widget from PHP $this->id
	 * @return {void}
	 */
	repopulateSocialIcons: function ( socialIconsJSON, widgetId ) {
		var parameters = {
			el:           '#social-icons-' + widgetId,
			widgetId:     widgetId,
			itemsClass:   '.social-icons',
			itemTemplate: '#js-pt-social-icon-',
			itemsModel:   ProteusWidgets.Models.SocialIcon,
			itemView:     ProteusWidgets.Views.SocialIcon,
		};

		this.repopulateGeneric( ProteusWidgets.ListViews.SocialIcons, parameters, socialIconsJSON, widgetId );
	},


	/**
	 * Function which adds the existing counters to the DOM
	 * @param  {json} countersJSON
	 * @param  {string} widgetId ID of widget from PHP $this->id
	 * @return {void}
	 */
	repopulateCounters: function ( countersJSON, widgetId ) {
		var parameters = {
			el:           '#counters-' + widgetId,
			widgetId:     widgetId,
			itemsClass:   '.counters',
			itemTemplate: '#js-pt-counter-',
			itemsModel:   ProteusWidgets.Models.Counter,
			itemView:     ProteusWidgets.Views.Counter,
		};

		this.repopulateGeneric( ProteusWidgets.ListViews.Counters, parameters, countersJSON, widgetId );
	},

	/**
	 * Function which adds the existing accordion items to the DOM
	 * @param  {json} accordionItemsJSON
	 * @param  {string} widgetId ID of widget from PHP $this->id
	 * @return {void}
	 */
	repopulateAccordionItems: function ( accordionItemsJSON, widgetId ) {
		var parameters = {
			el:           '#accordion-items-' + widgetId,
			widgetId:     widgetId,
			itemsClass:   '.accordion-items',
			itemTemplate: '#js-pt-accordion-item-',
			itemsModel:   ProteusWidgets.Models.AccordionItem,
			itemView:     ProteusWidgets.Views.AccordionItem,
		};

		this.repopulateGeneric( ProteusWidgets.ListViews.AccordionItems, parameters, accordionItemsJSON, widgetId );
	},

	/**
	 * Function which adds the existing steps to the DOM
	 * @param  {json} stepItemsJSON
	 * @param  {string} widgetId ID of widget from PHP $this->id
	 * @return {void}
	 */
	repopulateStepItems: function ( stepItemsJSON, widgetId ) {
		var parameters = {
			el:           '#step-items-' + widgetId,
			widgetId:     widgetId,
			itemsClass:   '.step-items',
			itemTemplate: '#js-pt-step-item-',
			itemsModel:   ProteusWidgets.Models.Step,
			itemView:     ProteusWidgets.Views.Step,
		};

		this.repopulateGeneric( ProteusWidgets.ListViews.Steps, parameters, stepItemsJSON, widgetId );
	},

	/**
	 * Function which adds the existing pricing list items to the DOM
	 * @param  {json} pricingListItemJSON
	 * @param  {string} widgetId ID of widget from PHP $this->id
	 * @return {void}
	 */
	repopulatePricingListItems: function ( pricingListItemJSON, widgetId ) {
		var parameters = {
			el:           '#pricing-list-items-' + widgetId,
			widgetId:     widgetId,
			itemsClass:   '.pricing-list-items',
			itemTemplate: '#js-pt-pricing-list-item-',
			itemsModel:   ProteusWidgets.Models.PricingListItem,
			itemView:     ProteusWidgets.Views.PricingListItem,
		};

		this.repopulateGeneric( ProteusWidgets.ListViews.PricingListItems, parameters, pricingListItemJSON, widgetId );
	},

	/**
	 * Function which adds the existing skills to the DOM
	 * @param  {json} skillsJSON
	 * @param  {string} widgetId ID of widget from PHP $this->id
	 * @return {void}
	 */
	repopulateSkills: function ( skillsJSON, widgetId ) {
		var parameters = {
			el:           '#skills-' + widgetId,
			widgetId:     widgetId,
			itemsClass:   '.skills',
			itemTemplate: '#js-pt-skills-',
			itemsModel:   ProteusWidgets.Models.Skill,
			itemView:     ProteusWidgets.Views.Skill,
		};

		this.repopulateGeneric( ProteusWidgets.ListViews.Skills, parameters, skillsJSON, widgetId );
	},

	/**
	 * Function which adds the existing carousel items to the DOM
	 * @param  {json} carouselJSON
	 * @param  {string} widgetId ID of widget from PHP $this->id
	 * @return {void}
	 */
	repopulateCarousel: function ( carouselJSON, widgetId ) {
		var parameters = {
			el:           '#carousel-items-' + widgetId,
			widgetId:     widgetId,
			itemsClass:   '.carousel-items',
			itemTemplate: '#js-pt-carousel-',
			itemsModel:   ProteusWidgets.Models.CarouselItem,
			itemView:     ProteusWidgets.Views.CarouselItem,
		};

		this.repopulateGeneric( ProteusWidgets.ListViews.CarouselItems, parameters, carouselJSON, widgetId );
	},
} );
