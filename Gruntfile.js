module.exports = function ( grunt ) {
	// Auto-load the needed grunt tasks
	// require('load-grunt-tasks')(grunt);
	require( 'load-grunt-tasks' )( grunt, { pattern: ['grunt-*'] } );

	// configuration
	grunt.initConfig( {
		pgk: grunt.file.readJSON( 'package.json' ),

		// https://www.npmjs.com/package/grunt-wp-i18n
		addtextdomain: {
			options: {
				textdomain:    'proteuswidgets',
				updateDomains: true,
			},
			target: {
				files: {
					src: [
						'*.php',
						'inc/**/*.php',
						'widgets/**/*.php',
					]
				}
			}
		},

		// https://www.npmjs.com/package/grunt-wp-i18n
		makepot: {
			target: {
				options: {
					domainPath:  'languages/',
					include:     [
						'^[^/].+\.php$',
						'^inc/.+\.php$',
						'^widgets/.+\.php$',
					],
					mainFile:    'proteuswidgets.php',
					potComments: 'Copyright (C) {year} ProteusThemes \n# This file is distributed under the GPL 2.0.',
					potFilename: 'proteuswidgets.pot',
					potHeaders:  {
						poedit:                 true,
						'report-msgid-bugs-to': 'http://support.proteusthemes.com/',
					},
					type:            'wp-theme',
					updateTimestamp: false,
					updatePoFiles:   true,
				}
			},
		},

		// https://www.npmjs.com/package/grunt-po2mo
		po2mo: {
			files: {
				src:    'languages/*.po',
				expand: true,
			},
		},

	} );

	// update languages files
	grunt.registerTask( 'default', [
		'addtextdomain',
		'makepot',
		'po2mo',
	] );
};
