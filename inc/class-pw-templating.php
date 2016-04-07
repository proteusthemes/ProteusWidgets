<?php
/**
 * SINGLETON
 * Class for declaring the PHP templating engine
 *
 * @package ProteusWidgets
 */

if ( ! class_exists( 'PW_Templating' ) ) {
	class PW_Templating {

		private static $instance;
		private $template_engine, $template_folder_name, $child_template_folder_name, $theme_views_folder, $child_theme_views_folder;
		private $views_folder = 'inc/widgets-views/';

		/**
		 * Private constructor to prevent creating a new instance of the *Singleton* via the `new` operator from outside of this class.
		 */
		private function __construct() {

			// Set the Plates as the PHP templating engine. Learn more: http://platesphp.com/.
			$this->template_engine = new League\Plates\Engine( trailingslashit( get_template_directory() ) . 'vendor/proteusthemes/proteuswidgets/widgets/views' );

			$this->theme_views_folder = trailingslashit( get_template_directory() ) . $this->views_folder;

			if ( file_exists( $this->theme_views_folder ) ) {

				// Set the template folder name.
				$this->template_folder_name = 'theme';

				// Set folder, where overwriting templates can be stored: http://platesphp.com/engine/folders/.
				$this->template_engine->addFolder( $this->template_folder_name, trailingslashit( get_template_directory() ) . 'inc/widgets-views', true );
			}

			if ( is_child_theme() ) {

				$this->child_theme_views_folder = trailingslashit( get_stylesheet_directory() ) . $this->views_folder;

				if ( file_exists( $this->child_theme_views_folder ) ) {

					// Set the child template folder name.
					$this->child_template_folder_name = 'child-theme';

					// Set folder in child theme, where overwriting templates can be stored.
					$this->template_engine->addFolder( $this->child_template_folder_name, trailingslashit( get_stylesheet_directory() ) . 'inc/widgets-views', true );
				}
			}
		}

		/**
		 * Static function for retrieving or instantiation of this class - Singleton
		 */
		public static function get_instance() {
			if ( null === static::$instance ) {
				static::$instance = new static();
			}

			return static::$instance;
		}

		/**
		 * Renders a template.
		 *
		 * @param string $template_name name of the template file.
		 * @param array  $template_data data used in the template.
		 * @return string, rendered template.
		 */
		public function render_template( $template_name, $template_data ) {
			if ( ! empty( $this->child_template_folder_name ) && file_exists( $this->child_theme_views_folder . $template_name . '.php' ) ) {
				return $this->template_engine->render( $this->child_template_folder_name . '::' . $template_name, $template_data );
			}
			else if ( ! empty( $this->template_folder_name ) && file_exists( $this->theme_views_folder . $template_name . '.php' ) ) {
				return $this->template_engine->render( $this->template_folder_name . '::' . $template_name, $template_data );
			}
			else {
				return $this->template_engine->render( $template_name, $template_data );
			}
		}

		/**
		 * Private clone method to prevent cloning of the instance of the *Singleton* instance.
		 *
		 * @return void
		 */
		private function __clone() {}

		/**
		 * Private unserialize method to prevent unserializing of the *Singleton* instance.
		 *
		 * @return void
		 */
		private function __wakeup() {}
	}
}
