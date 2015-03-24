<?php
/**
 * Social Icons Widget
 *
 * @package ProteusWidgets
 * @since 1.0.0
 */

if ( ! class_exists( 'PW_Social_Icons' ) ) {
	class PW_Social_Icons extends PW_Widget {

		private $num_social_icons = 8;

		// Basic widget settings
		function widget_name() { return __( 'Social Icons', 'proteuswidgets' ); }
		function widget_description() { return __( 'Social Icons widget for Header of the page.', 'proteuswidgets' ); }
		function widget_class() { return 'widget-social-icons'; }

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
			$instance['target'] = ! empty ( $instance['new_tab'] ) ? '_blank' : '_self';
			$social_icons       = array();

			for ( $i=0; $i < $this->num_social_icons; $i++ ) {
				if ( ! empty ( $instance[ 'btn_link_' . $i ] ) ) {
					$curent_line = array();
					$curent_line['link'] = esc_url( $instance[ 'btn_link_' . $i ] );
					$curent_line['icon'] = sanitize_html_class( $instance[ 'icon_' . $i ] );
					array_push( $social_icons, $curent_line);
				}
			}

			// Mustache widget-social-icons template rendering
			echo $this->mustache->render( apply_filters( 'pw/widget_social_icons_view', 'widget-social-icons' ), array(
				'args'         => $args,
				'instance'     => $instance,
				'social-icons' => $social_icons,
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
			for ( $i = 0; $i < $this->num_social_icons; $i++ ) {
				$instance['btn_link_' . $i] = esc_url( $new_instance['btn_link_' . $i] );
				$instance['icon_' . $i]     = sanitize_key( $new_instance['icon_' . $i] );
			}
			$instance['new_tab'] = sanitize_key( $new_instance['new_tab'] );
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
			$btn_links = array();
			$icons = array();
			$new_tab  = empty( $instance['new_tab'] ) ? '' : $instance['new_tab'];

			for ( $i = 0; $i < $this->num_social_icons; $i++ ) {
				$btn_links[$i] = empty( $instance['btn_link_' . $i] ) ? '' : $instance['btn_link_' . $i];
				$icons[$i] = empty( $instance['icon_' . $i] ) ? '' : $instance['icon_' . $i];
			}
			?>

			<p>
				<input class="checkbox" type="checkbox" <?php checked( $new_tab, 'on'); ?> id="<?php echo $this->get_field_id( 'new_tab' ); ?>" name="<?php echo $this->get_field_name( 'new_tab' ); ?>" value="on" />
				<label for="<?php echo $this->get_field_id( 'new_tab' ); ?>"><?php _e('Open Links in New Tab', 'proteuswidgets' ); ?></label>
			</p>
			<hr>

			<?php
				foreach ( $btn_links as $i => $btn_link ) :
			?>
				<p>
					<label for="<?php echo $this->get_field_id( 'btn_link_' . $i ); ?>"><?php _e( 'Link:', 'proteuswidgets' ); ?></label> <br />
					<input style="width: 100%;" id="<?php echo $this->get_field_id( 'btn_link_' . $i ); ?>" name="<?php echo $this->get_field_name( 'btn_link_' . $i ); ?>" type="text" value="<?php echo $btn_link; ?>" />
				</p>

				<p>
					<label for="<?php echo $this->get_field_id( 'icon_' . $i ); ?>"><?php _e( 'Select social network:', 'proteuswidgets' ); ?></label>
					<select name="<?php echo $this->get_field_name( 'icon_' . $i ); ?>" id="<?php echo $this->get_field_id( 'icon_' . $i ); ?>">
						<option value="fa-facebook" <?php selected( $icons[$i], 'fa-facebook' ); ?>>Facebook</option>
						<option value="fa-twitter" <?php selected( $icons[$i], 'fa-twitter' ); ?>>Twitter</option>
						<option value="fa-youtube" <?php selected( $icons[$i], 'fa-youtube' ); ?>>Youtube</option>
						<option value="fa-skype" <?php selected( $icons[$i], 'fa-skype' ); ?>>Skype</option>
						<option value="fa-google-plus" <?php selected( $icons[$i], 'fa-google-plus' ); ?>>Google Plus</option>
						<option value="fa-pinterest" <?php selected( $icons[$i], 'fa-pinterest' ); ?>>Pinterest</option>
						<option value="fa-instagram" <?php selected( $icons[$i], 'fa-instagram' ); ?>>Instagram</option>
						<option value="fa-vine" <?php selected( $icons[$i], 'fa-vine' ); ?>>Vine</option>
						<option value="fa-tumblr" <?php selected( $icons[$i], 'fa-tumblr' ); ?>>Tumblr</option>
						<option value="fa-flickr" <?php selected( $icons[$i], 'fa-flickr' ); ?>>Flickr</option>
						<option value="fa-vimeo-square" <?php selected( $icons[$i], 'fa-vimeo-square' ); ?>>Vimeo</option>
						<option value="fa-linkedin" <?php selected( $icons[$i], 'fa-linkedin' ); ?>>Linkedin</option>
						<option value="fa-dribble" <?php selected( $icons[$i], 'fa-dribble' ); ?>>Dribble</option>
						<option value="fa-wordpress" <?php selected( $icons[$i], 'fa-wordpress' ); ?>>Wordpress</option>
						<option value="fa-rss" <?php selected( $icons[$i], 'fa-rss' ); ?>>RSS</option>
					</select>
				</p>

				<hr>

			<?php
				endforeach;
			?>

			<?php
		}

	}
	register_widget( 'PW_Social_Icons' );
}