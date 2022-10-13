<?php
/*
Plugin Name: ProteusWidgets
Plugin URI: http://www.proteusthemes.com
Description: WP widgets for retail businesses by ProteusThemes
Version: 1.1.3
Author: ProteusThemes
Author URI: http://www.proteusthemes.com
License: GPL3
License URI: http://www.gnu.org/licenses/gpl.html
Donate link: http://www.proteusthemes.com/#donate
Text domain: proteuswidgets
Prefix: pw
*/



// Path/URL to root of this plugin, with trailing slash
define( 'PW_PATH', apply_filters( 'pw/plugin_dir_path', plugin_dir_path( __FILE__ ) ) );
define( 'PW_URL', apply_filters( 'pw/plugin_dir_url', plugin_dir_url( __FILE__ ) ) );

//include php files
require_once( PW_PATH . 'inc/class-pw-functions.php');
require_once( PW_PATH . 'inc/class-pw-widget.php');

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
		add_action( 'admin_init', array( $this, 'update_plugin_version' ) );
		add_action( 'plugins_loaded', array( $this, 'define_version' ) );
		add_action( 'plugins_loaded', array( $this, 'plugin_textdomain' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_js_css' ), 20 );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_js_css' ), 20 );
		add_action( 'widgets_init', array( $this, 'widgets_init' ) );
		add_action( 'after_setup_theme', array( $this, 'after_theme_setup' ) , 11 );

		// filters
		add_filter( 'kses_allowed_protocols', array( $this, 'kses_allowed_protocols' ) );

		// plugin activation hook
		register_activation_hook( __FILE__, array( $this, 'plugin_activation' ) );
	}


	/**
	 * Enqueue the JS and CSS files for backend (admin area) with the right action
	 * @see admin_enqueue_scripts
	 * @return void
	 */
	public static function admin_enqueue_js_css() {
		$version = get_option( 'pw_installed_version', '0.0.1' );

		if ( ! defined( 'PW_VERSION' ) ) {
			define( 'PW_VERSION', apply_filters( 'pw/version', $version ) );
		}

		wp_register_script( 'pw-mustache', PW_URL  . 'bower_components/mustache/mustache.min.js', array(), null, true );
		wp_enqueue_script( 'pw-admin-script', PW_URL . 'assets/js/admin.js' , array( 'jquery', 'underscore', 'backbone', 'pw-mustache' ), PW_VERSION );

		wp_enqueue_style( 'font-awesome', PW_URL . 'bower_components/fontawesome/css/font-awesome.min.css', array(), '4.2.0' );
		wp_enqueue_style( 'pw-admin-style', PW_URL . 'assets/stylesheets/admin.css', array( 'font-awesome' ), PW_VERSION );
	}


	/**
	 * Enqueue the JS and CSS files for frontend with the right action
	 * @see wp_enqueue_scripts
	 * @return void
	 */
	public static function enqueue_js_css() {
		$version = get_option( 'pw_installed_version', '0.0.1' );

		if ( ! defined( 'PW_VERSION' ) ) {
			define( 'PW_VERSION', apply_filters( 'pw/version', $version ) );
		}

		wp_enqueue_style( 'font-awesome', PW_URL . 'bower_components/fontawesome/css/font-awesome.min.css' );
		wp_enqueue_style( 'pw-style', PW_URL . 'main.css', array( 'font-awesome' ), PW_VERSION );

		// main JS file
		wp_enqueue_script( 'pw-script', PW_URL  . 'assets/js/main.min.js', array( 'jquery', 'underscore' ), PW_VERSION );

		// Pass data to the main script
		wp_localize_script( 'pw-script', 'PWVars', array(
			'pathToPlugin'  => PW_URL,
		) );
	}


	/**
	 * Write to the DB the current installed plugin version
	 */
	public function update_plugin_version() {
		$plugin_data = get_plugin_data( __FILE__ );

		return update_option( 'pw_installed_version', $plugin_data['Version'] );
	}


	/**
	 * Define some constants as soon as the plugins are loaded
	 */
	public function define_version() {
		$version = get_option( 'pw_installed_version', '0.0.1' );

		if ( ! defined( 'PW_VERSION' ) ) {
			define( 'PW_VERSION', apply_filters( 'pw/version', $version ) );
		}

		return PW_VERSION;
	}


	/**
	 * Load the plugin textdomain
	 */
	public function plugin_textdomain() {
		load_plugin_textdomain( 'proteuswidgets', false, plugin_basename( dirname( __FILE__ ) ) . '/languages/' );
	}


	/**
	 * Require all widgets
	 */
	public function widgets_init() {
		foreach ( apply_filters( 'pw/loaded_widgets', $this->widgets ) as $filename ) {
			require_once sprintf( '%swidgets/%s.php', PW_PATH, $filename );
		}
	}

	/**
	 * Adds theme support - thumbnail for featured page widget
	 */
	public function after_theme_setup() {
		// Backwards compatibility for MentalPress 1.0.1 or older and ProteusWidgets version 1.0.3
		// Use these new image sizes only for the future releases of ProteusWidgets plugin.
		if ( PW_Functions::installed_after( '1.0.3' ) ) {
			$page_box_image_size = apply_filters( 'pw/featured_page_widget_page_box_image_size', array( 'width' => 360, 'height' => 240, 'crop' => true ) );
			$inline_image_size = apply_filters( 'pw/featured_page_widget_inline_image_size', array( 'width' => 100, 'height' => 75, 'crop' => true ) );

			if ( false === get_theme_support( 'post-thumbnails' ) ) {
				add_theme_support( 'post-thumbnails' );
				add_image_size( 'pw-page-box', $page_box_image_size['width'], $page_box_image_size['height'], $page_box_image_size['crop'] );
				add_image_size( 'pw-inline', $inline_image_size['width'], $inline_image_size['height'], $inline_image_size['crop'] );
			}
			else {
				add_image_size( 'pw-page-box', $page_box_image_size['width'], $page_box_image_size['height'], $page_box_image_size['crop'] );
				add_image_size( 'pw-inline', $inline_image_size['width'], $inline_image_size['height'], $inline_image_size['crop'] );
			}
		}
		else {
			if ( false === get_theme_support( 'post-thumbnails' ) ) {
				add_theme_support( 'post-thumbnails' );
				add_image_size( 'page-box', 360, 240, true );
			}
			else {
				add_image_size( 'page-box', 360, 240, true );
			}
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

	/**
	 * Plugin activation function - run at plugin activation
	 */
	public function plugin_activation() {
		$plugin_data = get_plugin_data( __FILE__ );
		add_option( 'pw_activation_version', $plugin_data['Version'] );
	}
}
$ProteusWidgets = new ProteusWidgets();
