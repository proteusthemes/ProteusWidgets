<?php

/**
 * Configure WP options
 */
// $GLOBALS['wp_tests_options'] = array(
// 	'active_plugins' => array( 'hello.php' ),
// 	'current_theme' => 'buildpress',
// );


$_tests_dir = getenv('WP_TESTS_DIR');
if ( !$_tests_dir ) $_tests_dir = '/tmp/wordpress-tests-lib';

require_once $_tests_dir . '/includes/functions.php';

// PW_Templating resolves view paths relative to get_template_directory(), expecting the
// package to live at <theme>/vendor/proteusthemes/proteuswidgets/. In tests we don't
// have a real consuming theme, so build a fake theme dir that symlinks back to this
// repo's widgets/views and point template_directory at it.
$_pw_project_root = dirname( __DIR__ );
$_pw_fake_theme   = sys_get_temp_dir() . '/pw-tests-fake-theme';
$_pw_fake_pkg_dir = $_pw_fake_theme . '/vendor/proteusthemes/proteuswidgets';

if ( ! is_dir( $_pw_fake_pkg_dir ) ) {
	@mkdir( $_pw_fake_pkg_dir, 0777, true );
}
$_pw_fake_views = $_pw_fake_pkg_dir . '/widgets/views';
if ( ! file_exists( $_pw_fake_views ) ) {
	@mkdir( dirname( $_pw_fake_views ), 0777, true );
	@symlink( $_pw_project_root . '/widgets/views', $_pw_fake_views );
}

tests_add_filter( 'template_directory', function () use ( $_pw_fake_theme ) {
	return $_pw_fake_theme;
} );
tests_add_filter( 'stylesheet_directory', function () use ( $_pw_fake_theme ) {
	return $_pw_fake_theme;
} );

function _manually_load_plugin() {
	require dirname( __FILE__ ) . '/../proteuswidgets.php';
}
tests_add_filter( 'muplugins_loaded', '_manually_load_plugin' );

function _register_proteuswidgets_widgets_for_tests() {
	$widget_classes = array(
		'PW_About_Us',
		'PW_Brochure_Box',
		'PW_Facebook',
		'PW_Featured_Page',
		'PW_Google_Map',
	);
	foreach ( $widget_classes as $widget_class ) {
		if ( class_exists( $widget_class ) ) {
			register_widget( $widget_class );
		}
	}
}
tests_add_filter( 'widgets_init', '_register_proteuswidgets_widgets_for_tests' );

require $_tests_dir . '/includes/bootstrap.php';
