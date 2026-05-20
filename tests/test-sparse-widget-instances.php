<?php

class SparseWidgetInstancesTest extends WP_UnitTestCase {

	function render_widget( $widget_class, $instance ) {
		ob_start();
		the_widget( $widget_class, $instance, array(
			'before_widget' => '<div class="test-widget %s">',
			'after_widget'  => '</div>',
			'before_title'  => '<h2>',
			'after_title'   => '</h2>',
		) );
		return ob_get_clean();
	}

	function assert_output_contains( $needle, $output, $message = '' ) {
		$this->assertTrue( false !== strpos( $output, $needle ), $message );
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
