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
		function widget_name() { return __( 'Skype', 'proteuswidgets' ); }
		function widget_description() { return __( 'Skype button for sidebar.', 'proteuswidgets' ); }
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

			$title          = empty( $instance['title'] ) ? '' : $instance['title'];
			$skype_username = empty( $instance['skype_username'] ) ? '' : $instance['skype_username'];


			// Mustache widget-skype template rendering
			echo $this->mustache->render( apply_filters( 'pw/widget_skype_view', 'widget-skype' ), array(
				'before-widget'  => $args['before_widget'],
				'after-widget'   => $args['after_widget'],
				'title'          => $title,
				'skype-or-phone' => 'skype' == substr( $skype_username, 0, 5 ) ? 'skype' : 'phone',
				'skype-username' => $skype_username,
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
				<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'proteuswidgets'); ?></label>
				<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
			</p>

			<p>
				<label for="<?php echo $this->get_field_id( 'skype_username' ); ?>"><?php _e( 'Skype username:', 'proteuswidgets'); ?></label>
				<input class="widefat" id="<?php echo $this->get_field_id( 'skype_username' ); ?>" placeholder="skype:your_skype_username" name="<?php echo $this->get_field_name( 'skype_username' ); ?>" type="text" value="<?php echo esc_attr( $skype_username ); ?>" />
				<small class="skype-widget-small-text">Examples of use: <br><code>skype:your_skype_username</code> or <code>tel:your_phone_number</code></small>
			</p>

			<?php
		}

	} // Class PW_Banner
	register_widget( 'PW_Skype' );
}