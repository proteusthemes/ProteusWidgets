<?php

class PWFunctionsTest extends WP_UnitTestCase {
	protected $ProteusWidgets;

	public function set_up() {
		parent::set_up();
		$this->ProteusWidgets = new ProteusWidgets();
	}

	function test_class_is_available() {
		$this->assertTrue( class_exists( 'PW_Functions' ) );
	}

	function test_all_methods_exist_and_are_callable() {
		$methods = array(
			'get_social_icons_links',
			'get_attachment_image_srcs',
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

	function test_get_attachment_image_srcs_skips_missing_images() {
		$this->assertSame(
			'',
			PW_Functions::get_attachment_image_srcs( 0, array( 'thumbnail', 'full' ) )
		);
	}

	function test_get_attachment_image_srcs_skips_malformed_image_data() {
		$filter = function( $out, $id, $size ) {
			if ( 123 !== $id ) {
				return $out;
			}

			if ( 'thumbnail' === $size ) {
				return array( 'https://example.com/thumb.jpg' );
			}

			if ( 'medium' === $size ) {
				return 'not-an-array';
			}

			if ( 'full' === $size ) {
				return array( 'https://example.com/full.jpg', 1200, 800, false );
			}

			return $out;
		};

		add_filter( 'image_downsize', $filter, 10, 3 );

		try {
			$this->assertSame(
				'https://example.com/full.jpg 1200w',
				PW_Functions::get_attachment_image_srcs( 123, array( 'thumbnail', 'medium', 'full' ) )
			);
		} finally {
			remove_filter( 'image_downsize', $filter, 10 );
		}
	}
}
