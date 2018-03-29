<?php
/**
 * Person Profile Widget
 *
 * @package ProteusWidgets
 * @since 2.3.0
 */


if ( ! class_exists( 'PW_Person_Profile' ) ) {
	class PW_Person_Profile extends PW_Widget {

		private $fields, $current_widget_id, $font_awesome_icons_list, $icon_list_fa_icons;

		/**
		 * Register widget with WordPress.
		 */
		public function __construct() {

			// Overwrite the widget variables of the parent class.
			$this->widget_id_base     = 'person_profile';
			$this->widget_name        = esc_html__( 'Person Profile', 'proteuswidgets' );
			$this->widget_description = esc_html__( 'Widget displaying person\'s profile with a photo.', 'proteuswidgets' );
			$this->widget_class       = 'widget-person-profile';

			parent::__construct();

			// Get the settings for the this widget.
			$this->fields = apply_filters( 'pw/person_profile_widget_settings', array(
				'label_instead_of_tag'      => false,
				'carousel_instead_of_image' => false,
				'skills'                    => false,
				'tags'                      => false,
				'description'               => true,
				'social_icons'              => true,
				'location'                  => false,
				'specific_location'         => false,
				'cta'                       => false,
				'icon_list_items'           => false,
				'carousel_custom_links'     => false,
				'name_link_url'             => false,
			) );

			// A list of icons to choose from in the widget backend.
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

			$this->icon_list_fa_icons = apply_filters(
				'pw/icon_list_widget_fa_icons_list',
				array(
					'fa-home',
					'fa-phone',
					'fa-inbox',
					'fa-angle-right',
					'fa-question-circle',
					'fa-envelope-o',
					'fa-envelope',
					'fa-map-marker',
					'fa-users',
					'fa-female',
					'fa-male',
					'fa-compass',
					'fa-laptop',
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
			// Prepare data for template.
			if ( $this->fields['social_icons'] ) {
				$instance['social_icons'] = isset( $instance['social_icons'] ) ? array_values( $instance['social_icons'] ) : array();
			}
			if ( $this->fields['skills'] ) {
				$instance['skills'] = ! empty( $instance['skills'] ) ? array_values( $instance['skills'] ) : array();
			}
			if ( $this->fields['carousel_instead_of_image'] ) {
				$instance['carousel'] = ! empty( $instance['carousel'] ) ? array_values( $instance['carousel'] ) : array();
			}
			if ( $this->fields['tags'] ) {
				$instance['tags'] = ! empty( $instance['tags'] ) ? explode( ',', $instance['tags'] ) : array();
			}
			if ( $this->fields['icon_list_items'] ) {
				$instance['icon_list_items'] = ! empty( $instance['icon_list_items'] ) ? array_values( $instance['icon_list_items'] ) : array();
			}

			$text = array(
				'picture_of' => esc_html__( 'Picture of', 'proteuswidgets' ),
				'meet_me_on' => esc_html__( 'Meet me on:', 'proteuswidgets' ),
			);

			// The widget-person-profile template rendering.
			echo $this->template_engine->render_template( apply_filters( 'pw/widget_person_profile_view', 'widget-person-profile' ), array(
				'args'     => $args,
				'instance' => $instance,
				'text'     => $text,
			));
		}

		/**
		 * Sanitize widget form values as they are saved.
		 *
		 * @param array $new_instance The new options.
		 * @param array $old_instance The previous options.
		 */
		public function update( $new_instance, $old_instance ) {
			$instance = array();

			$instance['name']    = sanitize_text_field( $new_instance['name'] );
			$instance['new_tab'] = ! empty( $new_instance['new_tab'] ) ? sanitize_key( $new_instance['new_tab'] ) : '';

			if ( $this->fields['name_link_url'] ) {
				$instance['name_link'] = esc_url_raw( $new_instance['name_link'] );
			}

			if ( $this->fields['description'] ) {
				$instance['description'] = wp_kses_post( $new_instance['description'] );
			}

			if ( $this->fields['location'] ) {
				$instance['location'] = sanitize_text_field( $new_instance['location'] );
			}

			if ( $this->fields['specific_location'] ) {
				$instance['specific_location'] = sanitize_text_field( $new_instance['specific_location'] );
			}

			if ( $this->fields['cta'] ) {
				$instance['cta_text']    = sanitize_text_field( $new_instance['cta_text'] );
				$instance['cta_link']    = sanitize_text_field( $new_instance['cta_link'] );
				$instance['cta_new_tab'] = ! empty( $new_instance['cta_new_tab'] ) ? sanitize_key( $new_instance['cta_new_tab'] ) : '';
			}

			if ( $this->fields['label_instead_of_tag'] ) {
				$instance['label'] = sanitize_text_field( $new_instance['label'] );
			}
			else {
				$instance['tag'] = sanitize_text_field( $new_instance['tag'] );
			}

			if ( $this->fields['tags'] ) {
				$instance['tags'] = sanitize_text_field( $new_instance['tags'] );
			}

			if ( $this->fields['carousel_instead_of_image'] ) {
				if ( ! empty( $new_instance['carousel'] ) ) {
					foreach ( $new_instance['carousel'] as $key => $carousel_item ) {
						$instance['carousel'][ $key ]['id']   = sanitize_key( $carousel_item['id'] );
						$instance['carousel'][ $key ]['type'] = sanitize_text_field( $carousel_item['type'] );
						$instance['carousel'][ $key ]['url']  = esc_url_raw( $carousel_item['url'] );

						if ( true === $this->fields['carousel_custom_links'] ) {
							$instance['carousel'][ $key ]['link'] = esc_url_raw( $carousel_item['link'] );
						}
					}
				}
			}
			else {
				$instance['image'] = esc_url_raw( $new_instance['image'] );
			}

			if ( $this->fields['social_icons'] && ! empty( $new_instance['social_icons'] )  ) {
				foreach ( $new_instance['social_icons'] as $key => $social_icon ) {
					$instance['social_icons'][ $key ]['id']   = sanitize_key( $social_icon['id'] );
					$instance['social_icons'][ $key ]['icon'] = sanitize_text_field( $social_icon['icon'] );
					$instance['social_icons'][ $key ]['link'] = esc_url_raw( $social_icon['link'] );
				}
			}

			if ( $this->fields['skills'] && ! empty( $new_instance['skills'] ) ) {
				foreach ( $new_instance['skills'] as $key => $skill ) {
					$instance['skills'][ $key ]['id']     = sanitize_key( $skill['id'] );
					$instance['skills'][ $key ]['name']   = sanitize_text_field( $skill['name'] );
					$instance['skills'][ $key ]['rating'] = sanitize_text_field( $skill['rating'] );
				}
			}

			if ( $this->fields['icon_list_items'] && ! empty( $new_instance['icon_list_items'] ) ) {
				foreach ( $new_instance['icon_list_items'] as $key => $item ) {
					$instance['icon_list_items'][ $key ]['id']          = sanitize_key( $item['id'] );
					$instance['icon_list_items'][ $key ]['text']        = sanitize_text_field( $item['text'] );
					$instance['icon_list_items'][ $key ]['icon']        = sanitize_text_field( $item['icon'] );
					$instance['icon_list_items'][ $key ]['link']        = esc_url_raw( $item['link'] );
					$instance['icon_list_items'][ $key ]['is-featured'] = ! empty( $item['is-featured'] ) ? sanitize_key( $item['is-featured'] ) : '';
					$instance['icon_list_items'][ $key ]['description'] = wp_kses_post( $item['description'] );
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
			$image       = empty( $instance['image'] ) ? '' : $instance['image'];
			$new_tab     = empty( $instance['new_tab'] ) ? '' : $instance['new_tab'];

			if ( $this->fields['name_link_url'] ) {
				$name_link = empty( $instance['name_link'] ) ? '' : $instance['name_link'];
			}

			if ( $this->fields['description'] ) {
				$description = empty( $instance['description'] ) ? '' : $instance['description'];
			}

			if ( $this->fields['location'] ) {
				$location = empty( $instance['location'] ) ? '' : $instance['location'];
			}

			if ( $this->fields['specific_location'] ) {
				$specific_location = empty( $instance['specific_location'] ) ? '' : $instance['specific_location'];
			}

			if ( $this->fields['cta'] ) {
				$cta_text    = empty( $instance['cta_text'] ) ? '' : $instance['cta_text'];
				$cta_link    = empty( $instance['cta_link'] ) ? '' : $instance['cta_link'];
				$cta_new_tab = empty( $instance['cta_new_tab'] ) ? '' : $instance['cta_new_tab'];
			}

			if ( $this->fields['label_instead_of_tag'] ) {
				$label = empty( $instance['label'] ) ? '' : $instance['label'];
			}
			else {
				$tag = empty( $instance['tag'] ) ? '' : $instance['tag'];
			}

			if ( $this->fields['tags'] ) {
				$tags = empty( $instance['tags'] ) ? '' : $instance['tags'];
			}

			if ( $this->fields['social_icons'] ) {
				$social_icons = array();
				if ( isset( $instance['social_icons'] ) ) {
					$social_icons = $instance['social_icons'];
				}
			}

			if ( $this->fields['skills'] ) {
				$skills = isset( $instance['skills'] ) ? $instance['skills'] : array();
			}

			if ( $this->fields['icon_list_items'] ) {
				$icon_list_items = isset( $instance['icon_list_items'] ) ? $instance['icon_list_items'] : array();
			}

			if ( $this->fields['carousel_instead_of_image'] ) {
				$carousel = isset( $instance['carousel'] ) ? $instance['carousel'] : array();
			}

			// Page Builder fix when using repeating fields.
			if ( 'temp' === $this->id ) {
				$this->current_widget_id = $this->number;
			}
			else {
				$this->current_widget_id = $this->id;
			}

			?>

			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'name' ) ); ?>"><?php esc_html_e( 'Name:', 'proteuswidgets' ); ?></label>
				<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'name' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'name' ) ); ?>" type="text" value="<?php echo esc_attr( $name ); ?>" />
			</p>

			<?php if ( $this->fields['name_link_url'] ) : ?>
				<p>
					<label for="<?php echo esc_attr( $this->get_field_id( 'name_link' ) ); ?>"><?php esc_html_e( 'Link (URL):', 'proteuswidgets' ); ?></label>
					<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'name_link' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'name_link' ) ); ?>" type="text" value="<?php echo esc_attr( $name_link ); ?>" />
				</p>
			<?php endif; ?>

			<?php if ( $this->fields['label_instead_of_tag'] ) : ?>
				<p>
					<label for="<?php echo esc_attr( $this->get_field_id( 'label' ) ); ?>"><?php esc_html_e( 'Label:', 'proteuswidgets' ); ?></label>
					<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'label' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'label' ) ); ?>" type="text" value="<?php echo esc_attr( $label ); ?>" />
				</p>
			<?php else : ?>
				<p>
					<label for="<?php echo esc_attr( $this->get_field_id( 'tag' ) ); ?>"><?php esc_html_e( 'Title:', 'proteuswidgets' ); ?></label>
					<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'tag' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'tag' ) ); ?>" type="text" value="<?php echo esc_attr( $tag ); ?>" />
				</p>
			<?php endif; ?>

			<?php if ( ! $this->fields['carousel_instead_of_image'] ) : ?>
				<p>
					<label for="<?php echo esc_attr( $this->get_field_id( 'image' ) ); ?>"><?php esc_html_e( 'Picture URL:', 'proteuswidgets' ); ?></label>
					<input class="widefat" style="margin-bottom: 6px;" id="<?php echo esc_attr( $this->get_field_id( 'image' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'image' ) ); ?>" type="text" value="<?php echo esc_attr( $image ); ?>" />
					<input type="button" onclick="ProteusWidgetsUploader.imageUploader.openFileFrame('<?php echo esc_attr( $this->get_field_id( 'image' ) ); ?>');" class="button button-secondary" value="Upload Image" />
				</p>
			<?php endif; ?>

			<?php if ( $this->fields['location'] ) : ?>
				<p>
					<label for="<?php echo esc_attr( $this->get_field_id( 'location' ) ); ?>"><?php esc_html_e( 'Location:', 'proteuswidgets' ); ?></label>
					<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'location' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'location' ) ); ?>" type="text" value="<?php echo esc_attr( $location ); ?>" />
				</p>
			<?php endif; ?>

			<?php if ( $this->fields['specific_location'] ) : ?>
				<p>
					<label for="<?php echo esc_attr( $this->get_field_id( 'specific_location' ) ); ?>"><?php esc_html_e( 'Specific location:', 'proteuswidgets' ); ?></label>
					<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'specific_location' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'specific_location' ) ); ?>" type="text" value="<?php echo esc_attr( $specific_location ); ?>" />
				</p>
			<?php endif; ?>

			<?php if ( $this->fields['cta'] ) : ?>
				<div style="padding: 0 10px; border: 1px solid rgba(0,0,0,0.1); background-color: rgba(0,0,0,0.03); margin-bottom: 10px;">
					<h4 style="margin-bottom: 0;"><?php esc_html_e( 'Button/CTA', 'proteuswidgets' ); ?></h4>
					<small><?php esc_html_e( 'To disable the CTA button, just leave the button text field empty.', 'proteuswidgets' ); ?></small>

					<p>
						<label for="<?php echo esc_attr( $this->get_field_id( 'cta_text' ) ); ?>"><?php esc_html_e( 'Button text:', 'proteuswidgets' ); ?></label>
						<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'cta_text' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'cta_text' ) ); ?>" type="text" value="<?php echo esc_attr( $cta_text ); ?>" />
					</p>

					<p>
						<label for="<?php echo esc_attr( $this->get_field_id( 'cta_link' ) ); ?>"><?php esc_html_e( 'Button URL:', 'proteuswidgets' ); ?></label>
						<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'cta_link' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'cta_link' ) ); ?>" type="text" value="<?php echo esc_attr( $cta_link ); ?>" />
					</p>

					<p>
						<input class="checkbox" type="checkbox" <?php checked( $cta_new_tab, 'on' ); ?> id="<?php echo esc_attr( $this->get_field_id( 'cta_new_tab' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'cta_new_tab' ) ); ?>" value="on" />
						<label for="<?php echo esc_attr( $this->get_field_id( 'cta_new_tab' ) ); ?>"><?php esc_html_e( 'Open button link in new tab!', 'proteuswidgets' ); ?></label>
					</p>
				</div>
			<?php endif; ?>

			<?php if ( $this->fields['description'] ) : ?>
				<p>
					<label for="<?php echo esc_attr( $this->get_field_id( 'description' ) ); ?>"><?php esc_html_e( 'Introduction:', 'proteuswidgets' ); ?></label>
					<textarea rows="4" class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'description' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'description' ) ); ?>"><?php echo esc_attr( $description ); ?></textarea>
				</p>
			<?php endif; ?>

			<?php if ( $this->fields['tags'] ) : ?>
				<p>
					<label for="<?php echo esc_attr( $this->get_field_id( 'tags' ) ); ?>"><?php esc_html_e( 'Tags:', 'proteuswidgets' ); ?></label>
					<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'tags' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'tags' ) ); ?>" type="text" value="<?php echo esc_html( $tags ); ?>" />
					<br>
					<small><?php esc_html_e( 'Separate the tags with a comma. Example: first tag, second tag, third tag', 'proteuswidgets' ); ?></small>
				</p>
			<?php endif; ?>

			<?php if ( $this->fields['social_icons'] ) : ?>
				<p>
					<input class="checkbox" type="checkbox" <?php checked( $new_tab, 'on' ); ?> id="<?php echo esc_attr( $this->get_field_id( 'new_tab' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'new_tab' ) ); ?>" value="on" />
					<label for="<?php echo esc_attr( $this->get_field_id( 'new_tab' ) ); ?>"><?php esc_html_e( 'Open links in new tab', 'proteuswidgets' ); ?></label>
				</p>
			<?php endif; ?>

			<?php if ( $this->fields['icon_list_items'] ) : ?>
				<hr>

				<h3 style="margin-top: 50px;"><?php esc_html_e( 'Icon list items:', 'proteuswidgets' ); ?></h3>

				<script type="text/template" id="js-pt-icon-list-item-<?php echo esc_attr( $this->current_widget_id ); ?>">
					<p>
						<label for="<?php echo esc_attr( $this->get_field_id( 'icon_list_items' ) ); ?>-{{id}}-text"><?php esc_html_e( 'Text:', 'proteuswidgets' ); ?></label>
						<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'icon_list_items' ) ); ?>-{{id}}-text" name="<?php echo esc_attr( $this->get_field_name( 'icon_list_items' ) ); ?>[{{id}}][text]" type="text" value="{{text}}" />
					</p>

					<p>
						<label for="<?php echo esc_attr( $this->get_field_id( 'icon_list_items' ) ); ?>-{{id}}-icon"><?php esc_html_e( 'Icon:', 'proteuswidgets' ); ?></label> <br />
						<small><?php echo wp_kses_post( apply_filters( 'pw/icons_input_field_notice', sprintf( esc_html__( 'Click on the icon below or manually select from the %s website.', 'proteuswidgets' ), '<a href="https://fontawesome.com/v4.7.0/icons/" target="_blank">FontAwesome</a>' ) ) ); ?></small>
						<input id="<?php echo esc_attr( $this->get_field_id( 'icon_list_items' ) ); ?>-{{id}}-icon" name="<?php echo esc_attr( $this->get_field_name( 'icon_list_items' ) ); ?>[{{id}}][icon]" type="text" value="{{icon}}" class="widefat  js-icon-input" /> <br><br>
						<?php foreach ( $this->icon_list_fa_icons as $icon ) : ?>
							<a class="js-selectable-icon  icon-widget" href="#" data-iconname="<?php echo esc_attr( $icon ); ?>"><i class="fa fa-lg <?php echo esc_html( $icon ) ?>"></i></a>
						<?php endforeach; ?>
					</p>

					<p>
						<label for="<?php echo esc_attr( $this->get_field_id( 'icon_list_items' ) ); ?>-{{id}}-link"><?php esc_html_e( 'URL:', 'proteuswidgets' ); ?></label>
						<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'icon_list_items' ) ); ?>-{{id}}-link" name="<?php echo esc_attr( $this->get_field_name( 'icon_list_items' ) ); ?>[{{id}}][link]" type="link" value="{{link}}" />
					</p>

					<p>
						<input class="checkbox  js-is-featured-checkbox" type="checkbox" id="<?php echo esc_attr( $this->get_field_id( 'icon_list_items' ) ); ?>-{{id}}-is-featured" name="<?php echo esc_attr( $this->get_field_name( 'icon_list_items' ) ); ?>[{{id}}][is-featured]" value="on"/>
						<label for="<?php echo esc_attr( $this->get_field_id( 'icon_list_items' ) ); ?>-{{id}}-is-featured"><?php esc_html_e( 'Set this item as featured!', 'proteuswidgets' ); ?></label>
						<br>
						<small><?php esc_html_e( 'It will make this item bigger and display a description as well.', 'proteuswidgets' ); ?></small>
					</p>

					<p class="js-icon-list-description-container">
						<label for="<?php echo esc_attr( $this->get_field_id( 'icon_list_items' ) ); ?>-{{id}}-description"><?php esc_html_e( 'Description:', 'proteuswidgets' ); ?></label>
						<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'icon_list_items' ) ); ?>-{{id}}-description" name="<?php echo esc_attr( $this->get_field_name( 'icon_list_items' ) ); ?>[{{id}}][description]" type="description" value="{{description}}" />
					</p>

					<p>
						<input name="<?php echo esc_attr( $this->get_field_name( 'icon_list_items' ) ); ?>[{{id}}][id]" class="js-pt-icon-list-id" type="hidden" value="{{id}}" />
						<a href="#" class="pt-remove-icon-list-item  js-pt-remove-icon-list-item"><span class="dashicons dashicons-dismiss"></span> <?php esc_html_e( 'Remove item', 'proteuswidgets' ); ?></a>
					</p>
				</script>

				<div class="pt-widget-icon-list-items" id="icon-list-items-<?php echo esc_attr( $this->current_widget_id ); ?>">
					<div class="icon-list-items  js-pt-sortable-icon-list-items"></div>
					<p>
						<a href="#" class="button  js-pt-add-icon-list-item"><?php esc_html_e( 'Add new item', 'proteuswidgets' ); ?></a>
					</p>
				</div>
			<?php endif; ?>

			<?php if ( $this->fields['carousel_instead_of_image'] ) : ?>

				<hr>

				<h3 style="margin-top: 50px;"><?php esc_html_e( 'Carousel items:', 'proteuswidgets' ); ?></h3>

				<p>
					<?php esc_html_e( 'Note: in order to have a smooth transition between your images, please use images with equal size. If you also have a video in the carousel, then use images with 16:9 ratio.', 'proteuswidgets' ); ?>
				</p>

				<script type="text/template" id="js-pt-carousel-<?php echo esc_attr( $this->current_widget_id ); ?>">
					<p>
						<label for="<?php echo esc_attr( $this->get_field_id( 'carousel' ) ); ?>-{{id}}-type"><?php esc_html_e( 'Media type:', 'proteuswidgets' ); ?></label>
						<select name="<?php echo esc_attr( $this->get_field_name( 'carousel' ) ); ?>[{{id}}][type]" id="<?php echo esc_attr( $this->get_field_id( 'carousel' ) ); ?>-{{id}}-type" class="js-media-type">
							<option value="image"><?php esc_html_e( 'Image', 'proteuswidgets' ); ?></option>
							<option value="video"><?php esc_html_e( 'Video', 'proteuswidgets' ); ?></option>
	 					</select>
					</p>

					<p>
						<label for="<?php echo esc_attr( $this->get_field_id( 'carousel' ) ); ?>-{{id}}-url"><?php esc_html_e( 'URL:', 'proteuswidgets' ); ?></label>
						<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'carousel' ) ); ?>-{{id}}-url" name="<?php echo esc_attr( $this->get_field_name( 'carousel' ) ); ?>[{{id}}][url]" type="text" value="{{url}}" />
						<input type="button" onclick="ProteusWidgetsUploader.imageUploader.openFileFrame('<?php echo esc_attr( $this->get_field_id( 'carousel' ) ); ?>-{{id}}-url');" class="button button-secondary" value="Upload Image" />
					</p>

					<?php if ( true === $this->fields['carousel_custom_links'] ) : ?>
						<p>
							<label for="<?php echo esc_attr( $this->get_field_id( 'carousel' ) ); ?>-{{id}}-link"><?php esc_html_e( 'Link:', 'proteuswidgets' ); ?></label>
							<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'carousel' ) ); ?>-{{id}}-link" name="<?php echo esc_attr( $this->get_field_name( 'carousel' ) ); ?>[{{id}}][link]" type="text" value="{{link}}" placeholder="<?php esc_attr_e( 'https://www.proteusthemes.com', 'proteuswidgets' ); ?>" />
							<small><?php esc_html_e( 'Optional. Works only with Image media type. If set, the image will link to the specified URL.', 'proteuswidgets' ) ?></small>
						</p>
					<?php endif; ?>

					<p>
						<input name="<?php echo esc_attr( $this->get_field_name( 'carousel' ) ); ?>[{{id}}][id]" type="hidden" value="{{id}}" />
						<a href="#" class="pt-remove-carousel-item  js-pt-remove-carousel-item"><span class="dashicons dashicons-dismiss"></span> <?php esc_html_e( 'Remove Carousel Item', 'proteuswidgets' ); ?></a>
					</p>
				</script>
				<div class="pt-widget-carousel-items" id="carousel-items-<?php echo esc_attr( $this->current_widget_id ); ?>">
					<div class="carousel-items"></div>
					<p>
						<a href="#" class="button  js-pt-add-carousel-item"><?php esc_html_e( 'Add New Carousel Item', 'proteuswidgets' ); ?></a>
					</p>
				</div>

			<?php endif; ?>

			<?php if ( $this->fields['skills'] ) : ?>

				<hr>

				<h3 style="margin-top: 50px;"><?php esc_html_e( 'Skills:', 'proteuswidgets' ); ?></h3>

				<script type="text/template" id="js-pt-skills-<?php echo esc_attr( $this->current_widget_id ); ?>">
					<p>
						<label for="<?php echo esc_attr( $this->get_field_id( 'skills' ) ); ?>-{{id}}-name"><?php esc_html_e( 'Name:', 'proteuswidgets' ); ?></label>
						<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'skills' ) ); ?>-{{id}}-name" name="<?php echo esc_attr( $this->get_field_name( 'skills' ) ); ?>[{{id}}][name]" type="text" value="{{name}}" />
					</p>

					<p>
						<label for="<?php echo esc_attr( $this->get_field_id( 'skills' ) ); ?>-{{id}}-rating"><?php esc_html_e( 'Rating:', 'proteuswidgets' ); ?></label>
						<select name="<?php echo esc_attr( $this->get_field_name( 'skills' ) ); ?>[{{id}}][rating]" id="<?php echo esc_attr( $this->get_field_id( 'skills' ) ); ?>-{{id}}-rating" class="js-rating">
							<option value="nothing"><?php esc_html_e( 'Don\'t show', 'proteuswidgets' ); ?></option>
							<option value="0">0</option>
							<option value="1">1</option>
							<option value="2">2</option>
							<option value="3">3</option>
							<option value="4">4</option>
							<option value="5">5</option>
	 					</select>
					</p>

					<p>
						<input name="<?php echo esc_attr( $this->get_field_name( 'skills' ) ); ?>[{{id}}][id]" type="hidden" value="{{id}}" />
						<a href="#" class="pt-remove-skill  js-pt-remove-skill"><span class="dashicons dashicons-dismiss"></span> <?php esc_html_e( 'Remove Skill', 'proteuswidgets' ); ?></a>
					</p>
				</script>
				<div class="pt-widget-skills" id="skills-<?php echo esc_attr( $this->current_widget_id ); ?>">
					<div class="skills"></div>
					<p>
						<a href="#" class="button  js-pt-add-skill"><?php esc_html_e( 'Add New Skill', 'proteuswidgets' ); ?></a>
					</p>
				</div>

			<?php endif; ?>


			<?php if ( $this->fields['social_icons'] ) : ?>

				<hr>

				<h3 style="margin-top: 50px;"><?php esc_html_e( 'Social icons:', 'proteuswidgets' ); ?></h3>

				<script type="text/template" id="js-pt-social-icon-<?php echo esc_attr( $this->current_widget_id ); ?>">
					<p>
						<label for="<?php echo esc_attr( $this->get_field_id( 'social_icons' ) ); ?>-{{id}}-link"><?php esc_html_e( 'Link:', 'proteuswidgets' ); ?></label>
						<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'social_icons' ) ); ?>-{{id}}-link" name="<?php echo esc_attr( $this->get_field_name( 'social_icons' ) ); ?>[{{id}}][link]" type="text" value="{{link}}" />
					</p>

					<p>
						<label for="<?php echo esc_attr( $this->get_field_id( 'social_icons' ) ); ?>-{{id}}-icon"><?php esc_html_e( 'Select social icon:', 'proteuswidgets' ); ?></label> <br />
						<small><?php echo wp_kses_post( apply_filters( 'pw/icons_input_field_notice', sprintf( esc_html__( 'Click on the icon below or manually select from the %s website.', 'proteuswidgets' ), '<a href="https://fontawesome.com/v4.7.0/icons/" target="_blank">FontAwesome</a>' ) ) ); ?></small>
						<input id="<?php echo esc_attr( $this->get_field_id( 'social_icons' ) ); ?>-{{id}}-icon" name="<?php echo esc_attr( $this->get_field_name( 'social_icons' ) ); ?>[{{id}}][icon]" type="text" value="{{icon}}" class="widefat  js-icon-input" /> <br><br>
						<?php foreach ( $this->font_awesome_icons_list as $icon ) : ?>
							<a class="js-selectable-icon  icon-widget" href="#" data-iconname="<?php echo esc_attr( $icon ); ?>"><i class="fa fa-lg <?php echo esc_attr( $icon ); ?>"></i></a>
						<?php endforeach; ?>
					</p>

					<p>
						<input name="<?php echo esc_attr( $this->get_field_name( 'social_icons' ) ); ?>[{{id}}][id]" type="hidden" value="{{id}}" />
						<a href="#" class="pt-remove-social-icon  js-pt-remove-social-icon"><span class="dashicons dashicons-dismiss"></span> <?php esc_html_e( 'Remove Social Icon', 'proteuswidgets' ); ?></a>
					</p>
				</script>
				<div class="pt-widget-social-icons" id="social-icons-<?php echo esc_attr( $this->current_widget_id ); ?>">
					<div class="social-icons"></div>
					<p>
						<a href="#" class="button  js-pt-add-social-icon"><?php esc_html_e( 'Add New Social Icon', 'proteuswidgets' ); ?></a>
					</p>
				</div>

			<?php endif; ?>


			<script type="text/javascript">
				(function( $ ) {
					// get the right widget id and remove the added < > characters at the start and at the end.
					var widgetId = '<<?php echo esc_js( $this->current_widget_id ); ?>>'.slice( 1, -1 );

					// repopulate the form
					<?php if ( $this->fields['social_icons'] ) : ?>
						var socialIconsJSON = <?php echo wp_json_encode( $social_icons ); ?>;

						if ( _.isFunction( ProteusWidgets.Utils.repopulateSocialIcons ) ) {
							ProteusWidgets.Utils.repopulateSocialIcons( socialIconsJSON, widgetId );
						}
					<?php endif; ?>

					<?php if ( $this->fields['icon_list_items'] ) : ?>
						var iconListItemsJSON = <?php echo wp_json_encode( $icon_list_items ) ?>;

						if ( _.isFunction( ProteusWidgets.Utils.repopulateIconListItems ) ) {
							ProteusWidgets.Utils.repopulateIconListItems( iconListItemsJSON, widgetId );
						}

						// Hide/display description.
						$( document ).on( 'change', '.js-is-featured-checkbox', function() {
							$( this )
								.parent()
								.siblings( '.js-icon-list-description-container' )
									.toggle( $( this ).prop( 'checked' ) );
						});
					<?php endif; ?>

					<?php if ( $this->fields['carousel_instead_of_image'] ) : ?>
						var carouselJSON = <?php echo wp_json_encode( $carousel ); ?>;

						if ( _.isFunction( ProteusWidgets.Utils.repopulateCarousel ) ) {
							ProteusWidgets.Utils.repopulateCarousel( carouselJSON, widgetId );
						}
					<?php endif; ?>

					<?php if ( $this->fields['skills'] ) : ?>
						var skillsJSON = <?php echo wp_json_encode( $skills ); ?>;

						if ( _.isFunction( ProteusWidgets.Utils.repopulateSkills ) ) {
							ProteusWidgets.Utils.repopulateSkills( skillsJSON, widgetId );
						}
					<?php endif; ?>
				})( jQuery );
			</script>

			<?php
		}
	}
}
