<?php

class PWFunctionsTest extends WP_UnitTestCase {
	public $class_name = 'PWFunctions';

	function test_class_is_available() {
		$this->assertTrue( class_exists( $this->class_name ) );
	}

	function test_all_methods_exist_and_are_static() {
		$methods = array(
			'get_social_icons_links',
			'reorder_widget_array_key_values',
			'installed_after',
		);

		foreach ( $methods as $method ) {
			$this->assertTrue( is_callable( array( $this->class_name, $method ) ), "method {$method} from class {$this->class_name} should be callable" );
		}
	}
}