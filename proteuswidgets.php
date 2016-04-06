<?php

// Path/URL to root of this composer package, without trailing slash
if ( ! defined( 'PW_PATH' ) ) {
	define( 'PW_PATH', apply_filters( 'pw/dir_path', get_template_directory() . '/vendor/proteusthemes/proteuswidgets' ) );
}

if ( ! defined( 'PW_URL' ) ) {
	define( 'PW_URL', apply_filters( 'pw/dir_url', get_template_directory_uri() . '/vendor/proteusthemes/proteuswidgets' ) );
}

/**
* ProteusWidgets class, so we don't have to worry about namespace
*/
class ProteusWidgets {

	function __construct() {
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_js_css' ), 20 );
		add_action( 'after_setup_theme', array( $this, 'after_theme_setup' ), 11 );
	}


	/**
	 * Enqueue the JS and CSS files for backend (admin area) with the right action
	 * @see admin_enqueue_scripts
	 * @return void
	 */
	public static function admin_enqueue_js_css() {
		// Media uploader include files
		wp_enqueue_media();
		wp_enqueue_script( 'pw-media-uploader', PW_URL . '/assets/js/BrochureAdmin.js', array( 'jquery' ), '1.0', true );

		wp_enqueue_script( 'pw-admin-script', PW_URL . '/assets/js/admin.js' , array( 'jquery', 'underscore', 'backbone', 'jquery-ui-sortable' , apply_filters( 'pw/theme_prefix', '' ) . 'mustache.js' ) );

		// Provide variables to the admin.js script
		wp_localize_script( 'pw-admin-script', 'ProteusWidgetsAdminVars', array(
			'urlToPlugin'              => PW_URL,
			'ptTextReplacementEnabled' => apply_filters( 'pw/proteus_themes_text_replacement_enabled', true ),
			'defaultSocialIcon'        => apply_filters( 'pw/default_social_icon', 'fa-facebook' ),
		) );

		// Enqueue admin dashboard CSS
		wp_enqueue_style( 'pw-admin-style', PW_URL . '/assets/stylesheets/admin.css', array( apply_filters( 'pw/theme_prefix', '' ) . 'font-awesome' ) );
	}

	/**
	 * Add theme support - thumbnail for featured page widget
	 */
	public static function after_theme_setup() {
		$page_box_image_size = apply_filters( 'pw/featured_page_widget_page_box_image_size', array( 'width' => 360, 'height' => 240, 'crop' => true ) );
		$inline_image_size = apply_filters( 'pw/featured_page_widget_inline_image_size', array( 'width' => 100, 'height' => 75, 'crop' => true ) );
		$latest_news_image_size = apply_filters( 'pw/latest_news_widget_image_size', array( 'width' => 360, 'height' => 204, 'crop' => true ) );

		add_image_size( 'pw-page-box', $page_box_image_size['width'], $page_box_image_size['height'], $page_box_image_size['crop'] );
		add_image_size( 'pw-inline', $inline_image_size['width'], $inline_image_size['height'], $inline_image_size['crop'] );
		add_image_size( 'pw-latest-news', $latest_news_image_size['width'], $latest_news_image_size['height'], $latest_news_image_size['crop'] );
	}
}