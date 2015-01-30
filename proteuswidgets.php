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
define( 'PROTEUSWUIDGETS_PATH', plugin_dir_path(__FILE__) );



/**
* ProteusWidgets class, so we don't have to worry about namespace
*/
class ProteusWidgets {
	function __construct() {
		// actions
		add_action( 'plugins_loaded', array( __CLASS__, 'plugins_loaded' ) );
		add_action( 'widgets_init', array( __CLASS__, 'widgets_init' ) );
	}


	/**
	 * Define some constants as soon as the plugins are loaded
	 */
	public static function plugins_loaded() {
		define( 'PROTEUSWIDGETS_VERSION', get_plugin_data( __FILE__ )['Version'] );
	}


	/**
	 * Define some constants as soon as the plugins are loaded
	 */
	public static function widgets_init() {
		require_once PROTEUSWUIDGETS_PATH . 'widgets/widget-brochure-box.php';
		require_once PROTEUSWUIDGETS_PATH . 'widgets/widget-facebook.php';
		require_once PROTEUSWUIDGETS_PATH . 'widgets/widget-featured-page.php';
		require_once PROTEUSWUIDGETS_PATH . 'widgets/widget-google-map.php';
		require_once PROTEUSWUIDGETS_PATH . 'widgets/widget-icon-box.php';
		require_once PROTEUSWUIDGETS_PATH . 'widgets/widget-opening-time.php';
		require_once PROTEUSWUIDGETS_PATH . 'widgets/widget-social-icons.php';
		require_once PROTEUSWUIDGETS_PATH . 'widgets/widget-testimonials.php';
	}
}
new ProteusWidgets;