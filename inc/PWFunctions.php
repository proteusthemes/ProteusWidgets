<?php
/*
	Here are stored all ProteusWidgets helper functions.
*/

// define the PWFunctions class that will hold all static helper functions
class PWFunctions {

	/**
	 * Filter the array to return only the social icons links / values
	 * @return array The array of the social icons and links, or empty array when there is no options in the DB
	 */
	public static function get_social_icons_links ( $all_options ) {
		if ( ! is_array( $all_options ) ) {
			return array();
		}

		$out = array();

		foreach ( $all_options as $key => $value ) {
			if ( self::starts_with_pw( $key ) && ! empty( $value ) ) {
				$out[ $key ] = $value;
			}
		}

		return $out;
	}

	// helper functions for the get_social_icons_links function
	private static function starts_with_pw( $str ) {
		return strpos( $str , 'pw-' ) === 0;
	}

	/**
	 * Reorder the widget array with multiple instances of repeating fields (like multiple testimonials or people).
	 * Reorders the instances to start with key = 0 and the following instances in further order (1,2,3,...) so that
	 * PHP mustache can iterate trough them.
	 * @return array The array of instances with proper order from 0 onward
	 */
	public static function reorder_widget_array_key_values( $widget_array ) {
		$tmp_widget_array = array();
		foreach ( $widget_array as $instace ) {
			array_push( $tmp_widget_array , (array) $instace );
		}
		return $tmp_widget_array;
	}

	/**
	 * Checks if the plugin was installed after the specified version.
	 * @param  string $version_to_compare
	 * @return boolean
	 */
	public static function installed_after( $version_to_compare ) {
		return get_option( 'proteuswidgets_activation_version' ) && version_compare( get_option( 'proteuswidgets_activation_version' ), $version_to_compare, '>' );
	}

}