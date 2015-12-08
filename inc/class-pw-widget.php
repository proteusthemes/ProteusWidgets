<?php

/*
 * Abstract class that extends WP_Widget and will be extended by individual widget
 */

if ( ! class_exists( 'PW_Widget' ) ) {
	abstract class PW_Widget extends WP_Widget {

		protected $template_engine;

		abstract function widget_id_base();
		abstract function widget_class();
		abstract function widget_description();
		abstract function widget_name();

		public function __construct() {
			parent::__construct(
				'pw_' . $this->widget_id_base(),
				sprintf( 'ProteusThemes: %s', $this->widget_name() ),
				array(
					'description' => $this->widget_description(),
					'classname'   => $this->widget_class(),
				)
			);

			// Define the ProteusWidgets PHP templating engine *Singleton*
			$this->template_engine = PW_Templating::get_instance();
		}

	}
}