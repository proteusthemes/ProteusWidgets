<?php

class ProteusWidgetsTest extends WP_UnitTestCase {
	function setUp() {
		parent::setUp();
		$this->ProteusWidgets = new ProteusWidgets();
		$this->ProteusWidgets->define_version();
	}

	function test_class_is_available() {
		$this->assertInstanceOf( 'ProteusWidgets', $this->ProteusWidgets );
	}

	function test_plugin_activation() {
		$this->assertFalse( get_option( 'pw_activation_version' ), 'not in db yet' );

		$this->ProteusWidgets->plugin_activation();

		$installed_version = get_option( 'pw_activation_version' );

		$this->assertNotEmpty( $installed_version );

		return $installed_version;
	}

	/**
	 * @depends test_plugin_activation
	 */
	function test_plugin_version( $installed_version ) {
		$this->assertRegExp( '/^\d{1,2}\.\d{1,2}\.\d{1,2}$/', $installed_version, 'installed version should be of the form X.X.X' );
	}

	function test_plugin_path_constant() {
		$this->assertTrue( defined( 'PW_PATH' ), 'plugin path should be defined' );
		$this->assertStringStartsWith( '/', PW_PATH );
		$this->assertStringEndsWith( 'proteuswidgets/', PW_PATH, 'should have a trailing slash' );
	}

	function test_plugin_url_constant() {
		$this->assertTrue( defined( 'PW_URL' ), 'plugin url should be defined' );
		$this->assertStringStartsWith( 'http', PW_URL );
		$this->assertStringEndsWith( 'proteuswidgets/', PW_URL, 'should have a trailing slash' );
	}

	function test_admin_enqueue_js() {
		$this->ProteusWidgets->admin_enqueue_js_css();

		$should_be_enqueued = array(
			'pw-mustache'     => PW_PATH . 'bower_components/mustache/mustache.min.js',
			'pw-admin-script' => PW_PATH . 'assets/js/admin.js',
		);

		foreach ( $should_be_enqueued as $handle => $path ) {
			$this->assertTrue( wp_script_is( $handle, 'enqueued' ), "script with handle {$handle} should be enqueued" );
			$this->assertFileExists( $path, "script file for {$handle} should exist" );
		}

		return;
	}

	/**
	 * @depends test_admin_enqueue_js
	 */
	function test_admin_enqueue_css() {
		$should_be_enqueued = array(
			'font-awesome'   => PW_PATH . 'bower_components/fontawesome/css/font-awesome.min.css',
			'pw-admin-style' => PW_PATH . 'assets/stylesheets/admin.css',
		);

		foreach ( $should_be_enqueued as $handle => $path ) {
			$this->assertTrue( wp_style_is( $handle, 'enqueued' ), "style with handle {$handle} should be enqueued" );
			$this->assertFileExists( $path, "style file for {$handle} should exist" );
		}
	}

	function test_enqueue_js() {
		$this->ProteusWidgets->enqueue_js_css();

		$should_be_enqueued = array(
			'pw-script' => PW_PATH . 'assets/js/main.min.js',
		);

		foreach ( $should_be_enqueued as $handle => $path ) {
			$this->assertTrue( wp_script_is( $handle, 'enqueued' ), "script with handle {$handle} should be enqueued" );
			$this->assertFileExists( $path, "script file for {$handle} should exist" );
		}

		return;
	}

	/**
	 * @depends test_enqueue_js
	 */
	function test_enqueue_css() {
		$this->ProteusWidgets->enqueue_js_css();

		$should_be_enqueued = array(
			'font-awesome' => PW_PATH . 'bower_components/fontawesome/css/font-awesome.min.css',
			'pw-style'     => PW_PATH . 'main.css',
		);

		foreach ( $should_be_enqueued as $handle => $path ) {
			$this->assertTrue( wp_style_is( $handle, 'enqueued' ), "style with handle {$handle} should be enqueued" );
			$this->assertFileExists( $path, "style file for {$handle} should exist" );
		}
	}

	function test_allowed_protocols() {
		$allowed = array( 'skype', 'tel', 'mailto' );

		foreach ($allowed as $protocol) {
			$this->assertContains( $protocol, wp_allowed_protocols(), "{$protocol} should be also allowed protocol" );
		}
	}
}