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

		/**
		 * Get the post excerpt. If post_excerpt data is defined use that, otherwise
		 * trim down the content to the proper size.
		 *
		 * @param string $post_excerpt
		 * @param string $post_content
		 *
		 */
		public static function get_post_excerpt( $post_excerpt, $post_content ) {
			if ( ! empty( $post_excerpt ) ) {
				return $post_excerpt;
			}

			$text = strip_shortcodes( $post_content );
			$text = strip_tags( $text );
			$text = str_replace( ']]>', ']]&gt;', $text );
			$excerpt_length = apply_filters( 'excerpt_length', 55 );
			$excerpt_more = apply_filters( 'excerpt_more', ' ' . '[&hellip;]' );
			$text = wp_trim_words( $text, $excerpt_length, $excerpt_more );

			return $text;
		}

		/**
		 * Fetch recent posts data from DB (cache it on first widget instance) and then
		 * fetch the data from cache for all other widget instances.
		 *
		 * @param string $cache_name
		 * @param int $number_of_posts
		 */
		public static function get_cached_data( $cache_name, $number_of_posts ) {
			// Get/set cache data just once for multiple widgets
			$recent_posts_data = wp_cache_get( $cache_name );
			if ( false === $recent_posts_data ) {
				$recent_posts_original_args = array(
					'numberposts'         => $number_of_posts,
					'orderby'             => 'post_date',
					'order'               => 'DESC',
					'post_type'           => 'post',
					'post_status'         => 'publish',
				);

				// Check if WPML plugin is active and set the suppress_filter parameter to false,
				// this solves the issue of multiple translations of the same blog post appear on single language page.
				if ( self::is_wpml_plugin_activated() ) {
					$recent_posts_original_args['suppress_filters'] = false;
				}

				$recent_posts_original = wp_get_recent_posts( $recent_posts_original_args );

				// Prepare the data that we need for display
				$recent_posts_data = array();
				foreach ( $recent_posts_original as $key => $post ) {
					$recent_posts_data[ $key ]['id']             = $post['ID'];
					$recent_posts_data[ $key ]['date']           = get_the_date( apply_filters( 'pw/widget_latest_news_short_date_format', 'M j' ), $post['ID'] );
					$split_date                                  = explode( ' ', $recent_posts_data[ $key ]['date'] );
					$recent_posts_data[ $key ]['day']            = $split_date[1];
					$recent_posts_data[ $key ]['month']          = $split_date[0];
					$recent_posts_data[ $key ]['full_date']      = get_the_date( get_option( 'date_format' ), $post['ID'] );
					$recent_posts_data[ $key ]['full_date_time'] = get_the_date( 'c', $post['ID'] );
					$attachment_image_id                         = get_post_thumbnail_id( $post['ID'] );
					$attachment_image_data                       = wp_get_attachment_image_src( $attachment_image_id, 'pw-latest-news' );

					// get featured image data if a featured image is set
					if ( ! empty( $attachment_image_data ) ) {
						$recent_posts_data[ $key ]['image_url']    = $attachment_image_data[0];
						$recent_posts_data[ $key ]['image_width']  = $attachment_image_data[1];
						$recent_posts_data[ $key ]['image_height'] = $attachment_image_data[2];
						$recent_posts_data[ $key ]['srcset']       = self::get_attachment_image_srcs( $attachment_image_id, array( 'pw-latest-news', 'full' ) );
					}

					$recent_posts_data[ $key ]['link']         = get_permalink( $post['ID'] );
					$recent_posts_data[ $key ]['title']        = $post['post_title'];
					$recent_posts_data[ $key ]['author']       = get_the_author_meta( 'display_name', $post['post_author'] );
					$recent_posts_data[ $key ]['excerpt']      = self::get_post_excerpt( $post['post_excerpt'], $post['post_content'] );
				}

				wp_cache_set( $cache_name, $recent_posts_data );
			}
			return $recent_posts_data;
		}

		/**
		 * Bound an integer between two numbers
		 *
		 * @param int $input
		 * @param int $min
		 * @param int $max
		 */
		public static function bound( $input, $min, $max ) {
			return min( max( $input, $min ), $max );
		}

		/**
		 * Prepare the srcset attribute value.
		 * @param  int $img_id ID of the image
		 * @param  array $sizes array of the image sizes. Example: $sizes = array( 'thumbnail', 'full' );
		 * @uses http://codex.wordpress.org/Function_Reference/wp_get_attachment_image_src
		 * @return string
		 */
		public static function get_attachment_image_srcs( $img_id, $sizes ) {
			$srcset = array();

			foreach ( $sizes as $size ) {
				$img = wp_get_attachment_image_src( $img_id, $size );
				$srcset[] = sprintf( '%s %sw', $img[0], $img[1] ); //
			}

			return implode( ', ' , $srcset );
		}

		/**
		 * Returns string of zeros
		 *
		 * @param int $length, the length of the string
		 */
		public static function leading_zeros( $length ) {
			$s = '';
			while ( strlen( $s ) < $length ){
				$s = '0' . $s;
			}
			return $s;
		}

		/**
		 * Checks if the WPML plugin is activated.
		 *
		 * Note: Used WPML_Config class, because it was the best I could find in the plugin (with a quick search).
		 */
		public static function is_wpml_plugin_activated() {
			return class_exists( 'WPML_Config' );
		}
	}
}
