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
		function widget_id_base() { return 'testimonials'; }
		function widget_name() { return esc_html__( 'Testimonials', 'proteuswidgets' ); }
		function widget_description() { return ''; }
		function widget_class() { return 'widget-testimonials'; }

		/**
		 * Register widget with WordPress.
		 */
		public function __construct() {

			parent::__construct();

			// Get the settings for the testimonial widgets
			$this->fields = apply_filters( 'pw/testimonial_widget', array(
				'rating'                          => true,
				'author_description'              => false,
				'author_avatar'                   => false,
				'number_of_testimonial_per_slide' => 2,
				'bootstrap_version'               => 3,
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
				$testimonials = array(
					array(
						'quote'              => $instance['quote'],
						'author'             => $instance['author'],
						'rating'             => $instance['rating'],
						'author_description' => $instance['author_description'],
						'author_avatar'      => $instance['author_avatar'],
					),
				);
			}
			else {
				$testimonials = array_values( $instance['testimonials'] );
			}

			$instance['spans'] = count( $testimonials ) < 2 ? '12' : '6';

			// set the layout of the testimonials per slide
			if ( $this->fields['number_of_testimonial_per_slide'] < 2 ) {
				$instance['spans'] = '12';
			}

			$testimonials = array_values( $testimonials );

			if ( isset( $testimonials[0] ) ) {
				$testimonials[0]['active'] = 'active';
			}

			foreach ( $testimonials as $key => $value ) {
				$testimonials[ $key ]['more-at-once'] = '';

				if ( 0 !== $key && 0 === $key % $this->fields['number_of_testimonial_per_slide'] ) {
					$testimonials[ $key ]['more-at-once'] = '</div></div> <div class="' . ( ( $this->fields['bootstrap_version'] > 3 ) ? 'carousel-' : '' ) . 'item"><div class="row">';
				}

				if ( $this->fields['rating'] && isset( $testimonials[ $key ]['rating'] ) ) {
					$testimonials[ $key ]['rating'] = ( $testimonials[ $key ]['rating'] > 0 ) ? range( 0, ( $testimonials[ $key ]['rating'] - 1 ) ) : 0;
					$testimonials[ $key ]['display-ratings'] = $testimonials[ $key ]['rating'] > 0;
				}
			}

			$instance['title']           = apply_filters( 'widget_title', $instance['title'] , $instance, $this->id_base );
			$args['widget_id']           = esc_attr( $args['widget_id'] );
			$instance['navigation']      = count( $testimonials ) > $this->fields['number_of_testimonial_per_slide'];
			$instance['slider_settings'] = 'yes' === $instance['autocycle'] ? esc_attr( empty( $instance['interval'] ) ? 5000 : absint( $instance['interval'] ) ) : 'false';

			$text = array(
				'previous'   => esc_html__( 'Previous', 'proteuswidgets' ),
				'next'       => esc_html__( 'Next', 'proteuswidgets' ),
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
				$instance['testimonials'][ $key ]['id']     = sanitize_key( $testimonial['id'] );
				$instance['testimonials'][ $key ]['quote']  = wp_kses_post( $testimonial['quote'] );
				$instance['testimonials'][ $key ]['author'] = sanitize_text_field( $testimonial['author'] );

				if ( $this->fields['author_description'] ) {
					$instance['testimonials'][ $key ]['author_description'] = sanitize_text_field( $testimonial['author_description'] );
				}

				if ( $this->fields['author_avatar'] ) {
					$instance['testimonials'][ $key ]['author_avatar'] = esc_url_raw( $testimonial['author_avatar'] );
				}

				if ( $this->fields['rating'] ) {
					$instance['testimonials'][ $key ]['rating']           = sanitize_text_field( $testimonial['rating'] );
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
			$title     = empty( $instance['title'] ) ? 'Testimonials' : $instance['title'];
			$autocycle = empty( $instance['autocycle'] ) ? 'no' : $instance['autocycle'];
			$interval  = empty( $instance['interval'] ) ? 5000 : $instance['interval'];

			if ( isset( $instance['quote'] ) ) {
				$testimonials = array(
					array(
						'id'                 => 1,
						'quote'              => $instance['quote'],
						'author'             => $instance['author'],
						'rating'             => $instance['rating'],
						'author_description' => $instance['author_description'],
						'author_avatar'      => $instance['author_avatar'],
					),
				);
			}
			else {
				$testimonials = isset( $instance['testimonials'] ) ? array_values( $instance['testimonials'] ) : array(
					array(
						'id'                 => 1,
						'quote'              => '',
						'author'             => '',
						'rating'             => 5,
						'author_description' => '',
						'author_avatar'      => '',
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

			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title:', 'proteuswidgets' ); ?></label>
				<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
			</p>

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

			<hr>

			<h4><?php esc_html_e( 'Testimonials', 'proteuswidgets' ); ?></h4>

			<script type="text/template" id="js-pt-testimonial-<?php echo esc_attr( $this->current_widget_id ); ?>">
				<p>
					<label for="<?php echo esc_attr( $this->get_field_id( 'quote' ) ); ?>-{{id}}-title"><?php esc_html_e( 'Quote:', 'proteuswidgets' ); ?></label>
					<textarea rows="4" class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'quote' ) ); ?>-{{id}}-title" name="<?php echo esc_attr( $this->get_field_name( 'testimonials' ) ); ?>[{{id}}][quote]">{{quote}}</textarea>
				</p>

				<p>
					<label for="<?php echo esc_attr( $this->get_field_id( 'testimonials' ) ); ?>-{{id}}-author"><?php esc_html_e( 'Author:', 'proteuswidgets' ); ?></label> <br>
					<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'testimonials' ) ); ?>-{{id}}-author" name="<?php echo esc_attr( $this->get_field_name( 'testimonials' ) ); ?>[{{id}}][author]" type="text" value="{{author}}" />
				</p>

				<?php if ( $this->fields['author_description'] ) : ?>
				<p>
					<label for="<?php echo esc_attr( $this->get_field_id( 'testimonials' ) ); ?>-{{id}}-author_description"><?php esc_html_e( 'Author description:', 'proteuswidgets' ); ?></label>
					<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'testimonials' ) ); ?>-{{id}}-author_description" name="<?php echo esc_attr( $this->get_field_name( 'testimonials' ) ); ?>[{{id}}][author_description]" type="text" value="{{author_description}}" />
				</p>
				<?php endif; ?>

				<?php if ( $this->fields['author_avatar'] ) : ?>
				<p>
					<label for="<?php echo esc_attr( $this->get_field_id( 'testimonials' ) ); ?>-{{id}}-author_avatar"><?php esc_html_e( 'Author avatar:', 'proteuswidgets' ); ?></label>
					<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'testimonials' ) ); ?>-{{id}}-author_avatar" name="<?php echo esc_attr( $this->get_field_name( 'testimonials' ) ); ?>[{{id}}][author_avatar]" type="text" value="{{author_avatar}}" />
					<input type="button" style="margin-top: 5px;" onclick="ProteusWidgetsUploader.imageUploader.openFileFrame('<?php echo esc_attr( $this->get_field_id( 'testimonials' ) ); ?>-{{id}}-author_avatar');" class="button button-secondary button-upload-image" value="Upload Image" />
				</p>
				<?php endif; ?>

				<?php if ( $this->fields['rating'] ) : ?>
				<p>
					<label for="<?php echo esc_attr( $this->get_field_id( 'testimonials' ) ); ?>-{{id}}-rating"><?php esc_html_e( 'Rating:', 'proteuswidgets' ); ?></label>
					<select name="<?php echo esc_attr( $this->get_field_name( 'testimonials' ) ); ?>[{{id}}][rating]" id="<?php echo esc_attr( $this->get_field_id( 'rating' ) ); ?>-{{id}}-rating" class="js-rating">
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
					<input name="<?php echo esc_attr( $this->get_field_name( 'testimonials' ) ); ?>[{{id}}][id]" type="hidden" value="{{id}}" />
					<a href="#" class="pt-remove-testimonial  js-pt-remove-testimonial"><span class="dashicons dashicons-dismiss"></span> <?php esc_html_e( 'Remove Testimonial', 'proteuswidgets' ); ?></a>
				</p>
			</script>
			<div class="pt-widget-testimonials" id="testimonials-<?php echo esc_attr( $this->current_widget_id ); ?>">
				<div class="testimonials"></div>
				<p>
					<a href="#" class="button  js-pt-add-testimonial"><?php _e( 'Add New Testimonial', 'proteuswidgets' ); ?></a>
				</p>
			</div>
			<script type="text/javascript">
				(function() {
					// repopulate the form
					var testimonialsJSON = <?php echo wp_json_encode( $testimonials ) ?>;

					// get the right widget id and remove the added < > characters at the start and at the end.
					var widgetId = '<<?php echo esc_js( $this->current_widget_id ); ?>>'.slice( 1, -1 );

					if ( _.isFunction( ProteusWidgets.Utils.repopulateTestimonials ) ) {
						ProteusWidgets.Utils.repopulateTestimonials( testimonialsJSON, widgetId );
					}
				})();
			</script>

			<?php
		}

	}
}
