<?php

class WidgetFacebookTest extends WP_UnitTestCase {

	function test_unsupported_color_scheme_is_hidden_but_preserved() {
		$widget = new PW_Facebook();
		$widget->_set( 1 );
		foreach ( array( 'light', 'dark' ) as $scheme ) {
			ob_start();
			try {
				$widget->form( array( 'colorscheme' => $scheme ) );
			}
			finally {
				$html = ob_get_clean();
			}
			$this->assertStringNotContainsString( '<select', $html );
			$this->assertStringContainsString( 'type="hidden" name="' . esc_attr( $widget->get_field_name( 'colorscheme' ) ) . '" value="' . $scheme . '"', $html );
			$saved = $widget->update( array( 'colorscheme' => $scheme ), array() );
			$this->assertSame( $scheme, $saved['colorscheme'] );
			$this->assertStringContainsString( esc_attr( $widget->get_field_name( 'background' ) ), $html );
			ob_start();
			try {
				$widget->widget( array( 'before_widget' => '', 'after_widget' => '', 'before_title' => '', 'after_title' => '' ), $saved );
			}
			finally {
				$rendered = ob_get_clean();
			}
			$this->assertStringContainsString( 'https://www.facebook.com/plugins/page.php?', $rendered );
			$this->assertStringNotContainsString( 'colorscheme=', $rendered );
		}
	}
}
