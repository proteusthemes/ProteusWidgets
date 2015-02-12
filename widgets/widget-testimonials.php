<?php
/**
 * Testimonials Widget
 *
 * @package ProteusWidgets
 * @since 0.1.0
 */

if ( ! class_exists( 'PW_Testimonials' ) ) {
	class PW_Testimonials extends WP_Widget {

		/**
		 * Register widget with WordPress.
		 */
		public function __construct() {
			parent::__construct(
				false, // ID, auto generate when false
				sprintf( 'ProteusThemes: %s', __( 'Testimonials', 'proteuswidgets' ) ), // Name
				array(
					'description' => __( 'Testimonials for Page Builder.', 'proteuswidgets' ),
					'classname'   => 'widget-testimonials',
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
			$title     = apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base );
			$autocycle = empty( $instance['autocycle'] ) ? false : 'yes' === $instance['autocycle'];
			$interval  = empty( $instance['interval'] ) ? 5000 : absint( $instance['interval'] );

			if ( isset( $instance['quote'] ) ) {
				$testimonials = array( array(
					'quote'  => $instance['quote'],
					'author' => $instance['author'],
					'rating' => $instance['rating'],
				) );
			}
			else {
				$testimonials = array_values( $instance['testimonials'] );
			}

			$spans = count( $testimonials ) < 2 ? '12' : '6';

			echo $args['before_widget'];

			?>

			<div class="testimonial">
				<h2 class="widget-title">
				<?php if ( count( $testimonials ) > 2 ) : ?>
					<a class="testimonial__carousel  testimonial__carousel--left" href="#carousel-testimonials-<?php echo $args['widget_id'] ?>" data-slide="next"><i class="fa  fa-angle-right" aria-hidden="true"></i><span class="sr-only" role="button">Next</span></a>
					<a class="testimonial__carousel  testimonial__carousel--right" href="#carousel-testimonials-<?php echo $args['widget_id'] ?>" data-slide="prev"><i class="fa  fa-angle-left" aria-hidden="true"></i><span class="sr-only" role="button">Previous</span></a>
				<?php endif; ?>

					<?php echo $title; ?>
				</h2>
				<div id="carousel-testimonials-<?php echo $args['widget_id'] ?>" class="carousel slide" <?php echo $autocycle ? 'data-ride="carousel" data-interval="' . $interval . '"' : ''; ?>>
					<!-- Wrapper for slides -->
					<div class="carousel-inner" role="listbox">
						<div class="item active">
							<div class="row">
							<?php foreach ($testimonials as $index => $testimonial) : ?>
								<?php echo ( 0 !== $index && 0 === $index % 2 ) ? '</div></div> <div class="item"><div class="row">' : ''; ?>
								<div class="col-xs-12  col-sm-<?php echo $spans; ?>">
									<blockquote class="testimonial__quote">
										<?php echo $testimonial['quote']; ?>
									</blockquote>
									<cite class="testimonial__author">
										<?php echo $testimonial['author']; ?>
									</cite>

									<?php if ( absint( $testimonial['rating'] ) > 0 ): ?>
										<div class="testimonial__rating">
										<?php
											for ( $i = 0; $i < $testimonial['rating']; $i++) {
												echo '<i class="fa  fa-star"></i>';
											}
										?>
										</div>
									<?php endif; ?>
								</div>
							<?php endforeach; ?>
							</div>
						</div>
					</div>
				</div>
			</div>

			<?php
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

			$instance['title']     = wp_kses_post( $new_instance['title'] );
			$instance['autocycle'] = sanitize_key( $new_instance['autocycle'] );
			$instance['interval']  = absint( $new_instance['interval'] );

			$instance['testimonials'] = $new_instance['testimonials'];

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
				) );
			}
			else {
				$testimonials = isset( $instance['testimonials'] ) ? array_values( $instance['testimonials'] ) : array(
					array(
						'id'     => 1,
						'quote'  => '',
						'author' => '',
						'rating' => 5,
					)
				);
			}

			?>

			<p>
				<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _ex( 'Title:', 'proteuswidgets' ); ?></label>
				<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
			</p>

			<p>
				<label for="<?php echo $this->get_field_id( 'autocycle' ); ?>"><?php _ex( 'Automatically cycle the carousel?', 'proteuswidgets' ); ?>:</label>
				<select class="widefat" name="<?php echo $this->get_field_name( 'autocycle' ); ?>" id="<?php echo $this->get_field_id( 'autocycle' ); ?>">
					<option value="yes"<?php selected( $autocycle, 'yes' ) ?>><?php _e( 'Yes', 'proteuswidgets' ); ?></option>
					<option value="no"<?php selected( $autocycle, 'no' ) ?>><?php _e( 'No', 'proteuswidgets' ); ?></option>
				</select>
			</p>

			<p>
				<label for="<?php echo $this->get_field_id( 'interval' ); ?>"><?php _ex( 'Interval (in miliseconds):', 'proteuswidgets' ); ?>:</label>
				<input class="widefat" id="<?php echo $this->get_field_id( 'interval' ); ?>" name="<?php echo $this->get_field_name( 'interval' ); ?>" type="number" min="0" step="500" value="<?php echo esc_attr( $interval ); ?>" />
			</p>

			<hr>

			<h4><?php _ex( 'Testimonials:', 'proteuswidgets' ); ?></h4>

			<script type="text/template" id="js-pt-testimonial-<?php echo $this->id; ?>">
				<p>
					<label for="<?php echo $this->get_field_id( 'quote' ); ?>-{{id}}-title"><?php _ex( 'Quote', 'backend', 'buildpress_wp'); ?>:</label>
					<textarea rows="4" class="widefat" id="<?php echo $this->get_field_id( 'quote' ); ?>-{{id}}-title" name="<?php echo $this->get_field_name( 'testimonials' ); ?>[{{id}}][quote]">{{quote}}</textarea>
				</p>

				<p>
					<label for="<?php echo $this->get_field_id( 'testimonials' ); ?>-{{id}}-author"><?php _ex( 'Author:', 'backend', 'buildpress_wp'); ?></label> <br>
					<input class="widefat" id="<?php echo $this->get_field_id( 'testimonials' ); ?>-{{id}}-author" name="<?php echo $this->get_field_name( 'testimonials' ); ?>[{{id}}][author]" type="text" value="{{author}}" />
				</p>


				<p>
					<label for="<?php echo $this->get_field_id( 'testimonials' ); ?>-{{id}}-rating"><?php _ex( 'Rating:', 'backend', 'buildpress_wp'); ?></label>
					<select name="<?php echo $this->get_field_name( 'testimonials' ); ?>[{{id}}][rating]" id="<?php echo $this->get_field_id( 'rating' ); ?>-{{id}}-rating" class="js-rating">
						<option value="0">0</option>
						<option value="1">1</option>
						<option value="2">2</option>
						<option value="3">3</option>
						<option value="4">4</option>
						<option value="5">5</option>
 					</select>
				</p>

				<p>
					<input name="<?php echo $this->get_field_name( 'testimonials' ); ?>[{{id}}][id]" type="hidden" value="{{id}}" />
					<a href="#" class="pt-remove-testimonial  js-pt-remove-testimonial"><span class="dashicons dashicons-dismiss"></span> <?php _ex( 'Remove Testimonial', 'proteuswidgets' ); ?></a>
				</p>
			</script>
			<div class="pt-widget-testimonials" id="testimonials-<?php echo $this->id; ?>">
				<div class="testimonials"></div>
				<p>
					<a href="#" class="button  js-pt-add-testimonial">Add New Testimonial</a>
				</p>
			</div>
			<script type="text/javascript">
				// repopulate the form
				var testimonialsJSON = <?php echo json_encode( $testimonials ) ?>;

				if ( _.isFunction( repopulateTestimonials ) ) {
					repopulateTestimonials( testimonialsJSON, '<?php echo $this->id; ?>' );
				}
			</script>

			<?php
		}

	}
	register_widget( 'PW_Testimonials' );
}