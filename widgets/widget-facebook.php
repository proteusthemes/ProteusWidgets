<?php
/**
 * Facebook Widget
 *
 * @package ProteusWidgets
 * @since 1.0.0
 */


if ( ! class_exists( 'PW_Facebook' ) ) {
	class PW_Facebook extends PW_Widget {

		// Basic widget settings
		function widget_id_base() { return 'facebook'; }
		function widget_name() { return esc_html__( 'Facebook', 'proteuswidgets' ); }
		function widget_description() { return esc_html__( 'Facebook like box with some customization settings.', 'proteuswidgets' ); }
		function widget_class() { return null; }

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
		 * @param array $args     Widget arguments.
		 * @param array $instance Saved values from database.
		 */
		public function widget( $args, $instance ) {
			// Prepare data for mustache template
			$instance['title']  = $args['before_title'] . apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base ) . $args['after_title'];
			$instance['width']  = absint( $instance['width'] );
			$instance['height'] = absint( $instance['height'] );

			// params for the iframe
			// @see https://developers.facebook.com/docs/plugins/like-box-for-pages (Out-dated)
			// @see https://developers.facebook.com/docs/plugins/page-plugin

			$fb_params = array(
				'href'          => $instance['like_link'],
				'width'         => $instance['width'],
				'height'        => $instance['height'],
				'hide_cover'    => ! empty( $instance['hide_cover'] ),
				'show_facepile' => empty( $instance['show_facepile'] ),
				'show_posts'    => ! empty( $instance['show_posts'] ),
				'small_header'  => ! empty( $instance['small_header'] ),
			);

			// Mustache widget-facebook template rendering
			echo $this->mustache->render( apply_filters( 'pw/widget_facebook_view', 'widget-facebook' ), array(
				'args'       => $args,
				'instance'   => $instance,
				'http-query' => http_build_query( $fb_params ),
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

			$instance['title']         = wp_kses_post( $new_instance['title'] );
			$instance['like_link']     = esc_url_raw( $new_instance['like_link'] );
			$instance['width']         = absint( $new_instance['width'] );
			$instance['height']        = absint( $new_instance['height'] );
			$instance['hide_cover']    = ! empty( $new_instance['hide_cover'] ) ? sanitize_key( $new_instance['hide_cover'] ) : '';
			$instance['show_facepile'] = ! empty( $new_instance['show_facepile'] ) ? sanitize_key( $new_instance['show_facepile'] ) : '';
			$instance['show_posts']    = ! empty( $new_instance['show_posts'] ) ? sanitize_key( $new_instance['show_posts'] ) : '';
			$instance['small_header']  = ! empty( $new_instance['small_header'] ) ? sanitize_key( $new_instance['small_header'] ) : '';

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
				$title         = isset( $instance['title'] ) ? $instance['title'] : 'Facebook';
				$like_link     = isset( $instance['like_link'] ) ? $instance['like_link'] : 'https://www.facebook.com/ProteusThemes';
				$width         = isset( $instance['width'] ) ? $instance['width'] : 340;
				$height        = isset( $instance['height'] ) ? $instance['height'] : 500;
				$hide_cover    = isset( $instance['hide_cover'] ) ? $instance['hide_cover'] : '';
				$show_facepile = isset( $instance['show_facepile'] ) ? $instance['show_facepile'] : '';
				$show_posts    = isset( $instance['show_posts'] ) ? $instance['show_posts'] : '';
				$small_header  = isset( $instance['small_header'] ) ? $instance['small_header'] : '';

			?>
			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title:', 'proteuswidgets' ); ?></label>
				<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
			</p>

			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'like_link' ) ); ?>"><?php esc_html_e( 'FB page to like (the whole URL):', 'proteuswidgets' ); ?></label> <br />
				<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'like_link' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'like_link' ) ); ?>" type="text" value="<?php echo esc_url( $like_link ); ?>" />
			</p>

			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'width' ) ); ?>"><?php esc_html_e( 'Width (in pixels, Min: 180, Max: 500):', 'proteuswidgets' ); ?></label>
				<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'width' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'width' ) ); ?>" type="number" min="0" step="10" value="<?php echo esc_attr( $width ); ?>" />
			</p>

			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'height' ) ); ?>"><?php esc_html_e( 'Height (in pixels, Min: 70):', 'proteuswidgets' ); ?></label>
				<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'height' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'height' ) ); ?>" type="number" min="0" step="10" value="<?php echo esc_attr( $height ); ?>" />
			</p>

			<p>
				<input class="checkbox" type="checkbox" <?php checked( $hide_cover, 'on' ); ?> id="<?php echo esc_attr( $this->get_field_id( 'hide_cover' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'hide_cover' ) ); ?>" value="on" />
				<label for="<?php echo esc_attr( $this->get_field_id( 'hide_cover' ) ); ?>"><?php esc_html_e( 'Hide cover photo', 'proteuswidgets' ); ?></label>

				<input class="checkbox" type="checkbox" <?php checked( $show_facepile, 'on' ); ?> id="<?php echo esc_attr( $this->get_field_id( 'show_facepile' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'show_facepile' ) ); ?>" value="on" />
				<label for="<?php echo esc_attr( $this->get_field_id( 'show_facepile' ) ); ?>"><?php esc_html_e( 'Hide friend\'s faces', 'proteuswidgets' ); ?></label>

				<input class="checkbox" type="checkbox" <?php checked( $show_posts, 'on' ); ?> id="<?php echo esc_attr( $this->get_field_id( 'show_posts' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'show_posts' ) ); ?>" value="on" />
				<label for="<?php echo esc_attr( $this->get_field_id( 'show_posts' ) ); ?>"><?php esc_html_e( 'Show page posts', 'proteuswidgets' ); ?></label>

				<input class="checkbox" type="checkbox" <?php checked( $small_header, 'on' ); ?> id="<?php echo esc_attr( $this->get_field_id( 'small_header' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'small_header' ) ); ?>" value="on" />
				<label for="<?php echo esc_attr( $this->get_field_id( 'small_header' ) ); ?>"><?php esc_html_e( 'Use small header', 'proteuswidgets' ); ?></label>
			</p>

			<?php
		}

	}
}