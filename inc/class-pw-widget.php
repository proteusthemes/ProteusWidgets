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
			 * Set the Plates
			 * Learn more: http://platesphp.com/
			 */
			$plates = new League\Plates\Engine( trailingslashit( get_template_directory() ) . 'vendor/proteusthemes/proteuswidgets/widgets/views' );

			// http://platesphp.com/engine/folders/
			$plates->addFolder( 'theme', trailingslashit( get_template_directory() ) . 'inc/widgets-views', true );

			$this->mustache = $plates;
		}

	}
}