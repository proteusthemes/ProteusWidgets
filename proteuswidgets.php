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

if ( ! function_exists( 'proteuswidgets_loaded' ) ) {
	function proteuswidgets_loaded() {
		define( 'PROTEUSWIDGETS_VERSION', get_plugin_data( __FILE__ )['Version'] );
	}
	add_action( 'plugins_loaded', 'proteuswidgets_loaded' );
}