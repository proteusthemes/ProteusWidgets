<?php
/**
 * Person Profile Widget
 *
 * @package ProteusWidgets
 * @since 2.3.0
 */


if ( ! class_exists( 'PW_Person_Profile' ) ) {
	class PW_Person_Profile extends PW_Widget {

		private $current_widget_id;
		private $font_awesome_icons_list;

		// Basic widget settings
		function widget_id_base() { return 'person_profile'; }
		function widget_name() { return esc_html__( 'Person Profile', 'proteuswidgets' ); }
		function widget_description() { return esc_html__( 'Widget displaying person\'s profile with a photo.', 'proteuswidgets' ); }
		function widget_class() { return 'widget-person-profile'; }

		/**
		 * Register widget with WordPress.
		 */
		public function __construct() {
			parent::__construct();

			// A list of icons to choose from in the widget backend
			$this->font_awesome_icons_list = apply_filters(
				'pw/social_icons_fa_icons_list',
				array(
					'fa-facebook',
					'fa-twitter',
					'fa-youtube',
					'fa-skype',
					'fa-google-plus',
					'fa-pinterest',
					'fa-instagram',
					'fa-vine',
					'fa-tumblr',
					'fa-foursquare',
					'fa-xing',
					'fa-flickr',
					'fa-vimeo',
					'fa-linkedin',
					'fa-dribble',
					'fa-wordpress',
					'fa-rss',
					'fa-github',
					'fa-bitbucket',
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
			// Prepare data for mustache template
			$instance['new_tab']              = ! empty( $instance['new_tab'] ) ? '_blank' : '_self';
			$instance['image_is_set']         = ! empty( $instance['image'] );
			$instance['image']                = esc_url( $instance['image'] );
			$instance['tag']                  = esc_html( $instance['tag'] );
			$instance['name']                 = esc_html( $instance['name'] );
			$instance['social_icons']         = isset( $instance['social_icons'] ) ? array_values( $instance['social_icons'] ) : array();
			$instance['social_icons_are_set'] = ! empty( $instance['social_icons'] );
			$instance['tag_is_set']           = ! empty( $instance['tag'] );

			$text = array(
				'picture_of' => esc_html__( 'Picture of', 'proteuswidgets' ),
				'meet_me_on' => esc_html__( 'Meet me on:', 'proteuswidgets' ),
			);

			// Mustache widget-person-profile template rendering
			echo $this->mustache->render( apply_filters( 'pw/widget_person_profile_view', 'widget-person-profile' ), array(
				'args'     => $args,
				'instance' => $instance,
				'text'     => $text,
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

			$instance['name']        = sanitize_text_field( $new_instance['name'] );
			$instance['tag']         = sanitize_text_field( $new_instance['tag'] );
			$instance['description'] = wp_kses_post( $new_instance['description'] );
			$instance['image']       = esc_url_raw( $new_instance['image'] );
			$instance['new_tab']     = ! empty ( $new_instance['new_tab'] ) ? sanitize_key( $new_instance['new_tab'] ) : '';

			if ( ! empty( $new_instance['social_icons'] )  ) {
				foreach ( $new_instance['social_icons'] as $key => $social_icon ) {
					$instance['social_icons'][ $key ]['id']   = sanitize_key( $social_icon['id'] );
					$instance['social_icons'][ $key ]['icon'] = sanitize_html_class( $social_icon['icon'] );
					$instance['social_icons'][ $key ]['link'] = esc_url_raw( $social_icon['link'] );
				}
			}

			return $instance;
		}

		/**
		 * Back-end widget form.
		 *
		 * @param array $instance The widget options
		 */
		public function form( $instance ) {

			$name        = empty( $instance['name'] ) ? '' : $instance['name'];
			$tag         = empty( $instance['tag'] ) ? '' : $instance['tag'];
			$image       = empty( $instance['image'] ) ? '' : $instance['image'];
			$description = empty( $instance['description'] ) ? '' : $instance['description'];
			$new_tab     = empty( $instance['new_tab'] ) ? '' : $instance['new_tab'];

			$social_icons = array();
			if ( isset( $instance['social_icons'] ) ) {
				$social_icons = $instance['social_icons'];
			}

			// Page Builder fix when using repeating fields
			if ( 'temp' === $this->id ) {
				$this->current_widget_id = $this->number;
			}
			else {
				$this->current_widget_id = $this->id;
			}

			?>

			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'name' ) ); ?>"><?php _e( 'Name:', 'proteuswidgets' ); ?></label>
				<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'name' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'name' ) ); ?>" type="text" value="<?php echo esc_attr( $name ); ?>" />
			</p>

			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'tag' ) ); ?>"><?php _e( 'Title:', 'proteuswidgets' ); ?></label>
				<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'tag' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'tag' ) ); ?>" type="text" value="<?php echo esc_attr( $tag ); ?>" />
			</p>

			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'image' ) ); ?>"><?php _e( 'Picture URL:', 'proteuswidgets' ); ?></label>
				<input class="widefat" style="margin-bottom: 6px;" id="<?php echo esc_attr( $this->get_field_id( 'image' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'image' ) ); ?>" type="text" value="<?php echo esc_attr( $image ); ?>" />
				<input type="button" onclick="ProteusWidgetsUploader.imageUploader.openFileFrame('<?php echo esc_attr( $this->get_field_id( 'image' ) ); ?>');" class="button button-secondary" value="Upload Image" />
			</p>

			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'description' ) ); ?>"><?php _e( 'Introduction:', 'proteuswidgets' ); ?></label>
				<textarea rows="4" class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'description' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'description' ) ); ?>"><?php echo esc_attr( $description ); ?></textarea>
			</p>

			<p>
				<input class="checkbox" type="checkbox" <?php checked( $new_tab, 'on' ); ?> id="<?php echo esc_attr( $this->get_field_id( 'new_tab' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'new_tab' ) ); ?>" value="on" />
				<label for="<?php echo esc_attr( $this->get_field_id( 'new_tab' ) ); ?>"><?php _e( 'Open link in new tab', 'proteuswidgets' ); ?></label>
			</p>

			<hr>

			<script type="text/template" id="js-pt-social-icon-<?php echo esc_attr( $this->current_widget_id ); ?>">
				<p>
					<label for="<?php echo esc_attr( $this->get_field_id( 'social_icons' ) ); ?>-{{id}}-link"><?php _e( 'Link:', 'proteuswidgets' ); ?></label>
					<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'social_icons' ) ); ?>-{{id}}-link" name="<?php echo esc_attr( $this->get_field_name( 'social_icons' ) ); ?>[{{id}}][link]" type="text" value="{{link}}" />
				</p>

				<p>
					<label for="<?php echo esc_attr( $this->get_field_id( 'social_icons' ) ); ?>-{{id}}-icon"><?php _e( 'Select social icon:', 'proteuswidgets' ); ?></label> <br />
					<small><?php printf( esc_html__( 'Click on the icon below or manually select from the %s website.', 'proteuswidgets' ), '<a href="http://fontawesome.io/icons/" target="_blank">FontAwesome</a>' ); ?></small>
					<input id="<?php echo esc_attr( $this->get_field_id( 'social_icons' ) ); ?>-{{id}}-icon" name="<?php echo esc_attr( $this->get_field_name( 'social_icons' ) ); ?>[{{id}}][icon]" type="text" value="{{icon}}" class="widefat  js-icon-input" /> <br><br>
					<?php foreach ( $this->font_awesome_icons_list as $icon ) : ?>
						<a class="js-selectable-icon  icon-widget" href="#" data-iconname="<?php echo esc_attr( $icon ); ?>"><i class="fa fa-lg <?php echo esc_attr( $icon ); ?>"></i></a>
					<?php endforeach; ?>
				</p>

				<p>
					<input name="<?php echo esc_attr( $this->get_field_name( 'social_icons' ) ); ?>[{{id}}][id]" type="hidden" value="{{id}}" />
					<a href="#" class="pt-remove-social-icon  js-pt-remove-social-icon"><span class="dashicons dashicons-dismiss"></span> <?php _e( 'Remove Social Icon', 'proteuswidgets' ); ?></a>
				</p>
			</script>
			<div class="pt-widget-social-icons" id="social-icons-<?php echo esc_attr( $this->current_widget_id ); ?>">
				<div class="social-icons"></div>
				<p>
					<a href="#" class="button  js-pt-add-social-icon"><?php _e( 'Add New Social Icon', 'proteuswidgets' ); ?></a>
				</p>
			</div>
			<script type="text/javascript">
				(function() {
					// repopulate the form
					var socialIconsJSON = <?php echo wp_json_encode( $social_icons ) ?>;

					// get the right widget id and remove the added < > characters at the start and at the end.
					var widgetId = '<<?php echo esc_js( $this->current_widget_id ); ?>>'.slice( 1, -1 );

					if ( _.isFunction( ProteusWidgets.Utils.repopulateSocialIcons ) ) {
						ProteusWidgets.Utils.repopulateSocialIcons( socialIconsJSON, widgetId );
					}
				})();
			</script>

			<?php
		}
	}
}