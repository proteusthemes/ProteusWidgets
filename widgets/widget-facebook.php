<?php
/**
 * Facebook Widget
 *
 * @package ProteusWidgets
 * @since 1.0.0
 */


if ( ! class_exists( 'PW_Facebook' ) ) {
	class PW_Facebook extends WP_Widget {

		/**
		 * Register widget with WordPress.
		 */
		public function __construct() {
			parent::__construct(
				false, // ID, auto generate when false
				sprintf( 'ProteusThemes: %s', __( 'Facebook', 'proteuswidgets' ) ), // Name
				array(
					'description' => __( 'Facebook like box with some customization settings.', 'proteuswidgets' ),
				)
			);

			// color picker needed
			add_action( 'admin_enqueue_scripts', array( $this, 'add_color_picker' ) );
		}

		/**
		 * Add color picker as we need it in this widget
		 */
		public function add_color_picker() {
			wp_enqueue_style( 'wp-color-picker' );
			wp_enqueue_script( 'wp-color-picker' );
		}

		/**
		 * Front-end display of widget.
		 *
		 * @see WP_Widget::widget()
		 *
		 * @param array $args     Widget arguments.
		 * @param array $instance Saved values from database.
		 */
		public function widget( $args, $instance ) {
			extract( $args );
			$instance['title']      = apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base );
			$instance['height']     = absint( $instance['height'] );
			$instance['background'] = esc_attr( $instance['background'] );

			// params for the iframe
			// @see https://developers.facebook.com/docs/plugins/like-box-for-pages

			$fb_params = array(
				'colorscheme' => $instance['colorscheme'],
				'stream'      => 'false',
				'show_border' => 'false',
				'header'      => 'false',
				'show_faces'  => 'true',
				'width'       => 263,
				'height'      => $instance['height'],
				'href'        => $instance['like_link'],
			);

			// mustache widget-facebook template rendering
			global $mustache;
			echo $mustache->render('widget-facebook', array(
				'before-widget' => $args['before_widget'],
				'after-widget'  => $args['after_widget'],
				'title'         => ( ! empty ( $instance['title'] ) ) ? $args['before_title'] . $instance['title'] . $args['after_title'] : '',
				'http-query'    => http_build_query( $fb_params ),
				'height'        => $fb_params['height'],
				'background'    => $instance['background'],
			));
		}

		/**
		 * Sanitize widget form values as they are saved.
		 *
		 * @see WP_Widget::update()
		 *
		 * @param array $new_instance Values just sent to be saved.
		 * @param array $old_instance Previously saved values from database.
		 *
		 * @return array Updated safe values to be saved.
		 */
		public function update( $new_instance, $old_instance ) {
			$instance = array();

			$instance['title']       = wp_kses_post( $new_instance['title'] );
			$instance['colorscheme'] = sanitize_key( $new_instance['colorscheme'] );
			$instance['like_link']   = esc_url( $new_instance['like_link'] );
			$instance['height']      = absint( $new_instance['height'] );
			$instance['background']  = esc_attr( $new_instance['background'] );

			return $instance;
		}

		/**
		 * Back-end widget form.
		 *
		 * @see WP_Widget::form()
		 *
		 * @param array $instance Previously saved values from database.
		 */
		public function form( $instance ) {
				$title       = isset( $instance[ 'title' ] ) ? $instance[ 'title' ] : 'Facebook';
				$colorscheme = isset( $instance[ 'colorscheme' ] ) ? $instance[ 'colorscheme' ] : 'light';
				$like_link   = isset( $instance[ 'like_link' ] ) ? $instance[ 'like_link' ] : 'https://www.facebook.com/ProteusThemes';
				$height      = isset( $instance['height'] ) ? $instance['height'] : 290;
				$background  = isset( $instance['background'] ) ? $instance['background'] : '#ffffff';

			?>
			<p>
				<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'proteuswidgets' ); ?></label>
				<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
			</p>

			<p>
				<label for="<?php echo $this->get_field_id( 'like_link' ); ?>"><?php _e( 'FB Page to like (the whole URL):', 'proteuswidgets' ); ?></label> <br />
				<input class="widefat" id="<?php echo $this->get_field_id( 'like_link' ); ?>" name="<?php echo $this->get_field_name( 'like_link' ); ?>" type="text" value="<?php echo $like_link; ?>" />
			</p>

			<p>
				<label for="<?php echo $this->get_field_id( 'height' ); ?>"><?php _e( 'Height (in pixels):', 'proteuswidgets' ); ?></label>
				<input class="widefat" id="<?php echo $this->get_field_id( 'height' ); ?>" name="<?php echo $this->get_field_name( 'height' ); ?>" type="number" min="0" step="10" value="<?php echo esc_attr( $height ); ?>" />
			</p>

			<p>
				<label for="<?php echo $this->get_field_id( 'colorscheme' ); ?>"><?php _e( 'Color scheme:', 'proteuswidgets' ); ?></label> <br />
				<select id="<?php echo $this->get_field_id( 'colorscheme' ); ?>" name="<?php echo $this->get_field_name( 'colorscheme' ); ?>">
					<option value="light"<?php selected( $colorscheme, 'light' ); ?>><?php _e( 'Light', 'proteuswidgets' ); ?></option>
					<option value="dark"<?php selected( $colorscheme, 'dark' ); ?>><?php _e( 'Dark', 'proteuswidgets' ); ?></option>
				</select>
			</p>

			<p>
				<label for="<?php echo $this->get_field_id( 'background' ); ?>"><?php _e( 'Background color:', 'proteuswidgets' ); ?></label> <br>
				<input class="js-pt-color-picker" id="<?php echo $this->get_field_id( 'background' ); ?>" name="<?php echo $this->get_field_name( 'background' ); ?>" type="text" value="<?php echo esc_attr( $background ); ?>" data-default-color="<?php echo '#ffffff'; ?>" />
			</p>

			<?php
		}

	}
	register_widget( 'PW_Facebook' );
}