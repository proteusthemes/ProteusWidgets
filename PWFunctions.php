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
			if( ! is_array( $all_options ) ) {
				return array();
			}

			$out = array();

			foreach ($all_options as $key => $value) {
				if ( self::starts_with_zocial( $key ) && ! empty( $value ) ) {
					$out[$key] = $value;
				}
			}

			return $out;
		}

	// helper functions for the get_social_icons_links function
	private static function starts_with_zocial( $str ) {
		return strpos( $str , 'pw-' ) === 0;
	}

}