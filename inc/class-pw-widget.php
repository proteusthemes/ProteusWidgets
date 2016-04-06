<?php

/*
 * Abstract class that extends WP_Widget and will be extended by individual widget
 */

if ( ! class_exists( 'PW_Widget' ) ) {
	abstract class PW_Widget extends WP_Widget {

		protected $template_engine, $widget_id_base, $widget_class, $widget_description, $widget_name;

		public function __construct() {
			parent::__construct(
				'pw_' . $this->widget_id_base,
				sprintf( 'ProteusThemes: %s', $this->widget_name ),
				array(
					'description' => $this->widget_description,
					'classname'   => $this->widget_class,
				)
			);

			// Define the ProteusWidgets PHP templating engine *Singleton*.
			$this->template_engine = PW_Templating::get_instance();
		}

		/**
		 * Helper function to order items by ids.
		 * Used for sorting widget setting items.
		 *
		 * @param int $a first comparable parameter.
		 * @param int $b second comparable parameter.
		 */
		function sort_by_id( $a, $b ) {
			return $a['id'] - $b['id'];
		}
	}
}
