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
				'read_more_text'      => true,
				'tag'                 => false,
				'show_read_more_link' => false,
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
			// Prepare data for template
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
			$instance['read_more_text'] = empty( $instance['read_more_text'] ) ? esc_html__( 'Read more', 'proteuswidgets' ) : $instance['read_more_text'];
			$thumbnail_size             = 'inline' === $instance['layout'] ? 'pw-inline' : 'pw-page-box';

			// Get basic page info
			$page = get_post( $page_id, ARRAY_A );

			// Prepare the excerpt text
			$excerpt = wp_strip_all_tags( ! empty( $page['post_excerpt'] ) ? $page['post_excerpt'] : $page['post_content'] );

			if ( 'inline' === $instance['layout'] && strlen( $excerpt ) > $this->excerpt_lengths['inline_excerpt'] ) {
				$strpos  = strpos( $excerpt , ' ', $this->excerpt_lengths['inline_excerpt'] );
				$excerpt = ( false !== $strpos ) ? substr( $excerpt, 0, $strpos ) . ' &hellip;' : $excerpt;
			}
			elseif ( strlen( $excerpt ) > $this->excerpt_lengths['block_excerpt'] ) {
				$strpos  = strpos( $excerpt , ' ', $this->excerpt_lengths['block_excerpt'] );
				$excerpt = ( false !== $strpos ) ? substr( $excerpt, 0, $strpos ) . ' &hellip;' : $excerpt;
			}

			$page['post_excerpt'] = $excerpt;
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

			// widget-featured-page template rendering
			echo $this->template_engine->render_template( apply_filters( 'pw/widget_featured_page_view', 'widget-featured-page' ), array(
				'args'           => $args,
				'page'           => $page,
				'instance'       => $instance,
				'thumbnail_size' => $thumbnail_size,
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

			if ( $this->fields['tag'] ) {
				$instance['tag'] = sanitize_text_field( $new_instance['tag'] );
			}

			if ( $this->fields['show_read_more_link'] ) {
				$instance['show_read_more_link'] = empty( $new_instance['show_read_more_link'] ) ? '' : sanitize_key( $new_instance['show_read_more_link'] );
			}

			return $instance;
		}

		/**
		 * Back-end widget form.
		 *
		 * @param array $instance The widget options
		 */
		public function form( $instance ) {
			$page_id             = empty( $instance['page_id'] ) ? 0 : (int) $instance['page_id'];
			$layout              = empty( $instance['layout'] ) ? '' : $instance['layout'];
			$read_more_text      = empty( $instance['read_more_text'] ) ? esc_html__( 'Read more', 'proteuswidgets' ) : $instance['read_more_text'];
			$tag                 = empty( $instance['tag'] ) ? '' : $instance['tag'];
			$show_read_more_link = empty( $instance['show_read_more_link'] ) ? '' : $instance['show_read_more_link'];

			?>

			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'page_id' ) ); ?>"><?php esc_html_e( 'Page:', 'proteuswidgets' ); ?></label> <br>
				<?php
					wp_dropdown_pages( array(
						'selected' => $page_id,
						'name'     => $this->get_field_name( 'page_id' ),
						'id'       => $this->get_field_id( 'page_id' ),
					) );
				?>
			</p>

			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'layout' ) ); ?>"><?php esc_html_e( 'Layout:', 'proteuswidgets' ); ?></label> <br>
				<select class="js-featured-page-settings__select-layout" id="<?php echo esc_attr( $this->get_field_id( 'layout' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'layout' ) ); ?>">
					<option value="block" <?php selected( $layout, 'block' ); ?>><?php esc_html_e( 'With big picture', 'proteuswidgets' ); ?></option>
					<option value="inline" <?php selected( $layout, 'inline' ); ?>><?php esc_html_e( 'With small picture, inline', 'proteuswidgets' ); ?></option>
				</select>
			</p>

			<div class="js-featured-page-settings__additional-block-settings">
				<?php if ( $this->fields['show_read_more_link'] ) : ?>
				<p>
					<input class="checkbox" type="checkbox" <?php checked( $show_read_more_link, 'on' ); ?> id="<?php echo esc_attr( $this->get_field_id( 'show_read_more_link' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'show_read_more_link' ) ); ?>" value="on" />
					<label for="<?php echo esc_attr( $this->get_field_id( 'show_read_more_link' ) ); ?>"><?php esc_html_e( 'Show read more link?', 'proteuswidgets' ); ?></label>
				</p>
				<?php endif; ?>

				<?php if ( $this->fields['read_more_text'] ) : ?>
				<p>
					<label for="<?php echo esc_attr( $this->get_field_id( 'read_more_text' ) ); ?>"><?php esc_html_e( 'Read more text:', 'proteuswidgets' ); ?></label> <br>
					<input id="<?php echo esc_attr( $this->get_field_id( 'read_more_text' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'read_more_text' ) ); ?>" type="text" value="<?php echo esc_attr( $read_more_text ); ?>" />
				</p>
				<?php endif; ?>

				<?php if ( $this->fields['tag'] ) : ?>
				<p>
					<label for="<?php echo esc_attr( $this->get_field_id( 'tag' ) ); ?>"><?php esc_html_e( 'Tag:', 'proteuswidgets' ); ?></label> <br>
					<input id="<?php echo esc_attr( $this->get_field_id( 'tag' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'tag' ) ); ?>" type="text" value="<?php echo esc_attr( $tag ); ?>" />
				</p>
				<?php endif; ?>
			</div>

			<small><?php printf( esc_html__( 'If you want to edit the image and text of this widget, please %sread this article%s.', 'proteuswidgets' ), '<a href="https://support.proteusthemes.com/hc/en-us/articles/207428479-How-do-I-change-image-and-text-of-the-Featured-Page-widget-" target="_blank">', '</a>' ); ?></small>

			<script type="text/javascript">
				(function( $ ) {
					if ( 'inline' === '<?php echo esc_attr( $layout ); ?>' ) {
						$( '.js-featured-page-settings__additional-block-settings' ).hide();
					}
				})( jQuery );
			</script>

			<?php
		}
	}
}
