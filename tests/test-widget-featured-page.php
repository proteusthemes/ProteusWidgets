<?php

class WidgetFeaturedPageTest extends WP_UnitTestCase {

	function render_featured_page( $page_id ) {
		ob_start();
		( new PW_Featured_Page() )->widget( array( 'before_widget' => '<div>', 'after_widget' => '</div>', 'before_title' => '', 'after_title' => '', 'widget_id' => 'fp-1' ), array( 'page_id' => $page_id, 'layout' => 'inline' ) );
		return ob_get_clean();
	}

	function test_unpublished_and_protected_pages_do_not_show_their_text() {
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
	}

	function test_excerpt_drops_shortcodes_separates_blocks_and_cuts_long_words() {
		foreach ( array(
			'[gallery ids="1,2"] Text after the gallery.'  => '<p>Text after the gallery.</p>',
			'<h3>Our Vision</h3><p>Founded in 1979.</p>' => '<p>Our Vision Founded in 1979.</p>',
			'Start ' . str_repeat( 'b', 400 )             => '<p>Start ' . str_repeat( 'b', 54 ) . ' &hellip;</p>',
		) as $content => $expected ) {
			$page_id = self::factory()->post->create( array( 'post_type' => 'page', 'post_content' => $content, 'post_excerpt' => '' ) );
			$this->assertStringContainsString( $expected, $this->render_featured_page( $page_id ) );
		}
	}
}
