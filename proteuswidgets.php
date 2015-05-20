<?php

// Path/URL to root of this plugin, with trailing slash
define( 'PW_PATH', apply_filters( 'pw/dir_path', get_template_directory() . '/vendor/proteusthemes/proteuswidgets/' ) );
define( 'PW_URL', apply_filters( 'pw/dir_url', get_template_directory_uri() . '/vendor/proteusthemes/proteuswidgets/' ) );

//include php files
require_once( PW_PATH . 'inc/class-pw-functions.php');
require_once( PW_PATH . 'inc/class-pw-widget.php');

/**
* ProteusWidgets class, so we don't have to worry about namespace
*/
class ProteusWidgets {
	/**
	 * List of widgets
	 * @var array
	 */
	public $widgets;

	function __construct() {
		// initialize widgets array
		$this->widgets = array(
			'widget-about-us',
			'widget-author',
			'widget-banner',
			'widget-brochure-box',
			'widget-facebook',
			'widget-featured-page',
			'widget-google-map',
			'widget-icon-box',
			'widget-opening-time',
			'widget-skype',
			'widget-social-icons',
			'widget-testimonials',
		);

		// actions
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_js_css' ), 20 );
		add_action( 'widgets_init', array( $this, 'widgets_init' ) );
		add_action( 'after_setup_theme', array( $this, 'after_theme_setup' ) , 11 );

		// filters
		add_filter( 'kses_allowed_protocols', array( $this, 'kses_allowed_protocols' ) );

		// plugin activation hook
		register_activation_hook( __FILE__, array( $this, 'plugin_activation' ) );
	}


	/**
	 * Enqueue the JS and CSS files for backend (admin area) with the right action
	 * @see admin_enqueue_scripts
	 * @return void
	 */
	public static function admin_enqueue_js_css() {
		//media uploder include files
		wp_enqueue_media();
		wp_enqueue_script( 'pw-media-uploader', PW_URL . '/assets/js/BrochureAdmin.js', array( 'jquery' ), '1.0', true );

		wp_enqueue_script( 'pw-admin-script', PW_URL . 'assets/js/admin.js' , array( 'jquery', 'underscore', 'backbone', 'pt-mustache' ) );

		// provide the global variable to the `pw-admin-script`
		wp_localize_script( 'pw-admin-script', 'ProteusWidgetsAdminVars', array(
			'urlToPlugin' => PW_URL,
			'ptTextReplacementEnabled' => apply_filters( 'pw/proteus_themes_text_replacement_enabled', true ),
		) );

		wp_enqueue_style( 'pw-admin-style', PW_URL . 'assets/stylesheets/admin.css', array( 'font-awesome' ) );
	}

	/**
	 * Require all widgets
	 */
	public function widgets_init() {
		foreach ( apply_filters( 'pw/loaded_widgets', $this->widgets ) as $filename ) {
			require_once sprintf( '%swidgets/%s.php', PW_PATH, $filename );
		}
	}

	/**
	 * Adds theme support - thumbnail for featured page widget
	 */
	public function after_theme_setup() {
		$page_box_image_size = apply_filters( 'pw/featured_page_widget_page_box_image_size', array( 'width' => 360, 'height' => 240, 'crop' => true ) );
		$inline_image_size = apply_filters( 'pw/featured_page_widget_inline_image_size', array( 'width' => 100, 'height' => 75, 'crop' => true ) );

		if ( false === get_theme_support( 'post-thumbnails' ) ) {
			add_theme_support( 'post-thumbnails' );
			add_image_size( 'pw-page-box', $page_box_image_size['width'], $page_box_image_size['height'], $page_box_image_size['crop'] );
			add_image_size( 'pw-inline', $inline_image_size['width'], $inline_image_size['height'], $inline_image_size['crop'] );
		}
		else {
			add_image_size( 'pw-page-box', $page_box_image_size['width'], $page_box_image_size['height'], $page_box_image_size['crop'] );
			add_image_size( 'pw-inline', $inline_image_size['width'], $inline_image_size['height'], $inline_image_size['crop'] );
		}
	}

	/**
	 * Add more allowed protocols
	 *
	 * @link https://developer.wordpress.org/reference/functions/wp_allowed_protocols/
	 */
	public static function kses_allowed_protocols( $protocols ) {
		return array_merge( $protocols, array( 'skype' ) );
	}
}
$ProteusWidgets = new ProteusWidgets();