<?php
/**
 * Skype Widget
 *
 * @package ProteusWidgets
 * @since 1.0.0
 */


if ( ! class_exists( 'PW_Skype' ) ) {
	class PW_Skype extends PW_Widget {

		// Basic widget settings
		function widget_id_base() { return 'skype'; }
		function widget_name() { return esc_html__( 'Skype', 'proteuswidgets' ); }
		function widget_description() { return esc_html__( 'Linkable block with Skype or telephone icon.', 'proteuswidgets' ); }
		function widget_class() { return 'widget-skype'; }

		/**
		 * Register widget with WordPress.
		 */
		public function __construct() {
			parent::__construct();
		}

		/**
		 * Front-end display of widget.
		 *
		 * @see WP_Widget::widget()
		 *
		 * @param array $args
		 * @param array $instance
		 */
		public function widget( $args, $instance ) {
			// Prepare data for mustache template
			$instance['icon'] = 'skype' == substr( $instance['skype_username'], 0, 5 ) ? 'skype' : 'phone';

			// Mustache widget-skype template rendering
			echo $this->mustache->render( apply_filters( 'pw/widget_skype_view', 'widget-skype' ), array(
				'args'     => $args,
				'instance' => $instance,
			));
		}

		/**
		 * Sanitize widget form values as they are saved.
		 *
		 * @param array $new_instance The new options
		 * @param array $old_instance The previous options
		 */
		public function update( $new_instance, $old_instance ) {
			$instance = array();

			$instance['title']          = wp_kses_post( $new_instance['title'] );
			$instance['skype_username'] = wp_kses_post( $new_instance['skype_username'] );

			return $instance;
		}

		/**
		 * Back-end widget form.
		 *
		 * @param array $instance The widget options
		 */
		public function form( $instance ) {
			$title          = empty( $instance['title'] ) ? '' : $instance['title'];
			$skype_username = empty( $instance['skype_username'] ) ? '' : $instance['skype_username'];

			?>

			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title:', 'proteuswidgets' ); ?></label>
				<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
			</p>

			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'skype_username' ) ); ?>"><?php esc_html_e( 'Skype username:', 'proteuswidgets' ); ?></label>
				<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'skype_username' ) ); ?>" placeholder="skype:your_skype_username" name="<?php echo esc_attr( $this->get_field_name( 'skype_username' ) ); ?>" type="text" value="<?php echo esc_attr( $skype_username ); ?>" />
				<small class="skype-widget-small-text"><?php printf( esc_html__( 'Examples of use: %1$sskype:your_skype_username%2$s or %1$stel:your_phone_number%2$s.', 'proteuswidgets' ), '<code>', '</code>' ); ?></small>
			</p>

			<?php
		}
	}
}