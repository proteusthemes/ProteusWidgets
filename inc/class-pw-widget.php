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

			// include autoload from composer for PHP mustache
			require_once( PROTEUSWIDGETS_PATH . 'vendor/autoload.php' );

			// set the mustache engine
			$this->mustache = new Mustache_Engine(
				array(
					'loader' => new Mustache_Loader_CascadingLoader(
						array(
							// first loader for the theme overriding the views via filters
							new Mustache_Loader_FilesystemLoader( apply_filters( 'pw/widget_views_path' , PROTEUSWIDGETS_PATH . 'widgets/views' ) ),
							// second/default loader
							new Mustache_Loader_FilesystemLoader( PROTEUSWIDGETS_PATH . 'widgets/views' ),
						)
					)
				)
			);
		}

	}
}