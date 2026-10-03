<?php

class WidgetUpdatesTest extends WP_UnitTestCase {

	function update_widget( $widget_class, $instance, $old_instance = array() ) {
		$widget = new $widget_class();
		set_error_handler( function ( $severity, $message, $file, $line ) {
			throw new ErrorException( $message, 0, $severity, $file, $line );
		} );
		try {
			return $widget->update( $instance, $old_instance );
		}
		finally {
			restore_error_handler();
		}
	}

	function test_empty_repeaters_can_be_saved_and_cleared() {
		foreach ( array(
			'PW_Accordion'      => 'items',
			'PW_Pricing_List'   => 'items',
			'PW_Testimonials'   => 'testimonials',
			'PW_Number_Counter' => 'counters',
			'PW_Google_Map'     => 'locations',
		) as $widget_class => $field ) {
			foreach ( array( array(), array( $field => array() ), array( $field => null ), array( $field => '' ) ) as $instance ) {
				$saved = $this->update_widget( $widget_class, $instance, array( $field => array( array( 'id' => 1 ) ) ) );
				$this->assertSame( array(), $saved[ $field ], $widget_class );
			}
		}
	}

	function test_sparse_instances_keep_frontend_defaults_after_saving() {
		foreach ( array(
			'PW_Testimonials'   => array( 'title' => 'Testimonials', 'autocycle' => 'no', 'interval' => 5000 ),
			'PW_Number_Counter' => array( 'speed' => 1000 ),
			'PW_Featured_Page'  => array( 'layout' => 'block' ),
			'PW_Facebook'       => array( 'title' => 'Facebook', 'like_link' => 'https://www.facebook.com/ProteusThemes', 'width' => 340, 'height' => 500 ),
			'PW_Google_Map'     => array( 'latLng' => '51.507331,-0.127668', 'zoom' => 12, 'type' => 'roadmap', 'style' => 'Subtle Grayscale', 'height' => 380 ),
			'PW_Opening_Time'   => array( 'separator' => '-', 'closed_text' => 'CLOSED', 'Mon_from' => '8:00', 'Mon_to' => '16:00' ),
		) as $widget_class => $defaults ) {
			$saved = $this->update_widget( $widget_class, array() );
			foreach ( $defaults as $field => $value ) {
				$this->assertSame( $value, $saved[ $field ], $widget_class . ': ' . $field );
			}
		}
	}

	function test_saving_preserves_deliberately_blank_text_fields() {
		foreach ( array(
			'PW_Testimonials'  => array( 'title' => '' ),
			'PW_Facebook'      => array( 'title' => '', 'like_link' => '' ),
			'PW_Featured_Page' => array( 'layout' => '' ),
			'PW_Opening_Time'  => array( 'title' => '', 'separator' => '', 'closed_text' => '', 'Mon_from' => '', 'Mon_to' => '' ),
		) as $widget_class => $instance ) {
			$saved = $this->update_widget( $widget_class, $instance );
			foreach ( $instance as $field => $value ) {
				$this->assertSame( $value, $saved[ $field ], $widget_class . ': ' . $field );
			}
		}
	}

	function test_repeaters_keep_order_and_sanitize_sparse_items() {
		foreach ( array(
			'PW_Accordion'    => array( 'items', 'title' ),
			'PW_Pricing_List' => array( 'items', 'title' ),
			'PW_Testimonials' => array( 'testimonials', 'author' ),
		) as $widget_class => $fields ) {
			list( $field, $text_field ) = $fields;
			$saved = $this->update_widget( $widget_class, array( $field => array(
				7 => array( 'id' => '2', $text_field => 'Second' ),
				3 => array( 'id' => '1', $text_field => '<script>alert(1)</script>First' ),
			) ) );
			$this->assertSame( array( '1', '2' ), array_column( $saved[ $field ], 'id' ) );
			$this->assertSame( 'Second', $saved[ $field ][1][ $text_field ] );
			$this->assertFalse( strpos( $saved[ $field ][0][ $text_field ], '<script>' ) );
			$this->assertSame( $saved, $this->update_widget( $widget_class, $saved ) );
		}
	}

	function test_number_counter_preserves_hidden_progress_and_bounds_numeric_values() {
		$enable_progress = function ( $fields ) {
			$fields['progress_bar'] = true;
			return $fields;
		};
		add_filter( 'pw/number_counter_widget', $enable_progress );
		try {
			foreach ( array( '' => '', '0' => '0', '-20' => 0, '45' => '45', '140' => 100 ) as $value => $expected ) {
				$saved = $this->update_widget( 'PW_Number_Counter', array( 'counters' => array( array( 'progress_bar_value' => (string) $value ) ) ) );
				$this->assertSame( $expected, $saved['counters'][0]['progress_bar_value'] );
				$this->assertSame( $saved, $this->update_widget( 'PW_Number_Counter', $saved ) );
			}
			$saved = $this->update_widget( 'PW_Number_Counter', array( 'counters' => array( array( 'number' => 10 ) ) ) );
			$this->assertSame( '', $saved['counters'][0]['progress_bar_value'] );
		}
		finally {
			remove_filter( 'pw/number_counter_widget', $enable_progress );
		}
	}

	function test_unchecked_fields_do_not_restore_previous_checked_values() {
		$saved = $this->update_widget( 'PW_Opening_Time', array(), array( 'Mon_opened' => '1' ) );
		$this->assertSame( '', $saved['Mon_opened'] );
		$saved = $this->update_widget( 'PW_Facebook', array(), array( 'hide_cover' => '1', 'show_facepile' => '1' ) );
		$this->assertSame( '', $saved['hide_cover'] );
		$this->assertSame( '', $saved['show_facepile'] );
	}

	function test_percent_encoded_urls_survive_saving() {
		$link  = 'https://example.com/caf%C3%A9/my%20page/?q=a%26b';
		$image = 'https://example.com/uploads/team%20photo.jpg';
		$saved = $this->update_widget( 'PW_Social_Icons', array( 'social_icons' => array( array( 'id' => '1', 'link' => $link . ' ', 'icon' => 'fa-facebook' ) ) ) );
		$this->assertSame( $link, $saved['social_icons'][0]['link'] );
		$saved = $this->update_widget( 'PW_About_Us', array( 'autocycle' => 'no', 'interval' => 5000, 'people' => array( array( 'id' => '1', 'tag' => '', 'image' => $image, 'name' => '', 'description' => '', 'link' => $link ) ) ) );
		$this->assertSame( $image, $saved['people'][0]['image'] );
		$this->assertSame( $link, $saved['people'][0]['link'] );
	}

	function test_legacy_testimonial_is_preserved_when_resaved() {
		$saved = $this->update_widget( 'PW_Testimonials', array( 'quote' => '<strong>Great service</strong>', 'author' => 'Customer' ) );
		$this->assertSame( '<strong>Great service</strong>', $saved['testimonials'][0]['quote'] );
		$this->assertSame( 'Customer', $saved['testimonials'][0]['author'] );
		$this->assertSame( $saved, $this->update_widget( 'PW_Testimonials', $saved ) );
	}

	function test_items_without_ids_get_unique_ids() {
		foreach ( array(
			'PW_Accordion'      => 'items',
			'PW_Pricing_List'   => 'items',
			'PW_Testimonials'   => 'testimonials',
			'PW_Number_Counter' => 'counters',
			'PW_Google_Map'     => 'locations',
		) as $widget_class => $field ) {
			$saved = $this->update_widget( $widget_class, array( $field => array( array( 'id' => '1' ), array(), array( 'id' => '' ) ) ) );
			$ids   = array_column( $saved[ $field ], 'id' );
			$this->assertSame( '1', $ids[0], $widget_class );
			$this->assertCount( 3, array_unique( $ids ), $widget_class );
		}
	}

	function get_testimonial_form_rows() {
		$widget = new PW_Testimonials();
		$widget->_set( 2 );
		ob_start();
		$widget->form( $widget->update( array(), array() ) );
		preg_match( '/var testimonialsJSON = (.*?);/', ob_get_clean(), $matches );
		return json_decode( $matches[1], true );
	}

	function test_single_testimonial_form_keeps_a_row_after_an_empty_save() {
		add_filter( 'pw/supports_multiple_testimonials', '__return_false' );
		try {
			$this->assertCount( 1, $this->get_testimonial_form_rows() );
		}
		finally {
			remove_filter( 'pw/supports_multiple_testimonials', '__return_false' );
		}
		$this->assertSame( array(), $this->get_testimonial_form_rows() );
	}

	function test_sparse_map_markers_can_be_resaved() {
		$saved = $this->update_widget( 'PW_Google_Map', array( 'locations' => array( array( 'locationlatlng' => '10,20' ) ) ) );
		$this->assertSame( '10,20', $saved['locations'][0]['locationlatlng'] );
		$this->assertSame( '', $saved['locations'][0]['custompinimage'] );
		$this->assertSame( $saved, $this->update_widget( 'PW_Google_Map', $saved ) );
	}

	function test_non_numeric_ids_can_be_saved() {
		foreach ( array(
			'PW_Accordion'    => 'items',
			'PW_Pricing_List' => 'items',
			'PW_Testimonials' => 'testimonials',
		) as $widget_class => $field ) {
			$saved = $this->update_widget( $widget_class, array( $field => array( array( 'id' => 'abc' ), array( 'id' => 'x' ) ) ) );
			$this->assertSame( array( 'abc', 'x' ), array_column( $saved[ $field ], 'id' ), $widget_class );
		}
	}

	function test_malformed_lists_and_rows_do_not_throw() {
		$enable_lists = function ( $fields ) {
			return array_merge( $fields, array( 'carousel_instead_of_image' => true, 'skills' => true, 'icon_list_items' => true ) );
		};
		$args = array( 'before_widget' => '', 'after_widget' => '', 'before_title' => '', 'after_title' => '', 'widget_id' => 'pw-1' );
		$rows = array( array( 'id' => '0', 'title' => 'Kept' ), 'oops' );
		add_filter( 'pw/person_profile_widget_settings', $enable_lists );
		set_error_handler( '__return_true' );
		try {
			foreach ( array( 'carousel', 'social_icons', 'skills', 'icon_list_items' ) as $field ) {
				( new PW_Person_Profile() )->update( array( $field => $rows ), array() );
				( new PW_Person_Profile() )->widget( $args, array( $field => $rows ) );
			}
			( new PW_Social_Icons() )->update( array( 'social_icons' => $rows ), array() );
			( new PW_Steps() )->update( array( 'items' => $rows ), array() );

			ob_start();
			( new PW_Pricing_List() )->widget( $args, array( 'items' => $rows ) );
			( new PW_Testimonials() )->form( array( 'testimonials' => 'oops' ) );
			( new PW_Google_Map() )->form( array( 'locations' => 'oops' ) );
			$html = ob_get_clean();
		}
		finally {
			restore_error_handler();
			remove_filter( 'pw/person_profile_widget_settings', $enable_lists );
		}
		$this->assertStringContainsString( 'Kept', $html );
		$this->assertStringContainsString( 'var testimonialsJSON = [];', $html );
		$this->assertStringContainsString( 'var locationsJSON = [];', $html );
	}

	function test_person_profile_portrait_moves_into_the_carousel() {
		$stored = array( 'name' => 'Jane', 'image' => 'https://example.com/portrait.jpg', 'tag' => '', 'description' => '' );
		$this->assertSame( $stored['image'], $this->update_widget( 'PW_Person_Profile', $stored )['image'] );

		$enable_carousel = function ( $fields ) {
			$fields['carousel_instead_of_image'] = true;
			return $fields;
		};
		add_filter( 'pw/person_profile_widget_settings', $enable_carousel );
		try {
			$saved = $this->update_widget( 'PW_Person_Profile', $stored );
			$this->assertArrayNotHasKey( 'image', $saved );
			$this->assertSame( array( array( 'id' => '0', 'type' => 'image', 'url' => $stored['image'] ) ), $saved['carousel'] );
			$this->assertSame( $saved, $this->update_widget( 'PW_Person_Profile', $saved ) );
		}
		finally {
			remove_filter( 'pw/person_profile_widget_settings', $enable_carousel );
		}
	}

	function test_testimonial_rating_is_rendered_as_zero_to_five_stars() {
		$args = array( 'before_widget' => '', 'after_widget' => '', 'before_title' => '', 'after_title' => '', 'widget_id' => 'pw-1' );
		foreach ( array( 'abc' => 0, '999' => 5, '3' => 3 ) as $rating => $stars ) {
			ob_start();
			( new PW_Testimonials() )->widget( $args, array( 'testimonials' => array( array( 'id' => '0', 'quote' => 'Q', 'rating' => (string) $rating ) ) ) );
			$this->assertSame( $stars, substr_count( ob_get_clean(), 'fa-star' ), (string) $rating );
		}
	}
}
