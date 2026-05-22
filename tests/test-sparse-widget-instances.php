<?php

class SparseWidgetInstancesTest extends WP_UnitTestCase {

	function render_widget( $widget_class, $instance, $message = '' ) {
		$errors = array();

		set_error_handler( function ( $severity, $error, $file, $line ) use ( &$errors ) {
			if ( 0 === ( error_reporting() & $severity ) ) {
				return false;
			}

			$errors[] = sprintf( '%s:%d %s', basename( $file ), $line, $error );
			return true;
		} );

		ob_start();
		try {
			the_widget( $widget_class, $instance, array(
				'before_widget' => '<div class="test-widget %s">',
				'after_widget'  => '</div>',
				'before_title'  => '<h2>',
				'after_title'   => '</h2>',
				'widget_id'     => 'test_' . strtolower( $widget_class ),
			) );
		}
		finally {
			restore_error_handler();
			$output = ob_get_clean();
		}

		$this->assertSame( array(), array_values( array_unique( $errors ) ), $message );

		return $output;
	}

	function assert_output_contains( $needle, $output, $message = '' ) {
		$this->assertTrue( false !== strpos( $output, $needle ), $message );
	}

	function test_all_widgets_render_empty_instances_without_php_errors() {
		$widget_classes = array(
			'PW_About_Us',
			'PW_Accordion',
			'PW_Author',
			'PW_Banner',
			'PW_Brochure_Box',
			'PW_Facebook',
			'PW_Featured_Page',
			'PW_Google_Map',
			'PW_Icon_Box',
			'PW_Latest_News',
			'PW_Number_Counter',
			'PW_Opening_Time',
			'PW_Person_Profile',
			'PW_Pricing_List',
			'PW_Skype',
			'PW_Social_Icons',
			'PW_Steps',
			'PW_Testimonials',
		);

		foreach ( $widget_classes as $widget_class ) {
			$this->assertIsString(
				$this->render_widget( $widget_class, array(), "{$widget_class} should render an empty instance without PHP warnings." )
			);
		}
	}

	function test_brochure_box_renders_empty_instance_with_defaults() {
		$output = $this->render_widget( 'PW_Brochure_Box', array() );

		$this->assert_output_contains( 'class="brochure-box"', $output, 'Brochure Box should render its link with an empty instance.' );
		$this->assert_output_contains( 'href=""', $output, 'Brochure Box should default the brochure URL.' );
		$this->assert_output_contains( 'target="_self"', $output, 'Brochure Box should default to opening in the same tab.' );
		$this->assert_output_contains( 'class="fa  "', $output, 'Brochure Box should default the icon class.' );
	}

	function test_facebook_renders_empty_instance_with_defaults() {
		$output = $this->render_widget( 'PW_Facebook', array() );

		$this->assert_output_contains( '<h2>Facebook</h2>', $output, 'Facebook should default the title.' );
		$this->assert_output_contains( 'href=https%3A%2F%2Fwww.facebook.com%2FProteusThemes', $output, 'Facebook should default the page URL.' );
		$this->assert_output_contains( 'width=340', $output, 'Facebook should default the iframe width.' );
		$this->assert_output_contains( 'height=500', $output, 'Facebook should default the iframe height.' );
		$this->assert_output_contains( 'show_facepile=1', $output, 'Facebook should default to showing the facepile.' );
		$this->assert_output_contains( 'min-height: 500px; width: 340px;', $output, 'Facebook should apply default dimensions.' );
	}

	function test_featured_page_ignores_empty_instance_with_defaults() {
		unset( $GLOBALS['post'] );

		$output = $this->render_widget( 'PW_Featured_Page', array() );

		$this->assertSame( '', $output, 'Featured Page should return cleanly when sparse settings do not resolve to a page.' );
	}

	function test_google_map_renders_empty_instance_with_defaults() {
		$output = $this->render_widget( 'PW_Google_Map', array() );

		$this->assert_output_contains( 'data-latlng="51.507331,-0.127668"', $output, 'Google Map should default the map center.' );
		$this->assert_output_contains( 'data-markers="[]"', $output, 'Google Map should default to an empty marker list.' );
		$this->assert_output_contains( 'data-zoom="12"', $output, 'Google Map should default the zoom.' );
		$this->assert_output_contains( 'data-type="roadmap"', $output, 'Google Map should default the map type.' );
		$this->assert_output_contains( 'style="height: 380px;"', $output, 'Google Map should default the height.' );
	}

	function test_google_map_falls_back_for_invalid_locations_and_style() {
		$output = $this->render_widget( 'PW_Google_Map', array(
			'locations' => 'not-an-array',
			'style'     => 'Missing Style',
		) );

		$this->assert_output_contains( 'data-markers="[]"', $output, 'Google Map should ignore non-array locations.' );
		$this->assert_output_contains( 'data-style="[]"', $output, 'Google Map should fall back to an empty style for unknown styles.' );
	}
}
