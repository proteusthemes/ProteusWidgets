module.exports = function ( grunt ) {
	// Auto-load the needed grunt tasks
	// require('load-grunt-tasks')(grunt);
	require( 'load-grunt-tasks' )( grunt, { pattern: ['grunt-*'] } );

	// configuration
	grunt.initConfig( {
		pgk: grunt.file.readJSON( 'package.json' ),

		// https://npmjs.org/package/grunt-contrib-compass
		compass: {
			options: {
				sassDir:        'assets/sass',
				cssDir:         '.tmp/',
				imagesDir:      'assets/images',
				outputStyle:    'compact',
				relativeAssets: true,
				noLineComments: true
				// importPath:     ['bower_components/bootstrap-sass-official/assets/stylesheets']
			},
			dev: {
				options: {
					watch: true
				}
			},
			build: {
				options: {
					watch: false,
					force: true
				}
			}
		},

		// Parse CSS and add vendor-prefixed CSS properties using the Can I Use database. Based on Autoprefixer.
		// https://github.com/nDmitry/grunt-autoprefixer
		autoprefixer: {
			options: {
				browsers: ['last 2 versions', 'ie 9', 'ie 10']
			},
			style_css: {
				expand: true,
				cwd:    '.tmp/',
				src:    '*.css',
				dest:   './'
			},
		},

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
		'compass:dev',
		'watch'
	] );

	// build
	grunt.registerTask( 'build', [
		'compass:build',
		'autoprefixer',
		'requirejs',
		'wp_readme_to_markdown',
	] );
};