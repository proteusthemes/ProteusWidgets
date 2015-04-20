/* global Modernizr */

// config
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
] );