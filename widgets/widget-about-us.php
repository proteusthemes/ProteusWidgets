<?php
/**
 * About Us Widget
 *
 * @package ProteusWidgets
 * @since 1.0.0
 */


if ( ! class_exists( 'PW_About_Us' ) ) {
	class PW_About_Us extends PW_Widget {

		private $current_widget_id;

		/**
		 * Register widget with WordPress.
		 */
		public function __construct() {

			// Overwrite the widget variables of the parent class
			$this->widget_id_base     = 'about_us';
			$this->widget_name        = esc_html__( 'About Us', 'proteuswidgets' );
			$this->widget_description = esc_html__( 'Displaying person profiles in a carousel.', 'proteuswidgets' );
			$this->widget_class       = 'widget-about-us';

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
			// Prepare data for template
			if ( isset( $instance['people'] ) ) {
				$people = $instance['people'];
			}
			else {
				$people = array(
					array(
						'id'          => 1,
						'tag'         => '',
						'image'       => '',
						'name'        => '',
						'description' => '',
						'link'        => '',
					),
				);
			}

			$people = array_values( $people );

			if ( isset( $people[0] ) ) {
				$people[0]['active'] = 'active';
			}

			$instance['slider_settings'] = 'yes' === $instance['autocycle'] ? esc_attr( empty( $instance['interval'] ) ? 5000 : absint( $instance['interval'] ) ) : 'false';

			$text = array(
				'image-alt'  => esc_html__( 'About us image', 'proteuswidgets' ),
				'read-more'  => esc_html__( 'Read more', 'proteuswidgets' ),
				'previous'   => esc_html__( 'Previous', 'proteuswidgets' ),
				'next'       => esc_html__( 'Next', 'proteuswidgets' ),
			);

			// widget-about-us template rendering
			echo $this->template_engine->render_template( apply_filters( 'pw/widget_about_us_view', 'widget-about-us' ), array(
				'args'     => $args,
				'instance' => $instance,
				'people'   => $people,
				'text'     => $text,
			) );

		}

		/**
		 * Sanitize widget form values as they are saved.
		 *
		 * @param array $new_instance The new options
		 * @param array $old_instance The previous options
		 */
		public function update( $new_instance, $old_instance ) {
			$instance = array();

			$instance['autocycle'] = sanitize_key( $new_instance['autocycle'] );
			$instance['interval']  = absint( $new_instance['interval'] );

			foreach ( $new_instance['people'] as $key => $person ) {
				$instance['people'][ $key ]['id']          = sanitize_key( $person['id'] );
				$instance['people'][ $key ]['tag']         = sanitize_text_field( $person['tag'] );
				$instance['people'][ $key ]['image']       = sanitize_text_field( $person['image'] );
				$instance['people'][ $key ]['name']        = sanitize_text_field( $person['name'] );
				$instance['people'][ $key ]['description'] = sanitize_text_field( $person['description'] );
				$instance['people'][ $key ]['link']        = sanitize_text_field( $person['link'] );
			}

			return $instance;
		}

		/**
		 * Back-end widget form.
		 *
		 * @param array $instance The widget options
		 */
		public function form( $instance ) {

			$autocycle = empty( $instance['autocycle'] ) ? 'no' : $instance['autocycle'];
			$interval  = empty( $instance['interval'] ) ? 5000 : $instance['interval'];

			if ( isset( $instance['people'] ) ) {
				$people = $instance['people'];
			}
			else {
				$people = array(
					array(
						'id'          => 1,
						'tag'         => '',
						'image'       => '',
						'name'        => '',
						'description' => '',
						'link'        => '',
					),
				);
			}

			// Page Builder fix when using repeating fields
			if ( 'temp' === $this->id ) {
				$this->current_widget_id = $this->number;
			}
			else {
				$this->current_widget_id = $this->id;
			}

			?>

			<h4><?php esc_html_e( 'People', 'proteuswidgets' ); ?></h4>

			<script type="text/template" id="js-pt-person-<?php echo esc_attr( $this->current_widget_id ); ?>">
				<p>
					<label for="<?php echo esc_attr( $this->get_field_id( 'people' ) ); ?>-{{id}}-tag"><?php esc_html_e( 'Tag:', 'proteuswidgets' ); ?></label>
					<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'people' ) ); ?>-{{id}}-tag" name="<?php echo esc_attr( $this->get_field_name( 'people' ) ); ?>[{{id}}][tag]" type="text" value="{{tag}}" />
				</p>

				<p>
					<label for="<?php echo esc_attr( $this->get_field_id( 'people' ) ); ?>-{{id}}-image"><?php esc_html_e( 'Picture URL:', 'proteuswidgets' ); ?></label>
					<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'people' ) ); ?>-{{id}}-image" name="<?php echo esc_attr( $this->get_field_name( 'people' ) ); ?>[{{id}}][image]" type="text" value="{{image}}" />
					<input type="button" onclick="ProteusWidgetsUploader.imageUploader.openFileFrame('<?php echo esc_attr( $this->get_field_id( 'people' ) ); ?>-{{id}}-image');" class="upload-brochure-file button button-secondary pull-right" value="Upload file" />
				</p>

				<p>
					<label for="<?php echo esc_attr( $this->get_field_id( 'people' ) ); ?>-{{id}}-name"><?php esc_html_e( 'Name:', 'proteuswidgets' ); ?></label>
					<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'people' ) ); ?>-{{id}}-name" name="<?php echo esc_attr( $this->get_field_name( 'people' ) ); ?>[{{id}}][name]" type="text" value="{{name}}" />
				</p>

				<p>
					<label for="<?php echo esc_attr( $this->get_field_id( 'description' ) ); ?>-{{id}}-title"><?php esc_html_e( 'Description:', 'proteuswidgets' ); ?></label>
					<textarea rows="4" class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'description' ) ); ?>-{{id}}-title" name="<?php echo esc_attr( $this->get_field_name( 'people' ) ); ?>[{{id}}][description]">{{description}}</textarea>
				</p>

				<p>
					<label for="<?php echo esc_attr( $this->get_field_id( 'people' ) ); ?>-{{id}}-link"><?php esc_html_e( 'Link:', 'proteuswidgets' ); ?></label>
					<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'people' ) ); ?>-{{id}}-link" name="<?php echo esc_attr( $this->get_field_name( 'people' ) ); ?>[{{id}}][link]" type="text" value="{{link}}" />
				</p>


				<p>
					<input name="<?php echo esc_attr( $this->get_field_name( 'people' ) ); ?>[{{id}}][id]" type="hidden" value="{{id}}" />
					<a href="#" class="pt-remove-person  js-pt-remove-person"><span class="dashicons dashicons-dismiss"></span> <?php esc_html_e( 'Remove Person', 'proteuswidgets' ); ?></a>
				</p>
			</script>
			<div class="pt-widget-about-us" id="people-<?php echo esc_attr( $this->current_widget_id ); ?>">
				<div class="people"></div>
				<p>
					<a href="#" class="button  js-pt-add-person"><?php esc_html_e( 'Add New Person', 'proteuswidgets' ); ?></a>
				</p>
			</div>
			<script type="text/javascript">
				(function() {
					// repopulate the form
					var peopleJSON = <?php echo wp_json_encode( $people ) ?>;

					// get the right widget id and remove the added < > characters at the start and at the end.
					var widgetId = '<<?php echo esc_js( $this->current_widget_id ); ?>>'.slice( 1, -1 );

					if ( _.isFunction( ProteusWidgets.Utils.repopulatePeople ) ) {
						ProteusWidgets.Utils.repopulatePeople( peopleJSON, widgetId );
					}
				})();
			</script>

			<hr>

			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'autocycle' ) ); ?>"><?php esc_html_e( 'Automatically cycle the carousel:', 'proteuswidgets' ); ?></label>
				<select class="widefat" name="<?php echo esc_attr( $this->get_field_name( 'autocycle' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'autocycle' ) ); ?>">
					<option value="yes"<?php selected( $autocycle, 'yes' ) ?>><?php esc_html_e( 'Yes', 'proteuswidgets' ); ?></option>
					<option value="no"<?php selected( $autocycle, 'no' ) ?>><?php esc_html_e( 'No', 'proteuswidgets' ); ?></option>
				</select>
			</p>

			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'interval' ) ); ?>"><?php esc_html_e( 'Interval (in milliseconds):', 'proteuswidgets' ); ?></label>
				<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'interval' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'interval' ) ); ?>" type="number" min="0" step="500" value="<?php echo esc_attr( $interval ); ?>" />
			</p>

			<?php
		}

	}
}
