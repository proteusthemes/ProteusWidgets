<?php

class PWFunctionsTest extends WP_UnitTestCase {
	function setUp() {
		parent::setUp();
		$this->ProteusWidgets = new ProteusWidgets();
	}

	function test_class_is_available() {
		$this->assertTrue( class_exists( 'PWFunctions' ) );
	}

	function test_all_methods_exist_and_are_callable() {
		$methods = array(
			'get_social_icons_links',
			'reorder_widget_array_key_values',
			'installed_after',
		);

		foreach ( $methods as $method ) {
			$this->assertTrue( is_callable( array( 'PWFunctions', $method ) ), "method {$method} from class PWFunctions should be callable" );
		}
	}

	function test_get_social_icons_links() {
		$this->assertEmpty( PWFunctions::get_social_icons_links() );

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
			PWFunctions::get_social_icons_links( $array_to_test ),
			'return only entries with keys starting with default: pw-'
		);

		$this->assertEquals(
			array( 'hey-two' => 'test2' ),
			PWFunctions::get_social_icons_links( $array_to_test, 'hey' ),
			'return only entries with keys starting with some custom string'
		);

		$this->assertEmpty(
			PWFunctions::get_social_icons_links( $array_to_test, 'primoz' ),
			'test with the starting key that doesnt exist'
		);

		$this->assertEmpty(
			PWFunctions::get_social_icons_links( $array_to_test, 'foo' ),
			'empty values should not be included'
		);

		$array_to_test['foo'] = 42;

		$this->assertEquals(
			array( 'foo' => 42 ),
			PWFunctions::get_social_icons_links( $array_to_test, 'foo' ),
			'now the foo can be included'
		);
	}

	function test_installed_after() {
		$this->ProteusWidgets->plugin_activation();
		$this->assertTrue( PWFunctions::installed_after( '0' ), 'all versions are installed after 0' );
		$this->assertFalse( PWFunctions::installed_after( '999' ), 'we will never have such great version number' );

		update_option( 'pw_activation_version', '1.5.0' );

		$this->assertFalse( PWFunctions::installed_after( '1.5.0' ), 'equal versions should be false' );
		$this->assertTrue( PWFunctions::installed_after( '1.4.9' ) );
	}

	/**
	 * @expectedException PHPUnit_Framework_Error
	 */
	public function test_expect_error()
	{
		$this->ProteusWidgets->plugin_activation();
		PWFunctions::installed_after();
	}
}