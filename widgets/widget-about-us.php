<?php
/**
 * Testimonials Widget
 *
 * @package ProteusWidgets
 * @since 0.1.0
 */

if ( ! class_exists( 'PW_About_Us' ) ) {
	class PW_About_Us extends WP_Widget {

		/**
		 * Register widget with WordPress.
		 */
		public function __construct() {
			parent::__construct(
				false, // ID, auto generate when false
				sprintf( 'ProteusThemes: %s', __( 'About Us', 'proteuswidgets' ) ), // Name
				array(
					'description' => __( 'About Us widget used in sidebar.', 'proteuswidgets' ),
					'classname'   => 'widget-about-us',
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
			$persons = $instance['persons'];

			extract( $persons );

			echo $args['before_widget'];

			//TODO: front end display

			foreach ($persons as $person) :
			?>
				<div><?php echo $person['title'] ?></div>
				<img src="<?php echo $person['image'] ?>" width="100%">
				<h5><?php echo $person['name'] ?></h5>
				<div><?php echo $person['description'] ?></div>
				<a href="<?php echo $person['link'] ?>"><?php _e( 'Read more', 'proteuswidgets' ); ?></a>
			<?php
			endforeach;

			echo $args['after_widget'];
		}

		/**
		 * Sanitize widget form values as they are saved.
		 *
		 * @param array $new_instance The new options
		 * @param array $old_instance The previous options
		 */
		public function update( $new_instance, $old_instance ) {
			$instance = array();

			$instance['persons'] = $new_instance['persons'];

			return $instance;
		}

		/**
		 * Back-end widget form.
		 *
		 * @param array $instance The widget options
		 */
		public function form( $instance ) {

			if ( isset( $instance['name'] ) ) {
				$persons = array( array(
					'id'          => 1,
					'title'       => $instance['title'],
					'image'       => $instance['image'],
					'name'        => $instance['name'],
					'description' => $instance['description'],
					'link'        => $instance['link'],
				) );
			}
			else {
				$persons = isset( $instance['persons'] ) ? array_values( $instance['persons'] ) : array(
					array(
						'id'          => 1,
						'title'       => '',
						'image'       => '',
						'name'        => '',
						'description' => '',
						'link'        => '',
					)
				);
			}

			?>

			<h4><?php _ex( 'Persons:', 'proteuswidgets' ); ?></h4>

			<script type="text/template" id="js-pt-person-<?php echo $this->id; ?>">
				<p>
					<label for="<?php echo $this->get_field_id( 'persons' ); ?>-{{id}}-title"><?php _ex( 'Title', 'backend', 'proteus_widgets'); ?>:</label>
					<input class="widefat" id="<?php echo $this->get_field_id( 'persons' ); ?>-{{id}}-title" name="<?php echo $this->get_field_name( 'persons' ); ?>[{{id}}][title]" type="text" value="{{title}}" />
				</p>

				<p>
					<label for="<?php echo $this->get_field_id( 'persons' ); ?>-{{id}}-image"><?php _ex( 'Image URL', 'backend', 'proteus_widgets'); ?>:</label>
					<input class="widefat" id="<?php echo $this->get_field_id( 'persons' ); ?>-{{id}}-image" name="<?php echo $this->get_field_name( 'persons' ); ?>[{{id}}][image]" type="text" value="{{image}}" />
				</p>

				<p>
					<label for="<?php echo $this->get_field_id( 'persons' ); ?>-{{id}}-name"><?php _ex( 'Name', 'backend', 'proteus_widgets'); ?>:</label>
					<input class="widefat" id="<?php echo $this->get_field_id( 'persons' ); ?>-{{id}}-name" name="<?php echo $this->get_field_name( 'persons' ); ?>[{{id}}][name]" type="text" value="{{name}}" />
				</p>

				<p>
					<label for="<?php echo $this->get_field_id( 'description' ); ?>-{{id}}-title"><?php _ex( 'Description', 'backend', 'proteus_widgets'); ?>:</label>
					<textarea rows="4" class="widefat" id="<?php echo $this->get_field_id( 'description' ); ?>-{{id}}-title" name="<?php echo $this->get_field_name( 'persons' ); ?>[{{id}}][description]">{{description}}</textarea>
				</p>

				<p>
					<label for="<?php echo $this->get_field_id( 'persons' ); ?>-{{id}}-link"><?php _ex( 'Link', 'backend', 'proteus_widgets'); ?>:</label>
					<input class="widefat" id="<?php echo $this->get_field_id( 'persons' ); ?>-{{id}}-link" name="<?php echo $this->get_field_name( 'persons' ); ?>[{{id}}][link]" type="text" value="{{link}}" />
				</p>


				<p>
					<input name="<?php echo $this->get_field_name( 'persons' ); ?>[{{id}}][id]" type="hidden" value="{{id}}" />
					<a href="#" class="pt-remove-person  js-pt-remove-person"><span class="dashicons dashicons-dismiss"></span> <?php _ex( 'Remove person', 'proteuswidgets' ); ?></a>
				</p>
			</script>
			<div class="pt-widget-about-us" id="persons-<?php echo $this->id; ?>">
				<div class="persons"></div>
				<p>
					<a href="#" class="button  js-pt-add-person">Add New Person</a>
				</p>
			</div>
			<script type="text/javascript">
				// repopulate the form
				var personsJSON = <?php echo json_encode( $persons ) ?>;

				if ( _.isFunction( repopulatePersons ) ) {
					repopulatePersons( personsJSON, '<?php echo $this->id; ?>' );
				}
			</script>

			<?php
		}

	}
	register_widget( 'PW_About_Us' );
}