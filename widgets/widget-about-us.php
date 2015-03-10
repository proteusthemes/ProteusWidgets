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
			$autocycle = empty( $instance['autocycle'] ) ? false : 'yes' === $instance['autocycle'];
			$interval  = empty( $instance['interval'] ) ? 5000 : absint( $instance['interval'] );
			$first = true;

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

			echo $args['before_widget'];
			?>
			<div id="carousel-persons-<?php echo esc_attr( $args['widget_id'] ); ?>" class="carousel slide" <?php echo $autocycle ? 'data-ride="carousel" data-interval="' . esc_attr( $interval ) . '"' : ''; ?>>
				<div class="carousel-inner" role="listbox">
					<?php
						foreach ($persons as $person) :
						//typecast, because of the demo widgets import. By default it's an object(stdClass)
						$person = (array)$person;
					?>
						<div class="item  <?php echo ( $first ) ? 'active' : ''; $first = false; ?>">
							<?php if ( ! empty( $person['tag'] ) ) : ?>
							<<?php if( ! empty( $person['link'] ) ) : ?>a href="<?php echo esc_url( $person['link'] ); ?>"<?php else : ?>div<?php endif; ?> class="about-us__tag">
								<?php echo $person['tag']; ?>
							</<?php if( ! empty( $person['link'] ) ) : ?>a <?php else: ?>div<?php endif; ?>>
							<?php endif; ?>

							<?php if( ! empty( $person['image'] ) ) : ?>
								<img class="about-us__image" src="<?php echo $person['image'] ?>" alt="<?php _e( 'About us image', 'proteuswidgets' ); ?>">
							<?php endif; ?>
							<h5 class="about-us__name"><?php echo wp_kses_post( $person['name'] ); ?></h5>
							<p class="about-us__description"><?php echo wp_kses_post( $person['description'] ); ?></p>
							<?php if( ! empty( $person['link'] ) ) : ?>
								<a class="read-more  about-us__link" href="<?php echo esc_url( $person['link'] ); ?>"><?php _e( 'Read more', 'proteuswidgets' ); ?></a>
							<?php endif; ?>
						</div>
					<?php
					endforeach;
					?>
			</div>
		</div>

		<?php
			if ( count( $persons ) > 1 ) :
		?>
			<div class="about-us__navigation">
				<a class="person__carousel  person__carousel--left about-us__navigation__left" href="#carousel-persons-<?php echo esc_attr( $args['widget_id'] ); ?>" data-slide="prev"><i class="fa  fa-chevron-left" aria-hidden="true"></i><span class="sr-only" role="button"><?php _e( 'Previous', 'proteuswidgets' ); ?></span></a>
				<a class="person__carousel  person__carousel--right about-us__navigation__right" href="#carousel-persons-<?php echo $args['widget_id'] ?>" data-slide="next"><i class="fa  fa-chevron-right" aria-hidden="true"></i><span class="sr-only" role="button"><?php _e( 'Next', 'proteuswidgets' ); ?></span></a>
			</div>
		<?php
			endif;

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

			?>

			<h4><?php _e( 'Persons:', 'proteuswidgets' ); ?></h4>

			<script type="text/template" id="js-pt-person-<?php echo $this->id; ?>">
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
			<div class="pt-widget-about-us" id="persons-<?php echo $this->id; ?>">
				<div class="persons"></div>
				<p>
					<a href="#" class="button  js-pt-add-person"><?php _e( 'Add New Person', 'proteuswidgets' ); ?></a>
				</p>
			</div>
			<script type="text/javascript">
				// repopulate the form
				var personsJSON = <?php echo json_encode( $persons ) ?>;

				if ( _.isFunction( repopulatePersons ) ) {
					repopulatePersons( personsJSON, '<?php echo $this->id; ?>' );
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