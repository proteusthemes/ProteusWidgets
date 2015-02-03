module.exports = function ( grunt ) {
	// Auto-load the needed grunt tasks
	// require('load-grunt-tasks')(grunt);
	require( 'load-grunt-tasks' )( grunt, { pattern: ['grunt-*'] } );

	// configuration
	grunt.initConfig( {
		pgk: grunt.file.readJSON( 'package.json' ),

		// https://npmjs.org/package/grunt-contrib-watch
		watch: {
			options: {
				livereload: true,
			},

			// minify js files
			minifyjs: {
				files: ['assets/js/**/*.js'],
				tasks: ['requirejs'],
			},

			// PHP
			other: {
				files: ['**/*.php'],
			},

			// README file
			readme: {
				files: ['readme.txt'],
				tasks: ['wp_readme_to_markdown']
			}
		},

		// requireJS optimizer
		// https://github.com/gruntjs/grunt-contrib-requirejs
		requirejs: {
			build: {
				// Options: https://github.com/jrburke/r.js/blob/master/build/example.build.js
				options: {
					baseUrl:                 '',
					mainConfigFile:          'assets/js/main.js',
					optimize:                'uglify2',
					preserveLicenseComments: false,
					useStrict:               true,
					wrap:                    true,
					name:                    'bower_components/almond/almond',
					include:                 'assets/js/main',
					out:                     'assets/js/main.min.js'
				}
			}
		},

		wp_readme_to_markdown: {
			build: {
				files: {
					'README.md': 'readme.txt'
				}
			}
		},
	} );

	// when developing
	grunt.registerTask( 'default', [
		'watch'
	] );

	// build
	grunt.registerTask( 'build', [
		'requirejs',
		'wp_readme_to_markdown',
	] );
};