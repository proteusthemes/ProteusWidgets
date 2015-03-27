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
		private $current_widget_id;

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
			if ( ! isset( $instance['social_icons'] ) ) {
				$instance['social_icons'] = array(
					array(
						'link' => '',
						'icon' => '',
					)
				);
			}

			$instance['social_icons'] = PWFunctions::reorder_widget_array_key_values( $instance['social_icons'] );
			// Escape data
			for ($i=0; $i < count( $instance['social_icons'] ) ; $i++) {
				$instance['social_icons'][$i]['link'] = esc_url( $instance['social_icons'][$i]['link'] );
				$instance['social_icons'][$i]['icon'] = esc_attr( $instance['social_icons'][$i]['icon'] );
			}
			$instance['target'] = ! empty ( $instance['new_tab'] ) ? '_blank' : '_self';

			// Mustache widget-social-icons template rendering
			echo $this->mustache->render( apply_filters( 'pw/widget_social_icons_view', 'widget-social-icons' ), array(
				'args'         => $args,
				'instance'     => $instance,
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

			foreach ( $new_instance['social_icons'] as $key => $social_icon ) {
				$instance['social_icons'][$key]['id']   = sanitize_key( $social_icon['id'] );
				$instance['social_icons'][$key]['link'] = sanitize_text_field( $social_icon['link'] );
				$instance['social_icons'][$key]['icon'] = sanitize_html_class( $social_icon['icon'] );
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
			if ( ! isset( $instance['social_icons'] ) ) {
				$instance['social_icons'] = array(
					array(
						'id'   => 1,
						'link' => '',
						'icon' => '',
					)
				);
			}

			$new_tab  = empty( $instance['new_tab'] ) ? '' : $instance['new_tab'];

			// Page Builder fix when using repeating fields
			if ( 'temp' === $this->id ) {
				$this->current_widget_id = $this->number;
			}
			else {
				$this->current_widget_id = $this->id;
			}
		?>

			<p>
				<input class="checkbox" type="checkbox" <?php checked( $new_tab, 'on'); ?> id="<?php echo $this->get_field_id( 'new_tab' ); ?>" name="<?php echo $this->get_field_name( 'new_tab' ); ?>" value="on" />
				<label for="<?php echo $this->get_field_id( 'new_tab' ); ?>"><?php _e('Open Links in New Tab', 'proteuswidgets' ); ?></label>
			</p>
			<hr>


			<h4><?php _e( 'Social icons:', 'proteuswidgets' ); ?></h4>

			<script type="text/template" id="js-pt-social-icon-<?php echo $this->current_widget_id; ?>">
				<p>
					<label for="<?php echo $this->get_field_id( 'social_icons' ); ?>-{{id}}-link"><?php _e( 'Link:', 'proteuswidgets' ); ?></label>
					<input class="widefat" id="<?php echo $this->get_field_id( 'social_icons' ); ?>-{{id}}-link" name="<?php echo $this->get_field_name( 'social_icons' ); ?>[{{id}}][link]" type="text" value="{{link}}" />
				</p>

				<p>
					<label for="<?php echo $this->get_field_id( 'social_icons' ); ?>-{{id}}-icon"><?php _e( 'Select social network:', 'proteuswidgets' ); ?></label>
					<select name="<?php echo $this->get_field_name( 'social_icons'); ?>[{{id}}][icon]" id="<?php echo $this->get_field_id( 'social_icons' ); ?>-{{id}}-icon" class="js-icon">
						<option value="fa-facebook" <?php selected( '{{icon}}', 'fa-facebook' ); ?>>Facebook</option>
						<option value="fa-twitter" <?php selected( '{{icon}}', 'fa-twitter' ); ?>>Twitter</option>
						<option value="fa-youtube" <?php selected( '{{icon}}', 'fa-youtube' ); ?>>Youtube</option>
						<option value="fa-skype" <?php selected( '{{icon}}', 'fa-skype' ); ?>>Skype</option>
						<option value="fa-google-plus" <?php selected( '{{icon}}', 'fa-google-plus' ); ?>>Google Plus</option>
						<option value="fa-pinterest" <?php selected( '{{icon}}', 'fa-pinterest' ); ?>>Pinterest</option>
						<option value="fa-instagram" <?php selected( '{{icon}}', 'fa-instagram' ); ?>>Instagram</option>
						<option value="fa-vine" <?php selected( '{{icon}}', 'fa-vine' ); ?>>Vine</option>
						<option value="fa-tumblr" <?php selected( '{{icon}}', 'fa-tumblr' ); ?>>Tumblr</option>
						<option value="fa-flickr" <?php selected( '{{icon}}', 'fa-flickr' ); ?>>Flickr</option>
						<option value="fa-vimeo-square" <?php selected( '{{icon}}', 'fa-vimeo-square' ); ?>>Vimeo</option>
						<option value="fa-linkedin" <?php selected( '{{icon}}', 'fa-linkedin' ); ?>>Linkedin</option>
						<option value="fa-dribble" <?php selected( '{{icon}}', 'fa-dribble' ); ?>>Dribble</option>
						<option value="fa-wordpress" <?php selected( '{{icon}}', 'fa-wordpress' ); ?>>Wordpress</option>
						<option value="fa-rss" <?php selected( '{{icon}}', 'fa-rss' ); ?>>RSS</option>
					</select>
				</p>

				<p>
					<input name="<?php echo $this->get_field_name( 'social_icons' ); ?>[{{id}}][id]" type="hidden" value="{{id}}" />
					<a href="#" class="pt-remove-social-icon  js-pt-remove-social-icon"><span class="dashicons dashicons-dismiss"></span> <?php _e( 'Remove social icon', 'proteuswidgets' ); ?></a>
				</p>
			</script>
			<div class="pt-widget-social-icons" id="social-icons-<?php echo $this->current_widget_id; ?>">
				<div class="social-icons"></div>
				<p>
					<a href="#" class="button  js-pt-add-social-icon"><?php _e( 'Add New Social Icon', 'proteuswidgets' ); ?></a>
				</p>
			</div>
			<script type="text/javascript">
				// repopulate the form
				var socialIconsJSON = <?php echo json_encode( $instance['social_icons'] ) ?>;

				// get the right widget id and remove the added < > characters at the start and at the end.
				var widgetId = '<<?php echo $this->current_widget_id; ?>>'.slice( 1, -1 );

				if ( _.isFunction( repopulateSocialIcons ) ) {
					repopulateSocialIcons( socialIconsJSON, widgetId );
				}
			</script>

			<?php
		}

	}
	register_widget( 'PW_Social_Icons' );
}