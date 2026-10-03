<?php

class WidgetUpdatesTest extends WP_UnitTestCase {

	private $theme_directory;

	public function set_up() {
		parent::set_up();
		$this->theme_directory = sys_get_temp_dir() . '/pw-widget-updates-' . getmypid();
		$widgets_directory = $this->theme_directory . '/vendor/proteusthemes/proteuswidgets/widgets';
		mkdir( $widgets_directory, 0777, true );
		symlink( dirname( __DIR__ ) . '/widgets/views', $widgets_directory . '/views' );
		add_filter( 'template_directory', array( $this, 'template_directory' ) );
	}

	public function tear_down() {
		remove_filter( 'template_directory', array( $this, 'template_directory' ) );
		$directory = $this->theme_directory . '/vendor/proteusthemes/proteuswidgets/widgets';
		unlink( $directory . '/views' );
		while ( $directory !== dirname( $this->theme_directory ) ) {
			rmdir( $directory );
			$directory = dirname( $directory );
		}
		parent::tear_down();
	}

	public function template_directory() {
		return $this->theme_directory;
	}

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
			foreach ( array( array( $field . '_ready' => '1' ), array( $field => array() ), array( $field => null, $field . '_ready' => '1' ), array( $field => '' ) ) as $instance ) {
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

	function render_skype( $username ) {
		ob_start();
		( new PW_Skype() )->widget( array( 'before_widget' => '', 'after_widget' => '' ), array( 'title' => 'Call', 'skype_username' => $username ) );
		return ob_get_clean();
	}

	function test_skype_keeps_call_links_and_drops_other_schemes() {
		foreach ( array( 'skype:echo123?call', 'tel:+1 (555) 123-4567', 'callto:echo123', '' ) as $username ) {
			$saved = $this->update_widget( 'PW_Skype', array( 'title' => 'Call', 'skype_username' => $username ) );
			$this->assertSame( $username, $saved['skype_username'] );
		}
		$saved = $this->update_widget( 'PW_Skype', array( 'title' => 'Call', 'skype_username' => 'javascript:alert(1)' ) );
		$this->assertSame( '', $saved['skype_username'] );
		$this->assertStringContainsString( 'href="skype:echo123?call"', $this->render_skype( 'skype:echo123?call' ) );
		$this->assertStringContainsString( 'fa-skype', $this->render_skype( 'skype:echo123?call' ) );
		$this->assertStringContainsString( 'href="tel:+15551234567"', $this->render_skype( 'tel:+15551234567' ) );
		$this->assertStringContainsString( 'href=""', $this->render_skype( 'javascript:alert(1)' ) );
	}

	function render_featured_page( $page_id ) {
		ob_start();
		( new PW_Featured_Page() )->widget( array( 'before_widget' => '<div>', 'after_widget' => '</div>', 'before_title' => '', 'after_title' => '', 'widget_id' => 'fp-1' ), array( 'page_id' => $page_id, 'layout' => 'inline' ) );
		return ob_get_clean();
	}

	function test_featured_page_hides_unpublished_and_protected_text() {
		foreach ( array( 'private', 'draft', 'trash' ) as $status ) {
			$page_id = self::factory()->post->create( array( 'post_type' => 'page', 'post_status' => $status, 'post_content' => 'RESTRICTED-BODY' ) );
			$this->assertSame( '', $this->render_featured_page( $page_id ), $status );
		}
		$page_id = self::factory()->post->create( array( 'post_type' => 'page', 'post_title' => 'Protected', 'post_password' => 'secret', 'post_content' => 'RESTRICTED-BODY', 'post_excerpt' => 'RESTRICTED-EXCERPT' ) );
		$html    = $this->render_featured_page( $page_id );
		$this->assertStringContainsString( 'Protected', $html );
		$this->assertStringNotContainsString( 'RESTRICTED', $html );
		$GLOBALS['post'] = get_post( $page_id );
		$this->assertSame( '', $this->render_featured_page( 0 ) );
		$this->assertSame( '', $this->render_featured_page( $page_id + 1000 ) );
	}

	function test_featured_page_excerpt_drops_shortcodes_separates_blocks_and_cuts_long_words() {
		foreach ( array(
			'[gallery ids="1,2"] Text after the gallery.'  => '<p>Text after the gallery.</p>',
			'<h3>Our Vision</h3><p>Founded in 1979.</p>' => '<p>Our Vision Founded in 1979.</p>',
			'Start ' . str_repeat( 'b', 400 )             => '<p>Start ' . str_repeat( 'b', 54 ) . ' &hellip;</p>',
		) as $content => $expected ) {
			$page_id = self::factory()->post->create( array( 'post_type' => 'page', 'post_content' => $content, 'post_excerpt' => '' ) );
			$this->assertStringContainsString( $expected, $this->render_featured_page( $page_id ) );
		}
	}

	function test_featured_page_without_image_renders_without_warnings() {
		$page_id = self::factory()->post->create( array( 'post_type' => 'page', 'post_title' => 'No picture' ) );
		set_error_handler( function ( $severity, $message, $file, $line ) {
			throw new ErrorException( $message, 0, $severity, $file, $line );
		} );
		try {
			ob_start();
			( new PW_Featured_Page() )->widget( array( 'before_widget' => '', 'after_widget' => '', 'before_title' => '', 'after_title' => '', 'widget_id' => 'fp-1' ), array( 'page_id' => $page_id, 'layout' => 'block' ) );
			$html = ob_get_clean();
		}
		finally {
			restore_error_handler();
		}
		$this->assertStringContainsString( 'No picture', $html );
		$this->assertSame( '', PW_Functions::get_attachment_image_srcs( 0, array( 'pw-page-box', 'full' ) ) );
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

	function test_testimonials_and_map_render_without_saved_lists() {
		$args = array( 'before_widget' => '', 'after_widget' => '', 'before_title' => '', 'after_title' => '', 'widget_id' => 'pw-1' );
		set_error_handler( function ( $severity, $message, $file, $line ) {
			throw new ErrorException( $message, 0, $severity, $file, $line );
		} );
		try {
			foreach ( array( 'PW_Testimonials', 'PW_Google_Map' ) as $widget_class ) {
				ob_start();
				try {
					( new $widget_class() )->widget( $args, array() );
				}
				finally {
					$html = ob_get_clean();
				}
				$this->assertNotSame( '', trim( $html ), $widget_class );
			}
		}
		finally {
			restore_error_handler();
		}
		$this->assertStringContainsString( 'data-markers="[]"', $html );
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
		$args = array( 'before_widget' => '', 'after_widget' => '', 'before_title' => '', 'after_title' => '', 'widget_id' => 'pw-1' );
		$rows = array( array( 'id' => '0', 'title' => 'Kept', 'content' => '', 'quote' => 'Kept' ), 'oops' );
		set_error_handler( '__return_true' );
		ob_start();
		try {
			( new PW_Social_Icons() )->update( array( 'social_icons' => $rows ), array() );
			( new PW_Accordion() )->widget( $args, array( 'items' => 'oops' ) );
			( new PW_Accordion() )->widget( $args, array( 'items' => $rows ) );
			( new PW_Testimonials() )->widget( $args, array( 'testimonials' => $rows ) );
			( new PW_Testimonials() )->form( array( 'testimonials' => 'oops' ) );
		}
		finally {
			$html = ob_get_clean();
			restore_error_handler();
		}
		$this->assertSame( 2, substr_count( $html, 'Kept' ) );
		$this->assertStringContainsString( 'var testimonialsJSON = [];', $html );
	}

	function test_testimonial_rating_is_rendered_as_zero_to_five_stars() {
		$args = array( 'before_widget' => '', 'after_widget' => '', 'before_title' => '', 'after_title' => '', 'widget_id' => 'pw-1' );
		foreach ( array( 'abc' => 0, '999' => 5, '3' => 3 ) as $rating => $stars ) {
			ob_start();
			( new PW_Testimonials() )->widget( $args, array( 'testimonials' => array( array( 'id' => '0', 'quote' => 'Q', 'author' => 'A', 'author_description' => '', 'author_avatar' => '', 'rating' => (string) $rating ) ) ) );
			$this->assertSame( $stars, substr_count( ob_get_clean(), 'fa-star' ), (string) $rating );
		}
	}

	function render_widget( $widget_class, $instance, $args = array( 'before_widget' => '', 'after_widget' => '', 'before_title' => '', 'after_title' => '', 'widget_id' => 'pw-1' ) ) {
		set_error_handler( function ( $severity, $message, $file, $line ) {
			throw new ErrorException( $message, 0, $severity, $file, $line );
		} );
		ob_start();
		try {
			( new $widget_class() )->widget( $args, $instance );
		}
		finally {
			$html = ob_get_clean();
			restore_error_handler();
		}
		return $html;
	}

	function test_unopened_widgets_and_sparse_rows_save_without_php_messages() {
		foreach ( array(
			'PW_About_Us'       => array( 'people' => array( array() ) ),
			'PW_Author'         => array(),
			'PW_Banner'         => array(),
			'PW_Brochure_Box'   => array(),
			'PW_Icon_Box'       => array(),
			'PW_Latest_News'    => array(),
			'PW_Person_Profile' => array( 'social_icons' => array( array() ) ),
			'PW_Skype'          => array(),
			'PW_Social_Icons'   => array( 'social_icons' => array( array() ) ),
			'PW_Steps'          => array( 'items' => array( array() ) ),
		) as $widget_class => $sparse_rows ) {
			$this->assertIsArray( $this->update_widget( $widget_class, array() ), $widget_class );
			$this->assertIsArray( $this->update_widget( $widget_class, $sparse_rows ), $widget_class );
		}
	}

	function test_emptied_lists_are_stored_as_empty_lists() {
		foreach ( array(
			'PW_About_Us'     => 'people',
			'PW_Social_Icons' => 'social_icons',
			'PW_Steps'        => 'items',
		) as $widget_class => $field ) {
			$saved = $this->update_widget( $widget_class, array( $field => array() ) );
			$this->assertSame( array(), $saved[ $field ], $widget_class );
		}
	}

	function test_unopened_author_and_banner_keep_their_defaults() {
		$this->assertSame( 1, $this->update_widget( 'PW_Author', array() )['selected_user_id'] );
		$this->assertSame( '', $this->update_widget( 'PW_Banner', array( 'title' => 'T' ) )['open_new'] );
		$this->assertSame( '1', $this->update_widget( 'PW_Banner', array( 'open_new' => '1' ) )['open_new'] );
	}

	function test_empty_instances_and_sparse_rows_render_without_php_messages() {
		foreach ( array( 'PW_About_Us', 'PW_Accordion', 'PW_Author', 'PW_Banner', 'PW_Brochure_Box', 'PW_Facebook', 'PW_Google_Map', 'PW_Icon_Box', 'PW_Number_Counter', 'PW_Opening_Time', 'PW_Person_Profile', 'PW_Pricing_List', 'PW_Skype', 'PW_Social_Icons', 'PW_Steps', 'PW_Testimonials' ) as $widget_class ) {
			$this->assertIsString( $this->render_widget( $widget_class, array() ), $widget_class );
		}
		foreach ( array(
			'PW_Accordion'      => array( 'items' => array( array( 'id' => 1 ) ) ),
			'PW_Number_Counter' => array( 'counters' => array( array( 'id' => 1 ) ) ),
			'PW_Pricing_List'   => array( 'items' => array( array( 'id' => 1 ) ) ),
			'PW_Social_Icons'   => array( 'social_icons' => array( array( 'id' => 1 ) ) ),
			'PW_Steps'          => array( 'items' => array( array( 'id' => 1 ) ) ),
			'PW_Testimonials'   => array( 'testimonials' => array( array( 'id' => 1 ) ) ),
		) as $widget_class => $instance ) {
			$this->assertIsString( $this->render_widget( $widget_class, $instance ), $widget_class );
		}
	}

	function test_widgets_rendered_without_a_widget_id_get_unique_ids() {
		$args = array( 'before_widget' => '', 'after_widget' => '', 'before_title' => '', 'after_title' => '' );
		foreach ( array(
			'PW_About_Us'     => '/id="carousel-people-([^"]*)"/',
			'PW_Accordion'    => '/id="accordion-([^"]*)"/',
			'PW_Testimonials' => '/id="carousel-testimonials-([^"]*)"/',
		) as $widget_class => $pattern ) {
			$ids = array();
			foreach ( array( 1, 2 ) as $call ) {
				$this->assertSame( 1, preg_match( $pattern, $this->render_widget( $widget_class, array(), $args ), $matches ), $widget_class );
				$this->assertNotSame( '', $matches[1], $widget_class );
				$ids[] = $matches[1];
			}
			$this->assertNotSame( $ids[0], $ids[1], $widget_class );
		}
	}

	function test_social_icons_without_rows_print_no_link() {
		foreach ( array( array(), array( 'social_icons' => array() ) ) as $instance ) {
			$this->assertStringNotContainsString( 'social-icons__link', $this->render_widget( 'PW_Social_Icons', $instance ) );
		}
	}

	function test_lists_missing_from_a_form_that_never_showed_them_keep_their_rows() {
		foreach ( array(
			'PW_About_Us'       => 'people',
			'PW_Accordion'      => 'items',
			'PW_Google_Map'     => 'locations',
			'PW_Number_Counter' => 'counters',
			'PW_Person_Profile' => 'social_icons',
			'PW_Pricing_List'   => 'items',
			'PW_Social_Icons'   => 'social_icons',
			'PW_Steps'          => 'items',
			'PW_Testimonials'   => 'testimonials',
		) as $widget_class => $field ) {
			$old = $this->update_widget( $widget_class, array( $field => array( array( 'id' => '1' ) ) ) );
			$this->assertSame( $old[ $field ], $this->update_widget( $widget_class, array( 'title' => 'T' ), $old )[ $field ], $widget_class );

			$saved = $this->update_widget( $widget_class, array( $field . '_ready' => '1' ), $old );
			$this->assertEmpty( isset( $saved[ $field ] ) ? $saved[ $field ] : array(), $widget_class );
			$this->assertArrayNotHasKey( $field . '_ready', $saved, $widget_class );
		}
	}

	function test_repeater_forms_carry_their_rows_and_a_disabled_ready_field() {
		foreach ( array(
			'PW_About_Us'       => array( 'People', 'people' ),
			'PW_Accordion'      => array( 'AccordionItems', 'items' ),
			'PW_Google_Map'     => array( 'Locations', 'locations' ),
			'PW_Number_Counter' => array( 'Counters', 'counters' ),
			'PW_Person_Profile' => array( 'SocialIcons', 'social_icons' ),
			'PW_Pricing_List'   => array( 'PricingListItems', 'items' ),
			'PW_Social_Icons'   => array( 'SocialIcons', 'social_icons' ),
			'PW_Steps'          => array( 'StepItems', 'items' ),
			'PW_Testimonials'   => array( 'Testimonials', 'testimonials' ),
		) as $widget_class => $list ) {
			list( $repeater, $field ) = $list;
			$widget = new $widget_class();
			$widget->_set( 3 );
			ob_start();
			$widget->form( array( $field => array( array( 'id' => 1 ) ) ) );
			$form = ob_get_clean();

			$this->assertStringContainsString( 'data-pw-repeater="' . $repeater . '" data-pw-widget-id="' . $widget->id . '" data-pw-rows="', $form, $widget_class );
			$this->assertStringContainsString( '&quot;id&quot;:1', $form, $widget_class );
			$this->assertStringContainsString( 'name="' . $widget->get_field_name( $field . '_ready' ) . '" value="1" disabled', $form, $widget_class );
		}
	}

	function test_testimonials_form_keeps_a_cleared_title() {
		$widget = new PW_Testimonials();
		$widget->_set( 2 );
		foreach ( array( 'Testimonials' => array(), '' => array( 'title' => '' ) ) as $expected => $instance ) {
			ob_start();
			$widget->form( $instance );
			$this->assertStringContainsString( 'name="' . $widget->get_field_name( 'title' ) . '" type="text" value="' . $expected . '"', ob_get_clean() );
		}
	}

	function test_testimonials_use_the_full_width_for_a_single_testimonial() {
		$one = array( 'id' => 1, 'quote' => 'Q', 'author' => 'A', 'rating' => 5 );
		$this->assertStringContainsString( 'col-sm-12', $this->render_widget( 'PW_Testimonials', array( 'testimonials' => array( $one ) ) ) );
		$this->assertStringContainsString( 'col-sm-6', $this->render_widget( 'PW_Testimonials', array( 'testimonials' => array( $one, array( 'id' => 2 ) + $one ) ) ) );
	}

	function test_testimonials_without_a_title_print_no_heading() {
		$args = array( 'before_widget' => '', 'after_widget' => '', 'before_title' => '<h2>', 'after_title' => '</h2>', 'widget_id' => 'pw-1' );
		$this->assertStringNotContainsString( '<h2>', $this->render_widget( 'PW_Testimonials', array( 'title' => '', 'testimonials' => array( array( 'id' => 1, 'quote' => 'Q' ) ) ), $args ) );
	}
}
