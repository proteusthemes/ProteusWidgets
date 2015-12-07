<?php

/*
 * Class for declaring the PHP templating
 */

if ( ! class_exists( 'PW_Templating' ) ) {
	class PW_Templating {

		public static function init() {
			/*
			 * Set the Plates as the PHP templating engine
			 * Learn more: http://platesphp.com/
			 */
			$plates = new League\Plates\Engine( trailingslashit( get_template_directory() ) . 'vendor/proteusthemes/proteuswidgets/widgets/views' );

			// http://platesphp.com/engine/folders/
			$plates->addFolder( 'theme', trailingslashit( get_template_directory() ) . 'inc/widgets-views', true );

			return $plates;
		}

	}
}