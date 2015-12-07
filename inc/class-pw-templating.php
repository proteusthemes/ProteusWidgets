<?php

/*
 * Class for declaring the PHP templating
 */

if ( ! class_exists( 'PW_Templating' ) ) {
	class PW_Templating {

		private $template_engine;

		function __construct() {
			/*
			 * Set the Plates as the PHP templating engine
			 * Learn more: http://platesphp.com/
			 */
			$this->template_engine = new League\Plates\Engine( trailingslashit( get_template_directory() ) . 'vendor/proteusthemes/proteuswidgets/widgets/views' );
		}

		public function setup() {
			// http://platesphp.com/engine/folders/
			$this->template_engine->addFolder( 'theme', trailingslashit( get_template_directory() ) . 'inc/widgets-views', true );

			return $this->template_engine;
		}

	}
}