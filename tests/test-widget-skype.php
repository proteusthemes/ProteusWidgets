<?php

class WidgetSkypeTest extends WP_UnitTestCase {

	function render_skype( $username ) {
		ob_start();
		( new PW_Skype() )->widget( array( 'before_widget' => '', 'after_widget' => '' ), array( 'title' => 'Call', 'skype_username' => $username ) );
		return ob_get_clean();
	}

	function test_saving_keeps_call_links_and_drops_other_schemes() {
		foreach ( array( 'skype:echo123?call', 'tel:+1 (555) 123-4567', 'callto:echo123', '' ) as $username ) {
			$saved = ( new PW_Skype() )->update( array( 'title' => 'Call', 'skype_username' => $username ), array() );
			$this->assertSame( $username, $saved['skype_username'] );
		}
		$saved = ( new PW_Skype() )->update( array( 'title' => 'Call', 'skype_username' => 'javascript:alert(1)' ), array() );
		$this->assertSame( '', $saved['skype_username'] );
	}

	function test_rendering_keeps_call_links_and_drops_other_schemes() {
		$this->assertStringContainsString( 'href="skype:echo123?call"', $this->render_skype( 'skype:echo123?call' ) );
		$this->assertStringContainsString( 'fa-skype', $this->render_skype( 'skype:echo123?call' ) );
		$this->assertStringContainsString( 'href="tel:+15551234567"', $this->render_skype( 'tel:+15551234567' ) );
		$this->assertStringContainsString( 'href=""', $this->render_skype( 'javascript:alert(1)' ) );
	}
}
