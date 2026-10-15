<?php

/*
 * Abstract class that extends WP_Widget and will be extended by individual widget
 *
 *
*/

if ( ! class_exists( 'PW_Widget' ) ) {
	abstract class PW_Widget extends WP_Widget {

		protected $mustache;

		abstract function widget_id_base();
		abstract function widget_class();
		abstract function widget_description();
		abstract function widget_name();

		public function __construct() {
			parent::__construct(
				'pw_' . $this->widget_id_base(),
				sprintf( 'ProteusThemes: %s', $this->widget_name() ), // Name
				array(
					'description' => $this->widget_description(),
					'classname'   => $this->widget_class(),
				)
			);

			// Include autoload from composer for PHP mustache
			require_once( PW_PATH . 'vendor/autoload.php' );

			// Set the mustache engine
			// Learn more: https://github.com/bobthecow/mustache.php/wiki/Template-Loading
			$this->mustache = new \Mustache\Engine(
				array(
					'loader' => new \Mustache\Loader\CascadingLoader(
						array(
							// FilesystemLoader from configurable path
							new \Mustache\Loader\FilesystemLoader( apply_filters( 'pw/widget_views_path', PW_PATH . 'widgets/views' ) ),
							// Default FilesystemLoader from this plugin
							new \Mustache\Loader\FilesystemLoader( PW_PATH . 'widgets/views' ),
							// Default string longer, if nothing works from above
							new \Mustache\Loader\StringLoader,
						)
					)
				)
			);
		}

	}
}