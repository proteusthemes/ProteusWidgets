<?php

/*
 * Abstract class that extends WP_Widget and will be extended by individual widget
 *
 *
*/

if ( ! class_exists( 'PW_Widget' ) ) {
	abstract class PW_Widget extends WP_Widget {

		protected $mustache;

		abstract function widget_class();
		abstract function widget_description();
		abstract function widget_name();

		public function __construct() {
			parent::__construct(
				false, // ID, auto generate when false
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
					'loader' => new Mustache_Loader_FilesystemLoader( PROTEUSWIDGETS_PATH . 'widgets/views' ),
				)
			);
		}

	}
}