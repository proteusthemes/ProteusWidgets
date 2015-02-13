<?php
/**
 * Banner Widget
 *
 * @package ProteusWidgets
 */

if ( ! class_exists( 'PW_Banner' ) ) {
	class PW_Banner extends WP_Widget {

		/**
		 * Register widget with WordPress.
		 */
		public function __construct() {
			parent::__construct(
				false, // ID, auto generate when false
				_x( 'ProteusThemes: Banner' , 'backend', 'proteuswidgets'), // Name
				array(
					'description' => _x( 'Banner for Page Builder.', 'backend', 'proteuswidgets'),
					'classname'   => 'widget-banner',
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
			$content = empty( $instance['content'] ) ? '' : $instance['content'];
			$link = empty( $instance['link'] ) ? '' : $instance['link'];
			$open_new = empty( $instance['open_new'] ) ? '' : $instance['open_new'];

			echo $args['before_widget'];
			?>
				<div class="banner">
					<a href="<?php echo $link ?>" target="<?php echo ( '1' == $open_new ) ? '_blank' : '_self' ?>">
						<div class="banner__title">
							<?php echo $title; ?>
						</div>
						<div class="banner__content">
							<?php echo $content; ?>
						</div>
					</a>
				</div>
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
			$instance['content'] = wp_kses_post( $new_instance['content'] );
			$instance['link'] = wp_kses_post( $new_instance['link'] );
			$instance['open_new'] = wp_kses_post( $new_instance['open_new'] );

			return $instance;
		}

		/**
		 * Back-end widget form.
		 *
		 * @param array $instance The widget options
		 */
		public function form( $instance ) {
			$title = empty( $instance['title'] ) ? '' : $instance['title'];
			$content = empty( $instance['content'] ) ? '' : $instance['content'];
			$link = empty( $instance['link'] ) ? '' : $instance['link'];
			$open_new = empty( $instance['open_new'] ) ? '' : $instance['open_new'];

			?>

			<p>
				<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _ex( 'Title:', 'backend', 'proteuswidgets'); ?></label>
				<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
			</p>

			<p>
				<label for="<?php echo $this->get_field_id( 'content' ); ?>"><?php _ex( 'Content:', 'backend', 'proteuswidgets'); ?></label>
				<input class="widefat" id="<?php echo $this->get_field_id( 'content' ); ?>" name="<?php echo $this->get_field_name( 'content' ); ?>" type="text" value="<?php echo esc_attr( $content ); ?>" />
			</p>

			<p>
				<label for="<?php echo $this->get_field_id( 'link' ); ?>"><?php _ex( 'Link:', 'backend', 'proteuswidgets'); ?></label>
				<input class="widefat" id="<?php echo $this->get_field_id( 'link' ); ?>" name="<?php echo $this->get_field_name( 'link' ); ?>" type="text" value="<?php echo esc_attr( $link ); ?>" />
				<input type="checkbox" name="<?php echo $this->get_field_name( 'open_new' ); ?>" value="1" <?php checked( $open_new, 1 ); ?>>Open link in a new window/tab

			</p>

			<?php
		}

	} // Class PW_Banner
	register_widget( 'PW_Banner' );
}