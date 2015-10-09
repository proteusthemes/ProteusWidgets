<?php

/*
 * Abstract class that extends WP_Widget and will be extended by individual widget
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

			/*
			 * Set the mustache engine
			 * Learn more: https://github.com/bobthecow/mustache.php/wiki/Template-Loading
			 */
			$this->mustache = new Mustache_Engine(
				array(
					'cache'  => PW_PATH . '/tmp/cache',
					'loader' => new Mustache_Loader_CascadingLoader(
						array(
							// FilesystemLoader from configurable path
							new Mustache_Loader_FilesystemLoader( apply_filters( 'pw/widget_views_path', PW_PATH . '/widgets/views' ) ),
							// Default FilesystemLoader from this plugin
							new Mustache_Loader_FilesystemLoader( PW_PATH . '/widgets/views' ),
							// Default string longer, if nothing works from above
							new Mustache_Loader_StringLoader,
						)
					)
				)
			);
		}

	}
}