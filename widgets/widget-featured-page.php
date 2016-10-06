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
	class PW_Featured_Page extends Pw_Widget {

		/**
		 * Length of the line excerpt.
		 */
		private $excerpt_lengths;
		private $fields;

		// Basic widget settings
		function widget_id_base() { return 'featured_page'; }
		function widget_name() { return esc_html__( 'Featured Page', 'proteuswidgets' ); }
		function widget_description() { return esc_html__( 'Displays featured image, title and short excerpt of the selected page.', 'proteuswidgets' ); }
		function widget_class() { return 'widget-featured-page'; }

		/**
		 * Register widget with WordPress.
		 */
		public function __construct() {
			parent::__construct();

			// Get the settings for the this widget
			$this->fields = apply_filters( 'pw/featured_page_fields', array(
				'read_more_text' => true,
			) );

			$this->excerpt_lengths = apply_filters( 'pw/featured_page_excerpt_lengths', array(
				'inline_excerpt' => 60,
				'block_excerpt'  => 240,
			) );
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
			$page_id = absint( $instance['page_id'] );

			/**
			 * Support for the Polylang plugin.
			 * https://proteusthemes.zendesk.com/agent/tickets/5175
			 * https://polylang.wordpress.com/documentation/documentation-for-developers/functions-reference/#pll_get_post
			 */
			if ( function_exists( 'pll_get_post' ) ) {
				$page_id = pll_get_post( $page_id );
			}

			$instance['layout']         = sanitize_key( $instance['layout'] );
			$instance['read_more_text'] = empty( $instance['read_more_text'] ) ? esc_html__( 'Read more', 'proteuswidgets' ) : sanitize_text_field( $instance['read_more_text'] );
			$thumbnail_size             = 'inline' === $instance['layout'] ? 'pw-inline' : 'pw-page-box';

			// Get basic page info
			if ( $page_id ) {
				$page = (array) get_post( $page_id );
			}

			// Prepare the excerpt text
			$excerpt = ! empty( $page['post_excerpt'] ) ? $page['post_excerpt'] : $page['post_content'];

			if ( 'inline' === $instance['layout'] && strlen( $excerpt ) > $this->excerpt_lengths['inline_excerpt'] ) {
				$strpos  = strpos( $excerpt , ' ', $this->excerpt_lengths['inline_excerpt'] );
				$excerpt = ( false !== $strpos ) ? substr( $excerpt, 0, $strpos ) . ' &hellip;' : $excerpt;
			}
			elseif ( strlen( $excerpt ) > $this->excerpt_lengths['block_excerpt'] ) {
				$strpos  = strpos( $excerpt , ' ', $this->excerpt_lengths['block_excerpt'] );
				$excerpt = ( false !== $strpos ) ? substr( $excerpt, 0, $strpos ) . ' &hellip;' : $excerpt;
			}

			$page['post_excerpt'] = sanitize_text_field( $excerpt );
			$page['link']         = get_permalink( $page_id );
			$page['thumbnail']    = get_the_post_thumbnail( $page_id, $thumbnail_size );
			if ( 'block' === $instance['layout'] ) {
				$attachment_image_id   = get_post_thumbnail_id( $page_id );
				$attachment_image_data = wp_get_attachment_image_src( $attachment_image_id, 'pw-page-box' );
				$page['image_url']     = $attachment_image_data[0];
				$page['image_width']   = $attachment_image_data[1];
				$page['image_height']  = $attachment_image_data[2];
				$page['srcset']        = PW_Functions::get_attachment_image_srcs( $attachment_image_id, array( 'pw-page-box', 'full' ) );
			}

			// Mustache widget-featured-page template rendering
			echo $this->mustache->render( apply_filters( 'pw/widget_featured_page_view', 'widget-featured-page' ), array(
				'args'      => $args,
				'page'      => $page,
				'instance'  => $instance,
				'block'     => 'block' === $instance['layout'],
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