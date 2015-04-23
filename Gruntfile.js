module.exports = function ( grunt ) {
	// Auto-load the needed grunt tasks
	// require('load-grunt-tasks')(grunt);
	require( 'load-grunt-tasks' )( grunt, { pattern: ['grunt-*'] } );

	var settings = {
		phpFileRegex: '[^/]+\.php$',
	};

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
				noLineComments: true,
				importPath:     ['bower_components/bootstrap-sass/assets/stylesheets']
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

			// autoprefix the files
			autoprefixer: {
				files: ['.tmp/*.css'],
				tasks: ['autoprefixer:style_css'],
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

		// https://npmjs.org/package/grunt-concurrent
		concurrent: {
			server: [
				'compass:dev',
				'watch'
			]
		},

		// https://www.npmjs.com/package/grunt-wp-i18n
		makepot: {
			target: {
				options: {
					domainPath:      'languages/',
					include:         [settings.phpFileRegex, '^widgets/'+settings.phpFileRegex, '^inc/'+settings.phpFileRegex],
					mainFile:        'proteuswidgets.php',
					potComments:     'Copyright (C) {year} ProteusThemes \n# This file is distributed under the GPL 2.0.',
					potFilename:     'proteuswidgets.pot',
					potHeaders:      {
						poedit: true,
						'report-msgid-bugs-to': 'http://support.proteusthemes.com/'
					},
					type:            'wp-plugin',
					updateTimestamp: false,
					updatePoFiles:   true,
				}
			}
		},

		// https://www.npmjs.com/package/grunt-po2mo
		po2mo: {
			files: {
				src:    'languages/*.po',
				expand: true,
			},
		},

		// https://www.npmjs.com/package/grunt-wget
		wget: {
			basic: {
				files: {
					'git-hooks/pre-commit': 'https://gist.githubusercontent.com/capuderg/09f5c9c054ee8075d39d/raw/gistfile1.txt',
				},
				options: {
					overwrite: true,
				},
			},
		},

		// https://www.npmjs.com/package/grunt-githooks
		githooks: {
			all: {
				'pre-commit': {
					taskNames:   'pre-commit',
					hashbang:    '#!/bin/sh',
					template:    'git-hooks/pre-commit',
					startMarker: '## GRUNT GITHOOKS STARTS',
					endMarker:   '## GRUNT GITHOOKS ENDS'
				},
			},
		},

	} );

	// when developing
	grunt.registerTask( 'default', [
		'concurrent:server'
	] );

	// build
	grunt.registerTask( 'build', [
		'compass:build',
		'autoprefixer',
		'requirejs',
		'wp_readme_to_markdown',
	] );

	// update languages files
	grunt.registerTask( 'languages', [
		'makepot',
		'po2mo',
	] );

	// update githooks
	grunt.registerTask( 'updategithooks', [
		'wget',
		'githooks',
	] );
};