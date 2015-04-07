<?php
/**
 * Featured Page Widget
 *
 * @package ProteusWidgets
 * @since 1.0.0
 */

if ( ! class_exists( 'PW_Featured_Page' ) ) {
	class PW_Featured_Page extends Pw_Widget {

		/**
		 * Length of the line excerpt.
		 */
		const INLINE_EXCERPT = 60;
		const BLOCK_EXCERPT = 240;

		// Basic widget settings
		function widget_id_base() { return 'featured_page'; }
		function widget_name() { return __( 'Featured Page', 'proteuswidgets' ); }
		function widget_description() { return __( 'Featured Page widget for the Sidebar and Page Builder.', 'proteuswidgets' ); }
		function widget_class() { return 'widget-featured-page'; }

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
			// Prepare data for mustache template
			$page_id            = absint( $instance['page_id'] );
			$instance['layout'] = sanitize_key( $instance['layout'] );
			$thumbnail_size     = 'inline' === $instance['layout'] ? 'thumbnail' : 'page-box';

			// Get basic page info
			if ( $page_id ) {
				$page = (array) get_post( $page_id );
			}

			// Prepare the excerpt text
			$excerpt = ! empty( $page['post_excerpt'] ) ? $page['post_excerpt'] : $page['post_content'];

			if ( 'inline' === $instance['layout'] && strlen( $excerpt ) > self::INLINE_EXCERPT ) {
				$excerpt = substr( $excerpt, 0, strpos( $excerpt , ' ', self::INLINE_EXCERPT ) ) . ' &hellip;';
			}
			elseif ( strlen( $excerpt ) > self::BLOCK_EXCERPT ) {
				$excerpt = substr( $excerpt, 0, strpos( $excerpt , ' ', self::BLOCK_EXCERPT ) ) . ' &hellip;';
			}

			$page['post_excerpt'] = sanitize_text_field( $excerpt );
			$page['link']         = get_permalink( $page_id );
			$page['thumbnail']    = get_the_post_thumbnail( $page_id, $thumbnail_size );

			// Mustache widget-featured-page template rendering
			echo $this->mustache->render( apply_filters( 'pw/widget_featured_page_view', 'widget-featured-page' ), array(
				'args'      => $args,
				'page'      => $page,
				'instance'  => $instance,
				'block'     => 'block' === $instance['layout'],
				'read-more' => __( 'Read more', 'proteuswidgets' ),

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

			<?php
		}
	}
	register_widget( 'PW_Featured_Page' );
}