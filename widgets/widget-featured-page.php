<?php
/**
 * Featured Page Widget
 *
 * @package ProteusWidgets
 * @since 1.0.0
 */

if ( ! class_exists( 'PW_Featured_Page' ) ) {
	class PW_Featured_Page extends WP_Widget {

		/**
		 * Length of the line excerpt.
		 */
		const INLINE_EXCERPT = 60;

		/**
		 * Register widget with WordPress.
		 */
		public function __construct() {
			parent::__construct(
				false, // ID, auto generate when false
				sprintf( 'ProteusThemes: %s', __( 'Featured Page', 'proteuswidgets' ) ), // Name
				array(
					'description' => __( 'Featured Page for Page Builder.', 'proteuswidgets' ),
					'classname'   => 'widget-featured-page',
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

			$page_id        = absint( $instance['page_id'] );
			$layout         = sanitize_key( $instance['layout'] );
			$thumbnail_size = 'inline' === $layout ? 'thumbnail' : 'page-box';

			echo $args['before_widget'];

			if ( $page_id ) {
				$page_obj = new WP_Query( array( 'page_id' => $page_id ) );

				if ( $page_obj->have_posts() ) {
					$page_obj->the_post();

					$excerpt = get_the_excerpt();
					wp_reset_postdata();
					$page_obj->rewind_posts();
					$page_obj->the_post();

					if ( 'inline' === $layout && strlen( $excerpt ) > self::INLINE_EXCERPT ) {
						$excerpt = substr( $excerpt, 0, strpos( $excerpt , ' ', self::INLINE_EXCERPT ) ) . ' &hellip;';
					}

					?>

					<div <?php post_class( "page-box  page-box--{$layout}" ); ?>>
						<a class="page-box__picture" href="<?php the_permalink(); ?>"><?php echo the_post_thumbnail( $thumbnail_size ); ?></a>
						<div class="page-box__content">
							<h5 class="page-box__title  text-uppercase"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h5>
							<?php echo $excerpt; ?>
							<?php if ( 'block' === $layout ) : ?>
								<p><a href="<?php the_permalink(); ?>" class="read-more  read-more--page-box"><?php _e( 'Read more', 'proteuswidgets' ); ?></a></p>
							<?php endif; ?>
						</div>
					</div>

					<?php

					wp_reset_postdata();
				}
			}
			else {
				echo _e( 'Select page in widget settings', 'proteuswidgets' );
			}

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

			$instance['page_id'] = absint( $new_instance['page_id'] );
			$instance['layout']  = sanitize_key( $new_instance['layout'] );

			return $instance;
		}

		/**
		 * Back-end widget form.
		 *
		 * @param array $instance The widget options
		 */
		public function form( $instance ) {
			$page_id = empty( $instance['page_id'] ) ? 0 : (int) $instance['page_id'];
			$layout  = empty( $instance['layout'] ) ? '' : $instance['layout'];

			?>

			<p>
				<label for="<?php echo $this->get_field_id( 'page_id' ); ?>"><?php _e( 'Page:', 'proteuswidgets' ); ?></label> <br>
				<?php
					wp_dropdown_pages( array(
						'selected' => $page_id,
						'name'     => $this->get_field_name( 'page_id' ),
						'id'       => $this->get_field_id( 'page_id' ),
					) );
				?>
			</p>

			<p>
				<label for="<?php echo $this->get_field_id( 'layout' ); ?>"><?php _e( 'Layout:', 'proteuswidgets' ); ?></label> <br>
				<select id="<?php echo $this->get_field_id( 'layout' ); ?>" name="<?php echo $this->get_field_name( 'layout' ); ?>">
					<option value="block" <?php selected( $layout, 'block' ); ?>><?php _e( 'With big picture', 'proteuswidgets' ); ?></option>
					<option value="inline" <?php selected( $layout, 'inline' ); ?>><?php _e( 'With small picture, inline', 'proteuswidgets' ); ?></option>
				</select>
			</p>

			<?php
		}
	}
	register_widget( 'PW_Featured_Page' );
}