<?php
/**
 * Testimonials Widget
 *
 * @package ProteusWidgets
 * @since 1.0.0
 */

if ( ! class_exists( 'PW_About_Us' ) ) {
	class PW_About_Us extends PW_Widget {

		private $current_widget_id;

		// Basic widget settings
		function widget_name() { return __( 'About Us', 'proteuswidgets' ); }
		function widget_description() { return __( 'About Us widget used in sidebar.', 'proteuswidgets' ); }
		function widget_class() { return 'widget-about-us'; }

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
			$autocycle = empty( $instance['autocycle'] ) ? false : 'yes' === $instance['autocycle'];
			$interval  = empty( $instance['interval'] ) ? 5000 : absint( $instance['interval'] );

			if ( isset( $instance['persons'] ) ) {
				$persons = $instance['persons'];
			}
			else {
				$persons = array(
					array(
						'id'          => 1,
						'tag'         => '',
						'image'       => '',
						'name'        => '',
						'description' => '',
						'link'        => '',
					)
				);
			}

			// Mustache widget-about-us template rendering
			$persons = PWFunctions::reorder_widget_array_key_values( $persons );
			if ( isset( $persons[0] ) ) {
				$persons[0]['active'] = 'active';
			}
			echo $this->mustache->render( apply_filters( 'pw/widget_about_us_view', 'widget-about-us' ), array(
				'before-widget'     => $args['before_widget'],
				'after-widget'      => $args['after_widget'],
				'persons'           => $persons,
				'widget-id'         => esc_attr( $args['widget_id'] ),
				'data-interval'     => $autocycle ? 'data-interval=' . esc_attr( $interval ) : 'data-interval=false',
				'navigation'        => count( $persons ) > 1,
				'image-alt-text'    => esc_attr__( 'About us image', 'proteuswidgets' ),
				'read-more-text'    => __( 'Read more', 'proteuswidgets' ),
				'previous-text'     => __( 'Previous', 'proteuswidgets' ),
				'next-text'         => __( 'Next', 'proteuswidgets' ),
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

			$instance['persons']   = $new_instance['persons'];
			$instance['autocycle'] = sanitize_key( $new_instance['autocycle'] );
			$instance['interval']  = absint( $new_instance['interval'] );

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

			if ( isset( $instance['persons'] ) ) {
				$persons = $instance['persons'];
			}
			else {
				$persons = array(
					array(
						'id'          => 1,
						'tag'         => '',
						'image'       => '',
						'name'        => '',
						'description' => '',
						'link'        => '',
					)
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

			<h4><?php _e( 'Persons:', 'proteuswidgets' ); ?></h4>

			<script type="text/template" id="js-pt-person-<?php echo $this->current_widget_id; ?>">
				<p>
					<label for="<?php echo $this->get_field_id( 'persons' ); ?>-{{id}}-tag"><?php _e( 'Tag:', 'proteuswidgets'); ?></label>
					<input class="widefat" id="<?php echo $this->get_field_id( 'persons' ); ?>-{{id}}-tag" name="<?php echo $this->get_field_name( 'persons' ); ?>[{{id}}][tag]" type="text" value="{{tag}}" />
				</p>

				<p>
					<label for="<?php echo $this->get_field_id( 'persons' ); ?>-{{id}}-image"><?php _e( 'Image URL:', 'proteuswidgets'); ?></label>
					<input class="widefat" id="<?php echo $this->get_field_id( 'persons' ); ?>-{{id}}-image" name="<?php echo $this->get_field_name( 'persons' ); ?>[{{id}}][image]" type="text" value="{{image}}" />
				</p>

				<p>
					<label for="<?php echo $this->get_field_id( 'persons' ); ?>-{{id}}-name"><?php _e( 'Name:', 'proteuswidgets'); ?></label>
					<input class="widefat" id="<?php echo $this->get_field_id( 'persons' ); ?>-{{id}}-name" name="<?php echo $this->get_field_name( 'persons' ); ?>[{{id}}][name]" type="text" value="{{name}}" />
				</p>

				<p>
					<label for="<?php echo $this->get_field_id( 'description' ); ?>-{{id}}-title"><?php _e( 'Description:', 'proteuswidgets'); ?></label>
					<textarea rows="4" class="widefat" id="<?php echo $this->get_field_id( 'description' ); ?>-{{id}}-title" name="<?php echo $this->get_field_name( 'persons' ); ?>[{{id}}][description]">{{description}}</textarea>
				</p>

				<p>
					<label for="<?php echo $this->get_field_id( 'persons' ); ?>-{{id}}-link"><?php _e( 'Link:', 'proteuswidgets'); ?></label>
					<input class="widefat" id="<?php echo $this->get_field_id( 'persons' ); ?>-{{id}}-link" name="<?php echo $this->get_field_name( 'persons' ); ?>[{{id}}][link]" type="text" value="{{link}}" />
				</p>


				<p>
					<input name="<?php echo $this->get_field_name( 'persons' ); ?>[{{id}}][id]" type="hidden" value="{{id}}" />
					<a href="#" class="pt-remove-person  js-pt-remove-person"><span class="dashicons dashicons-dismiss"></span> <?php _e( 'Remove person', 'proteuswidgets' ); ?></a>
				</p>
			</script>
			<div class="pt-widget-about-us" id="persons-<?php echo $this->current_widget_id; ?>">
				<div class="persons"></div>
				<p>
					<a href="#" class="button  js-pt-add-person"><?php _e( 'Add New Person', 'proteuswidgets' ); ?></a>
				</p>
			</div>
			<script type="text/javascript">
				// repopulate the form
				var personsJSON = <?php echo json_encode( $persons ) ?>;

				// get the right widget id and remove the added < > characters at the start and at the end.
				var widgetId = '<<?php echo $this->current_widget_id; ?>>'.slice( 1, -1 );

				if ( _.isFunction( repopulatePersons ) ) {
					repopulatePersons( personsJSON, widgetId );
				}
			</script>

			<hr>

			<p>
				<label for="<?php echo $this->get_field_id( 'autocycle' ); ?>"><?php _e( 'Automatically cycle the carousel?', 'proteuswidgets' ); ?></label>
				<select class="widefat" name="<?php echo $this->get_field_name( 'autocycle' ); ?>" id="<?php echo $this->get_field_id( 'autocycle' ); ?>">
					<option value="yes"<?php selected( $autocycle, 'yes' ) ?>><?php _e( 'Yes', 'proteuswidgets' ); ?></option>
					<option value="no"<?php selected( $autocycle, 'no' ) ?>><?php _e( 'No', 'proteuswidgets' ); ?></option>
				</select>
			</p>

			<p>
				<label for="<?php echo $this->get_field_id( 'interval' ); ?>"><?php _e( 'Interval (in miliseconds):', 'proteuswidgets' ); ?></label>
				<input class="widefat" id="<?php echo $this->get_field_id( 'interval' ); ?>" name="<?php echo $this->get_field_name( 'interval' ); ?>" type="number" min="0" step="500" value="<?php echo esc_attr( $interval ); ?>" />
			</p>

			<?php
		}

	}
	register_widget( 'PW_About_Us' );
}