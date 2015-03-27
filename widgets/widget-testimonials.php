<?php
/**
 * Testimonials Widget
 *
 * @package ProteusWidgets
 * @since 1.0.0
 */

if ( ! class_exists( 'PW_Testimonials' ) ) {
	class PW_Testimonials extends PW_Widget {

		private $fields;
		private $current_widget_id;

		// Basic widget settings
		function widget_name() { return __( 'Testimonials', 'proteuswidgets' ); }
		function widget_description() { return __( 'Testimonials widget for Page Builder.', 'proteuswidgets' ); }
		function widget_class() { return 'widget-testimonials'; }

		/**
		 * Register widget with WordPress.
		 */
		public function __construct() {

			parent::__construct();

			// Get the settings for the testimonial widgets
			$this->fields = apply_filters( 'pw/testimonial_widget', array(
				'rating' => true,
				'author_description' => false,
				'number_of_testimonial_per_slide' => 2,
			) );

			// Set the max number of testimonials per slide to 2
			if ( $this->fields['number_of_testimonial_per_slide'] > 2 ) {
				$this->fields['number_of_testimonial_per_slide'] = 2;
			}

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
			if ( isset( $instance['quote'] ) ) {
				$testimonials = array( array(
					'quote'  => $instance['quote'],
					'author' => $instance['author'],
					'rating' => $instance['rating'],
					'author_description' => $instance['author_description'],
				) );
			}
			else {
				$testimonials = array_values( $instance['testimonials'] );
			}

			$instance['spans'] = count( $testimonials ) < 2 ? '12' : '6';

			// set the layout of the testimonials per slide
			if ( $this->fields['number_of_testimonial_per_slide'] < 2 ) {
				$instance['spans'] = '12';
			}

			$testimonials = PWFunctions::reorder_widget_array_key_values($testimonials);
			if ( isset( $testimonials[0] ) ) {
				$testimonials[0]['active'] = 'active';
			}
			foreach ($testimonials as $key => $value) {
				$testimonials[$key]['more-at-once'] = ( 0 !== $key && 0 === $key % $this->fields['number_of_testimonial_per_slide'] ) ? '</div></div> <div class="item"><div class="row">' : '';
				if ( $this->fields['rating'] && isset( $testimonials[$key]['rating'] ) ) {
					$testimonials[$key]['rating'] = ( $testimonials[$key]['rating'] > 0 ) ? range( 0, ( $testimonials[$key]['rating'] - 1 ) ) : 0;
					$testimonials[$key]['display-ratings'] = $testimonials[$key]['rating'] > 0;
				}
			}

			$instance['title']           = apply_filters( 'widget_title', $instance['title'] , $instance, $this->id_base );
			$args['widget_id']           = esc_attr( $args['widget_id'] );
			$instance['navigation']      = count( $testimonials ) > $this->fields['number_of_testimonial_per_slide'];
			$instance['slider_settings'] = 'yes' === $instance['autocycle'] ? esc_attr( empty( $instance['interval'] ) ? 5000 : absint( $instance['interval'] ) ) : 'false';

			$text = array(
				'previous'   => __( 'Previous', 'proteuswidgets' ),
				'next'       => __( 'Next', 'proteuswidgets' ),
			);

			// Mustache widget-testimonials template rendering
			echo $this->mustache->render( apply_filters( 'pw/widget_testimonials_view', 'widget-testimonials' ), array(
				'args'         => $args,
				'instance'     => $instance,
				'testimonials' => $testimonials,
				'text'         => $text,
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

			$instance['title']     = wp_kses_post( $new_instance['title'] );
			$instance['autocycle'] = sanitize_key( $new_instance['autocycle'] );
			$instance['interval']  = absint( $new_instance['interval'] );

			foreach ( $new_instance['testimonials'] as $key => $testimonial ) {
				$instance['testimonials'][$key]['id']                 = sanitize_key( $testimonial['id'] );
				$instance['testimonials'][$key]['quote']              = sanitize_text_field( $testimonial['quote'] );
				$instance['testimonials'][$key]['author']             = sanitize_text_field( $testimonial['author'] );
				$instance['testimonials'][$key]['rating']             = sanitize_text_field( $testimonial['rating'] );
				$instance['testimonials'][$key]['author_description'] = sanitize_text_field( $testimonial['author_description'] );
			}

			return $instance;
		}

		/**
		 * Back-end widget form.
		 *
		 * @param array $instance The widget options
		 */
		public function form( $instance ) {
			$title     = empty( $instance['title'] ) ? 'Testimonials' : $instance['title'];
			$autocycle = empty( $instance['autocycle'] ) ? 'no' : $instance['autocycle'];
			$interval  = empty( $instance['interval'] ) ? 5000 : $instance['interval'];

			if ( isset( $instance['quote'] ) ) {
				$testimonials = array( array(
					'id'     => 1,
					'quote'  => $instance['quote'],
					'author' => $instance['author'],
					'rating' => $instance['rating'],
					'author_description' => $instance['author_description'],
				) );
			}
			else {
				$testimonials = isset( $instance['testimonials'] ) ? array_values( $instance['testimonials'] ) : array(
					array(
						'id'     => 1,
						'quote'  => '',
						'author' => '',
						'rating' => 5,
						'author_description' => '',
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

			<p>
				<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'proteuswidgets' ); ?></label>
				<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
			</p>

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

			<hr>

			<h4><?php _e( 'Testimonials:', 'proteuswidgets' ); ?></h4>

			<script type="text/template" id="js-pt-testimonial-<?php echo $this->current_widget_id; ?>">
				<p>
					<label for="<?php echo $this->get_field_id( 'quote' ); ?>-{{id}}-title"><?php _e( 'Quote:', 'proteuswidgets'); ?></label>
					<textarea rows="4" class="widefat" id="<?php echo $this->get_field_id( 'quote' ); ?>-{{id}}-title" name="<?php echo $this->get_field_name( 'testimonials' ); ?>[{{id}}][quote]">{{quote}}</textarea>
				</p>

				<p>
					<label for="<?php echo $this->get_field_id( 'testimonials' ); ?>-{{id}}-author"><?php _e( 'Author:', 'proteuswidgets'); ?></label> <br>
					<input class="widefat" id="<?php echo $this->get_field_id( 'testimonials' ); ?>-{{id}}-author" name="<?php echo $this->get_field_name( 'testimonials' ); ?>[{{id}}][author]" type="text" value="{{author}}" />
				</p>

				<?php if ( $this->fields['author_description'] ) : ?>
				<p>
					<label for="<?php echo $this->get_field_id( 'testimonials' ); ?>-{{id}}-author_description"><?php _e( 'Author Description:', 'proteuswidgets'); ?></label>
					<input class="widefat" id="<?php echo $this->get_field_id( 'testimonials' ); ?>-{{id}}-author_description" name="<?php echo $this->get_field_name( 'testimonials' ); ?>[{{id}}][author_description]" type="text" value="{{author_description}}" />
				</p>
				<?php endif; ?>

				<?php if ( $this->fields['rating'] ) : ?>
				<p>
					<label for="<?php echo $this->get_field_id( 'testimonials' ); ?>-{{id}}-rating"><?php _e( 'Rating:', 'proteuswidgets'); ?></label>
					<select name="<?php echo $this->get_field_name( 'testimonials' ); ?>[{{id}}][rating]" id="<?php echo $this->get_field_id( 'rating' ); ?>-{{id}}-rating" class="js-rating">
						<option value="0">0</option>
						<option value="1">1</option>
						<option value="2">2</option>
						<option value="3">3</option>
						<option value="4">4</option>
						<option value="5">5</option>
 					</select>
				</p>
				<?php endif; ?>

				<p>
					<input name="<?php echo $this->get_field_name( 'testimonials' ); ?>[{{id}}][id]" type="hidden" value="{{id}}" />
					<a href="#" class="pt-remove-testimonial  js-pt-remove-testimonial"><span class="dashicons dashicons-dismiss"></span> <?php _e( 'Remove Testimonial', 'proteuswidgets' ); ?></a>
				</p>
			</script>
			<div class="pt-widget-testimonials" id="testimonials-<?php echo $this->current_widget_id; ?>">
				<div class="testimonials"></div>
				<p>
					<a href="#" class="button  js-pt-add-testimonial">Add New Testimonial</a>
				</p>
			</div>
			<script type="text/javascript">
				// repopulate the form
				var testimonialsJSON = <?php echo json_encode( $testimonials ) ?>;

				// get the right widget id and remove the added < > characters at the start and at the end.
				var widgetId = '<<?php echo $this->current_widget_id; ?>>'.slice( 1, -1 );

				if ( _.isFunction( repopulateTestimonials ) ) {
					repopulateTestimonials( testimonialsJSON, widgetId );
				}
			</script>

			<?php
		}

	}
	register_widget( 'PW_Testimonials' );
}