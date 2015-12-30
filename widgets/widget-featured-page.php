<?php
/**
 * Featured Page Widget
 *
 * @package ProteusWidgets
 * @since 1.0.0
 */


// add new thumbnails if we need this widget
add_action( 'after_setup_theme', array( 'ProteusWidgets', 'after_theme_setup' ), 11 );

if ( ! class_exists( 'PW_Featured_Page' ) ) {
	class PW_Featured_Page extends PW_Widget {

		/**
		 * Length of the line excerpt.
		 */
		private $excerpt_lengths, $fields;

		/**
		 * Register widget with WordPress.
		 */
		public function __construct() {

			// Overwrite the widget variables of the parent class
			$this->widget_id_base     = 'featured_page';
			$this->widget_name        = esc_html__( 'Featured Page', 'proteuswidgets' );
			$this->widget_description = esc_html__( 'Displays featured image, title and short excerpt of the selected page.', 'proteuswidgets' );
			$this->widget_class       = 'widget-featured-page';

			parent::__construct();

			// Get the settings for the this widget
			$this->fields = apply_filters( 'pw/featured_page_fields', array(
				'read_more_text' => true,
			) );

			$this->excerpt_lengths = apply_filters( 'pw/featured_page_excerpt_lengths', array(
				'inline_excerpt' => 60,
				'block_excerpt'  => 240,
			) );

			// remove srcset and sizes attributes for the pw-inline size
			if ( apply_filters( 'pw/remove_srcset_inline_img', true ) ) {
				add_filter( 'wp_get_attachment_image_attributes', array( $this, 'wp_get_attachment_image_attributes' ), 10, 3 );
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
			// Prepare data for template
			$page_id                    = absint( $instance['page_id'] );
			$instance['layout']         = 'inline' === $instance['layout'] ? 'inline' : 'block';
			$is_block                   = 'block' === $instance['layout'];
			$is_inline                  = ! $is_block;
			$excerpt_length             = $this->excerpt_lengths[sprintf('%s_excerpt', $instance['layout'])];
			$instance['read_more_text'] = empty( $instance['read_more_text'] ) ? esc_html__( 'Read more', 'proteuswidgets' ) : $instance['read_more_text'];
			$thumbnail_size             = $is_inline ? 'pw-inline' : 'pw-page-box';

			/**
			 * Support for the Polylang plugin.
			 * https://proteusthemes.zendesk.com/agent/tickets/5175
			 * https://polylang.wordpress.com/documentation/documentation-for-developers/functions-reference/#pll_get_post
			 */
			if ( function_exists( 'pll_get_post' ) ) {
				$page_id = pll_get_post( $page_id );
			}

			$page_query = new WP_Query( array( 'page_id' => $page_id ) );

			if ( $page_query->have_posts() ) {
				$page_query->the_post();

				// prepare the excerpt
				$excerpt = get_the_excerpt();

				if ( strlen( $excerpt ) > $excerpt_length ) {
					$excerpt = substr( $excerpt, 0, strpos( $excerpt, ' ', $excerpt_length ) ) . ' &hellip;';
				}

				// widget-featured-page template rendering
				echo $this->template_engine->render_template( apply_filters( 'pw/widget_featured_page_view', 'widget-featured-page' ), array(
					'args'           => $args,
					'instance'       => $instance,
					'excerpt'        => $excerpt,
					'thumbnail_size' => $thumbnail_size,
					'is_block'       => $is_block,
				));
			}

			wp_reset_postdata();
		}

		/**
		 * Remove srcset and sizes for pw-inline.
		 *
		 * @link https://developer.wordpress.org/reference/hooks/wp_get_attachment_image_attributes/
		 * @link https://developer.wordpress.org/reference/functions/wp_get_attachment_image/
		 *
		 * @return array
		 */
		public function wp_get_attachment_image_attributes( $attr, $attachment, $size ) {
			if ( 'pw-inline' === $size && isset( $attr['sizes'] ) ) {
				unset( $attr['srcset'] );
				unset( $attr['sizes'] );
			}

			return $attr;
		}

		/**
		 * Sanitize widget form values as they are saved.
		 *
		 * @param array $new_instance The new options
		 * @param array $old_instance The previous options
		 */
		public function update( $new_instance, $old_instance ) {
			$instance = array();

			$instance['page_id']        = absint( $new_instance['page_id'] );
			$instance['layout']         = sanitize_key( $new_instance['layout'] );
			if ( $this->fields['read_more_text'] ) {
				$instance['read_more_text'] = sanitize_text_field( $new_instance['read_more_text'] );
			}

			return $instance;
		}

		/**
		 * Back-end widget form.
		 *
		 * @param array $instance The widget options
		 */
		public function form( $instance ) {
			$page_id        = empty( $instance['page_id'] ) ? 0 : (int) $instance['page_id'];
			$layout         = empty( $instance['layout'] ) ? '' : $instance['layout'];
			$read_more_text = empty( $instance['read_more_text'] ) ? esc_html__( 'Read more', 'proteuswidgets' ) : $instance['read_more_text'];

			?>

			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'page_id' ) ); ?>"><?php _e( 'Page:', 'proteuswidgets' ); ?></label> <br>
				<?php
					wp_dropdown_pages( array(
						'selected' => $page_id,
						'name'     => $this->get_field_name( 'page_id' ),
						'id'       => $this->get_field_id( 'page_id' ),
					) );
				?>
			</p>

			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'layout' ) ); ?>"><?php _e( 'Layout:', 'proteuswidgets' ); ?></label> <br>
				<select id="<?php echo esc_attr( $this->get_field_id( 'layout' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'layout' ) ); ?>">
					<option value="block" <?php selected( $layout, 'block' ); ?>><?php _e( 'With big picture', 'proteuswidgets' ); ?></option>
					<option value="inline" <?php selected( $layout, 'inline' ); ?>><?php _e( 'With small picture, inline', 'proteuswidgets' ); ?></option>
				</select>
			</p>

			<?php if ( $this->fields['read_more_text'] ) : ?>
			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'read_more_text' ) ); ?>"><?php _e( 'Read more text:', 'proteuswidgets' ); ?></label> <br>
				<input id="<?php echo esc_attr( $this->get_field_id( 'read_more_text' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'read_more_text' ) ); ?>" type="text" value="<?php echo esc_attr( $read_more_text ); ?>" />
			</p>
			<?php endif; ?>

			<?php
		}
	}
}