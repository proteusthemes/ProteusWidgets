<?php
/**
 * Social Icons Widget
 *
 * @package ProteusWidgets
 * @since 0.1.0
 */

if ( ! class_exists( 'PW_Social_Icons' ) ) {
	class PW_Social_Icons extends WP_Widget {

		private $num_social_icons = 8;

		/**
		 * Register widget with WordPress.
		 */
		public function __construct() {
			parent::__construct(
				false, // ID, auto generate when false
				sprintf( 'ProteusThemes: %s', __( 'Social Icons', 'proteuswidgets' ) ), // Name
				array(
					'description' => __( 'Social Icons for Header of the page.', 'proteuswidgets' ),
					'classname'   => 'widget-social-icons',
				)
			);
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
			$non_empty_fields = array();

			for ( $i=0; $i < $this->num_social_icons; $i++ ) {
				if ( ! empty ( $instance[ 'btn_link_' . $i ] ) ) {
					$non_empty_fields[$instance[ 'icon_' . $i ]] = $instance[ 'btn_link_' . $i ];
				}
			}

			echo $args['before_widget'];

			foreach ( $non_empty_fields as $social_network_class => $url ) :
			?>
				<a class="social-icons__link" href="<?php echo $url; ?>" <?php echo empty ( $instance['new_tab'] ) ? '' : 'target="_blank"'; ?>><i class="fa  <?php echo $social_network_class; ?>"></i></a>
			<?php
			endforeach;

			echo $args['after_widget'];
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
					<label for="<?php echo $this->get_field_id( 'btn_link_' . $i ); ?>"><?php _ex( 'Link', 'proteuswidgets' ); ?>:</label> <br />
					<input style="width: 100%;" id="<?php echo $this->get_field_id( 'btn_link_' . $i ); ?>" name="<?php echo $this->get_field_name( 'btn_link_' . $i ); ?>" type="text" value="<?php echo $btn_link; ?>" />
				</p>

				<p>
					<label for="<?php echo $this->get_field_id( 'icon_' . $i ); ?>"><?php _ex( 'Select social network', 'proteuswidgets' ); ?>:</label>
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

	} // class PW_Social_Icons
	register_widget( 'PW_Social_Icons' );
}