<?php
/*
 * Here are stored all ProteusWidgets helper functions.
 */

if ( ! class_exists( 'PW_Functions' ) ) {
	class PW_Functions {

		/**
		 * Filter the array to return only the social icons links / values
		 * @return array The array of the social icons and links, or empty array when there is no options in the DB
		 */
		public static function get_social_icons_links ( $all_options = array(), $starts_with = 'pw-' ) {
			if ( ! is_array( $all_options ) ) {
				return array();
			}

			$out = array();

			foreach ( $all_options as $key => $value ) {
				if ( self::starts_with( $key, $starts_with ) && ! empty( $value ) ) {
					$out[ $key ] = $value;
				}
			}

			return $out;
		}


		// Helper functions for the get_social_icons_links function
		private static function starts_with( $str, $needle ) {
			return 0 !== strlen( $str ) && 0 === strpos( $str, $needle );
		}

	}
}