<?php
/*
Plugin Name: ProteusWidgets
Plugin URI: http://www.proteusthemes.com
Description: WP widgets for retail businesses by ProteusThemes
Version: 0.1.0
Author: ProteusThemes
Author URI: http://www.proteusthemes.com
License: GPL3
License URI: http://www.gnu.org/licenses/gpl.html
Donate link: http://www.proteusthemes.com/#donate
Text domain: proteuswidgets
*/



// Path to root of this plugin, with trailing slash
define( 'PROTEUSWUIDGETS_PATH', apply_filters( 'pw/plugin_dir_path', plugin_dir_path(__FILE__) ) );

//include php files
require_once( PROTEUSWUIDGETS_PATH . 'inc/PWFunctions.php');


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
		$this->widgets = apply_filters( 'pw/loaded_widgets', array(
			'widget-author',
			'widget-brochure-box',
			'widget-facebook',
			'widget-featured-page',
			'widget-google-map',
			'widget-icon-box',
			'widget-opening-time',
			'widget-social-icons',
			'widget-testimonials',
		) );

		// actions
		add_action( 'admin_init', array( $this, 'define_version' ) );
		add_action( 'admin_enqueue_scripts', array( $this , 'admin_enqueue_js_css' ), 20 );
		add_action( 'wp_enqueue_scripts', array( $this , 'enqueue_js_css' ), 20 );
		add_action( 'widgets_init', array( $this, 'widgets_init' ) );
	}


	/**
	 * Enqueue the JS and CSS files for backend (admin area) with the right action
	 * @see admin_enqueue_scripts
	 * @return void
	 */
	public static function admin_enqueue_js_css() {
		wp_enqueue_script( 'custom-admin-js', plugin_dir_url( __FILE__ ) . 'assets/js/admin.js' , array( 'jquery') );
		wp_register_style( 'fontawesome-icons', plugin_dir_url( __FILE__ ) . 'bower_components/fontawesome/css/font-awesome.min.css');
	}


	/**
	 * Enqueue the JS and CSS files for frontend with the right action
	 * @see wp_enqueue_scripts
	 * @return void
	 */
	public static function enqueue_js_css() {
		wp_register_style( 'fontawesome-icons', plugin_dir_url( __FILE__ ) . 'bower_components/fontawesome/css/font-awesome.min.css');
	}


	/**
	 * Define some constants as soon as the plugins are loaded
	 */
	public function define_version() {
		define( 'PROTEUSWIDGETS_VERSION', apply_filters( 'pw/version', get_plugin_data( __FILE__ )['Version'] ) );
	}


	/**
	 * Define some constants as soon as the plugins are loaded
	 */
	public function widgets_init() {
		foreach ( $this->widgets as $filename ) {
			require_once sprintf( '%swidgets/%s.php', PROTEUSWUIDGETS_PATH, $filename );
		}
	}
}
new ProteusWidgets;