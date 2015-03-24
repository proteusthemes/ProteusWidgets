<?php
/**
 * Banner Widget
 *
 * @package ProteusWidgets
 * @since 1.0.0
 */

if ( ! class_exists( 'PW_Banner' ) ) {
	class PW_Banner extends PW_Widget {

		// Basic widget settings
		function widget_name() { return __( 'Banner', 'proteuswidgets' ); }
		function widget_description() { return __( 'Banner widget for the Sidebar and Page Builder..', 'proteuswidgets' ); }
		function widget_class() { return 'widget-banner'; }

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

			$title    = empty( $instance['title'] ) ? '' : $instance['title'];
			$content  = empty( $instance['content'] ) ? '' : $instance['content'];
			$link     = empty( $instance['link'] ) ? '' : $instance['link'];
			$open_new = empty( $instance['open_new'] ) ? '' : $instance['open_new'];

			// Mustache widget-banner template rendering
			echo $this->mustache->render( apply_filters( 'pw/widget_banner_view', 'widget-banner' ), array(
				'before-widget' => $args['before_widget'],
				'after-widget'  => $args['after_widget'],
				'title'         => $title,
				'content'       => $content,
				'link'          => esc_url( $link ),
				'link-target'   => ( '1' == $open_new ) ? '_blank' : '_self',
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

			$instance['title']    = wp_kses_post( $new_instance['title'] );
			$instance['content']  = wp_kses_post( $new_instance['content'] );
			$instance['link']     = esc_url( $new_instance['link'] );
			$instance['open_new'] = wp_kses_post( $new_instance['open_new'] );

			return $instance;
		}

		/**
		 * Back-end widget form.
		 *
		 * @param array $instance The widget options
		 */
		public function form( $instance ) {
			$title    = empty( $instance['title'] ) ? '' : $instance['title'];
			$content  = empty( $instance['content'] ) ? '' : $instance['content'];
			$link     = empty( $instance['link'] ) ? '' : $instance['link'];
			$open_new = empty( $instance['open_new'] ) ? '' : $instance['open_new'];

			?>

			<p>
				<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'proteuswidgets'); ?></label>
				<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
			</p>

			<p>
				<label for="<?php echo $this->get_field_id( 'content' ); ?>"><?php _e( 'Content:', 'proteuswidgets'); ?></label>
				<input class="widefat" id="<?php echo $this->get_field_id( 'content' ); ?>" name="<?php echo $this->get_field_name( 'content' ); ?>" type="text" value="<?php echo esc_attr( $content ); ?>" />
			</p>

			<p>
				<label for="<?php echo $this->get_field_id( 'link' ); ?>"><?php _e( 'Link:', 'proteuswidgets'); ?></label>
				<input class="widefat" id="<?php echo $this->get_field_id( 'link' ); ?>" name="<?php echo $this->get_field_name( 'link' ); ?>" type="text" value="<?php echo esc_attr( $link ); ?>" />
				<input type="checkbox" name="<?php echo $this->get_field_name( 'open_new' ); ?>" value="1" <?php checked( $open_new, 1 ); ?>>
				<?php _e( 'Open link in a new window/tab', 'proteuswidgets' ); ?>
			</p>

			<?php
		}

	} // Class PW_Banner
	register_widget( 'PW_Banner' );
}