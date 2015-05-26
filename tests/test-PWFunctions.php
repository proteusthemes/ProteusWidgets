<?php

class PWFunctionsTest extends WP_UnitTestCase {
	function setUp() {
		parent::setUp();
		$this->ProteusWidgets = new ProteusWidgets();
	}

	function test_class_is_available() {
		$this->assertTrue( class_exists( 'PW_Functions' ) );
	}

	function test_all_methods_exist_and_are_callable() {
		$methods = array(
			'get_social_icons_links',
		);

		foreach ( $methods as $method ) {
			$this->assertTrue( is_callable( array( 'PW_Functions', $method ) ), "method {$method} from class PW_Functions should be callable" );
		}
	}

	function test_get_social_icons_links() {
		$this->assertEmpty( PW_Functions::get_social_icons_links() );

		$array_to_test = array(
			99        => new stdClass,
			'pw-test' => 'test',
			'pw-two'  => 'test2',
			'hey-two' => 'test2',
			'foo'     => null,
			0         => 123,
		);

		$this->assertEquals(
			array( 'pw-two'  => 'test2', 'pw-test' => 'test' ),
			PW_Functions::get_social_icons_links( $array_to_test ),
			'return only entries with keys starting with default: pw-'
		);

		$this->assertEquals(
			array( 'hey-two' => 'test2' ),
			PW_Functions::get_social_icons_links( $array_to_test, 'hey' ),
			'return only entries with keys starting with some custom string'
		);

		$this->assertEmpty(
			PW_Functions::get_social_icons_links( $array_to_test, 'primoz' ),
			'test with the starting key that doesnt exist'
		);

		$this->assertEmpty(
			PW_Functions::get_social_icons_links( $array_to_test, 'foo' ),
			'empty values should not be included'
		);

		$array_to_test['foo'] = 42;

		$this->assertEquals(
			array( 'foo' => 42 ),
			PW_Functions::get_social_icons_links( $array_to_test, 'foo' ),
			'now the foo can be included'
		);
	}
}