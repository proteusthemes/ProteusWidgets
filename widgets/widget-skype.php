<?php
/**
 * Skype Widget
 *
 * @package ProteusWidgets
 */

if ( ! class_exists( 'PW_Skype' ) ) {
	class PW_Skype extends WP_Widget {

		/**
		 * Register widget with WordPress.
		 */
		public function __construct() {
			parent::__construct(
				false, // ID, auto generate when false
				_x( 'ProteusThemes: Skype' , 'backend', 'proteuswidgets'), // Name
				array(
					'description' => _x( 'Skype button for sidebar.', 'backend', 'proteuswidgets'),
					'classname'   => 'widget-skype',
				)
			);
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

			$title = empty( $instance['title'] ) ? '' : $instance['title'];
			$skype_username = empty( $instance['skype_username'] ) ? '' : $instance['skype_username'];

			echo $args['before_widget'];
			?>
				<a class="skype-button" href="<?php echo $skype_username ?>">
					<?php if ( 'skype' == substr( $skype_username, 0, 5 ) ) : ?>
							<i class="fa  fa-skype"></i>
					<?php else :?>
							<i class="fa  fa-phone"></i>
					<?php endif; ?>
					<span class="skype-button__title"><?php echo $title; ?></span>
				</a>
			<?php
			echo $args['after_widget'];
		}

		/**
		 * Sanitize widget form values as they are saved.
		 *
		 * @param array $new_instance The new options
		 * @param array $old_instance The previous options
		 */
		public function update( $new_instance, $old_instance ) {
			$instance = array();

			$instance['title'] = wp_kses_post( $new_instance['title'] );
			$instance['skype_username'] = wp_kses_post( $new_instance['skype_username'] );

			return $instance;
		}

		/**
		 * Back-end widget form.
		 *
		 * @param array $instance The widget options
		 */
		public function form( $instance ) {
			$title = empty( $instance['title'] ) ? '' : $instance['title'];
			$skype_username = empty( $instance['skype_username'] ) ? '' : $instance['skype_username'];

			?>

			<p>
				<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _ex( 'Title:', 'backend', 'proteuswidgets'); ?></label>
				<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
			</p>

			<p>
				<label for="<?php echo $this->get_field_id( 'skype_username' ); ?>"><?php _ex( 'Skype username:', 'backend', 'proteuswidgets'); ?></label>
				<input class="widefat" id="<?php echo $this->get_field_id( 'skype_username' ); ?>" placeholder="skype:your_skype_username" name="<?php echo $this->get_field_name( 'skype_username' ); ?>" type="text" value="<?php echo esc_attr( $skype_username ); ?>" />
				<small class="skype-widget-small-text">Examples of use: <br><code>skype:your_skype_username</code> or <code>tel:your_phone_number</code></small>
			</p>

			<?php
		}

	} // Class PW_Banner
	register_widget( 'PW_Skype' );
}