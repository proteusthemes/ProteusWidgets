<?php
/*
Plugin Name: ProteusWidgets
Plugin URI: http://www.proteusthemes.com
Description: WP widgets for retail businesses by ProteusThemes
Version: 1.0.3
Author: ProteusThemes
Author URI: http://www.proteusthemes.com
License: GPL3
License URI: http://www.gnu.org/licenses/gpl.html
Donate link: http://www.proteusthemes.com/#donate
Text domain: proteuswidgets
*/



// Path to root of this plugin, with trailing slash
define( 'PROTEUSWIDGETS_PATH', apply_filters( 'pw/plugin_dir_path', plugin_dir_path(__FILE__) ) );

//include php files
require_once( PROTEUSWIDGETS_PATH . 'inc/PWFunctions.php');
require_once( PROTEUSWIDGETS_PATH . 'inc/class-pw-widget.php');

/**
* ProteusWidgets class, so we don't have to worry about namespace
*/
class ProteusWidgets {
	/**
	 * List of widgets
	 * @var array
	 */
	public $widgets;

	function __construct() {
		// initialize widgets array
		$this->widgets = array(
			'widget-author',
			'widget-banner',
			'widget-brochure-box',
			'widget-facebook',
			'widget-featured-page',
			'widget-google-map',
			'widget-icon-box',
			'widget-opening-time',
			'widget-social-icons',
			'widget-testimonials',
			'widget-skype',
			'widget-about-us',
		);

		// actions
		add_action( 'admin_init', array( $this, 'define_version' ) );
		add_action( 'admin_enqueue_scripts', array( $this , 'admin_enqueue_js_css' ), 20 );
		add_action( 'wp_enqueue_scripts', array( $this , 'enqueue_js_css' ), 20 );
		add_action( 'widgets_init', array( $this, 'widgets_init' ) );
		add_action( 'after_setup_theme', array( $this, 'custom_theme_setup' ) , 11 );

		// filters
		add_filter( 'kses_allowed_protocols', array( $this, 'kses_allowed_protocols' ) );
	}


	/**
	 * Enqueue the JS and CSS files for backend (admin area) with the right action
	 * @see admin_enqueue_scripts
	 * @return void
	 */
	public static function admin_enqueue_js_css() {
		wp_register_script( 'proteuswidgets-mustache', plugin_dir_url( __FILE__ )  . '/bower_components/mustache/mustache.min.js', array(), null, true );

		wp_enqueue_script( 'custom-admin-js', plugin_dir_url( __FILE__ ) . 'assets/js/admin.js' , array( 'jquery', 'underscore', 'backbone', 'proteuswidgets-mustache' ) );
		wp_enqueue_style( 'fontawesome-icons', plugin_dir_url( __FILE__ ) . 'bower_components/fontawesome/css/font-awesome.min.css' );

		wp_enqueue_style( 'admin-styles', plugin_dir_url( __FILE__ ) . '/assets/stylesheets/admin.css' );
	}


	/**
	 * Enqueue the JS and CSS files for frontend with the right action
	 * @see wp_enqueue_scripts
	 * @return void
	 */
	public static function enqueue_js_css() {
		wp_enqueue_style( 'fontawesome-icons', plugin_dir_url( __FILE__ ) . 'bower_components/fontawesome/css/font-awesome.min.css' );
		wp_register_style( 'main-styles', plugin_dir_url( __FILE__ ) . 'main.css' );
		wp_enqueue_style( 'main-styles' );

		// main JS file
		wp_enqueue_script( 'main-js', plugin_dir_url( __FILE__ )  . '/assets/js/main.min.js', array( 'jquery', 'underscore' ) );

		// Pass data to the main script
		wp_localize_script( 'main-js', 'ProteusWidgetsVars', array(
			'pathToPlugin'  => plugin_dir_url( __FILE__ ),
		) );
	}


	/**
	 * Define some constants as soon as the plugins are loaded
	 */
	public function define_version() {
		$plugin_data = get_plugin_data( __FILE__ );
		define( 'PROTEUSWIDGETS_VERSION', apply_filters( 'pw/version', $plugin_data['Version'] ) );
	}


	/**
	 * Define some constants as soon as the plugins are loaded
	 */
	public function widgets_init() {
		foreach ( apply_filters( 'pw/loaded_widgets', $this->widgets ) as $filename ) {
			require_once sprintf( '%swidgets/%s.php', PROTEUSWIDGETS_PATH, $filename );
		}
	}

	/**
	 * Adds theme support - thumbnail for featured page widget
	 */
	public function custom_theme_setup() {

		$supportedTypes = get_theme_support( 'post-thumbnails' );

		if( $supportedTypes === false ) {
			add_theme_support( 'post-thumbnails' );
			add_image_size( 'page-box', 360, 240, true );
		}
		else {
			add_image_size( 'page-box', 360, 240, true );
		}
	}

	/**
	 * Add more allowed protocols
	 *
	 * @link https://developer.wordpress.org/reference/functions/wp_allowed_protocols/
	 */
	public static function kses_allowed_protocols( $protocols ) {
		return array_merge( $protocols, array( 'skype' ) );
	}
}
new ProteusWidgets;