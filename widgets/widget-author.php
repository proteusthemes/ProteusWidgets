<?php
/**
 * Author box widget
 *
 * @package ProteusWidgets
 * @since 1.0.0
 */


if ( ! class_exists( 'PW_Author' ) ) {
	class PW_Author extends PW_Widget {

		// Basic widget settings
		function widget_id_base() { return 'author'; }
		function widget_name() { return esc_html__( 'Author', 'proteuswidgets' ); }
		function widget_description() { return esc_html__( 'Displays author details with a gravatar photo.', 'proteuswidgets' ); }
		function widget_class() { return 'widget-author'; }

		/**
		 * Register widget with WordPress.
		 */
		public function __construct() {
			parent::__construct();
		}

		/**
		 * Front-end display of widget.
		 */
		public function widget( $args, $instance ) {
			// Prepare data for mustache template
			$selected_user_id = intval( $instance['selected_user_id'] );
			$social_icons     = array();

			if ( is_callable( 'PW_Functions::get_social_icons_links' ) ) {
				$icons = PW_Functions::get_social_icons_links( get_user_meta( $selected_user_id ) );
				foreach ( $icons as $service => $url ) {
					$service_icon = substr( $service, 3 );
					array_push( $social_icons, array( 'icon' => $service_icon, 'url' => $url[0] ) );
				}
			}

			// Mustache author-widget template rendering
			echo $this->mustache->render( apply_filters( 'pw/widget_author_view', 'widget-author' ), array(
				'args'                    => $args,
				'author-avatar'           => get_avatar( $selected_user_id, 90 ),
				'author-posts'            => get_author_posts_url( $selected_user_id ),
				'author-meta-name'        => $args['before_title'] . get_the_author_meta( 'display_name', $selected_user_id ) . $args['after_title'],
				'author-meta-description' => wpautop( get_the_author_meta( 'description', $selected_user_id ) ),
				'author-meta-user-url'    => get_the_author_meta( 'user_url', $selected_user_id ),
				'social-icons'            => intval( count( $social_icons ) ),
				'social-icons-list'       => $social_icons,
			));

		}

		/**
		 * Sanitize widget form values as they are saved.
		 */
		public function update( $new_instance, $old_instance ) {
			$instance = array();

			$instance['selected_user_id'] = intval( $new_instance['selected_user_id'] );

			return $instance;
		}

		/**
		 * Back-end widget form.
		 */
		public function form( $instance ) {
			$selected_user_id = isset( $instance['selected_user_id'] ) ? $instance['selected_user_id'] : 1;

			?>

			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'selected_user_id' ) ); ?>"><?php esc_html_e( 'Display author:', 'proteuswidgets' ); ?></label>
				<?php wp_dropdown_users( array(
					'name'     => $this->get_field_name( 'selected_user_id' ),
					'id'       => $this->get_field_id( 'selected_user_id' ),
					'selected' => $selected_user_id,
					'class'    => 'widefat',
				) ); ?>

			</p>

			<p><small><?php printf( esc_html__( 'To add the social icons to this widget, please install the %sExtra User Details%s plugin and fill in the details in the &quot;Users&quot; section.', 'proteuswidgets' ), '<a href="https://wordpress.org/plugins/extra-user-details/" target="_blank">', '</a>' ); ?></small></p>

			<?php

		}

	}
}