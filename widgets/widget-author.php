<?php
/**
 * Author box widget
 *
 * @package Restaurant
 */


if ( ! class_exists( 'PW_Author' ) ) {
	class PW_Author extends WP_Widget {

		/**
		 * Register widget with WordPress.
		 */
		public function __construct() {
			parent::__construct(
				false, // ID, auto generate when false
				sprintf( 'ProteusThemes: %s', __( 'Author Widget', 'proteuswidgets' ) ),
				array(
					'classname' => 'widget-author'
				)
			);
		}

		/**
		 * Front-end display of widget.
		 */
		public function widget( $args, $instance ) {
			extract( $args );
			$selected_user_id = intval( $instance['selected_user_id'] );

			echo $before_widget;
			?>
				<div class="widget-author__image-container">
					<div class="widget-author__avatar--blurred">
						<?php echo get_avatar( $selected_user_id, 90 ); ?>
					</div>
					<a href="<?php echo get_author_posts_url( $selected_user_id ); ?>" class="widget-author__avatar">
						<?php echo get_avatar( $selected_user_id, 90 ); ?>
					</a>
				</div>
				<div class="row widget-author__content">
					<div class="col-xs-10  col-xs-offset-1">
						<?php echo $before_title; ?><?php the_author_meta( 'display_name', $selected_user_id ); ?><?php echo $after_title; ?>
						<?php echo wpautop( get_the_author_meta( 'description', $selected_user_id ) ); ?>

						<?php if ( strlen( get_the_author_meta( 'user_url', $selected_user_id) ) ) : ?>
						<p>
							<a href="<?php esc_url( the_author_meta( 'user_url', $selected_user_id ) ); ?>"><?php the_author_meta( 'user_url', $selected_user_id ); ?></a>
						</p>
						<?php endif ?>
						<?php
							if ( is_callable( 'PWFunctions::get_social_icons_links' ) ) {
								$icons = PWFunctions::get_social_icons_links( get_user_meta( $selected_user_id ) );
								if ( count( $icons ) ) {
									echo '<p class="social-icons__author">';
								}
								foreach ( $icons as $service => $url ) {
									$service_icon = substr( $service, 3 );
									printf( '<a href="%s" class="social-icons__container"><i class="fa fa-%s"></i></a>', esc_url( $url[0] ), sanitize_key( $service_icon ) );
								}
								if ( count( $icons ) ) {
									echo '</p>';
								}
							}
						?>
					</div>
				</div>

			<?php
			echo $after_widget;
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
				<label for="<?php echo $this->get_field_id( 'selected_user_id' ); ?>"><?php _ex( 'Display author: ', 'readable_wp'); ?></label>
				<?php wp_dropdown_users( array(
					'name'     => $this->get_field_name( 'selected_user_id' ),
					'id'       => $this->get_field_id( 'selected_user_id' ),
					'selected' => $selected_user_id,
					'class'    => 'widefat',
				) ); ?>

			</p>

			<p><small><?php printf( __( 'To add the social icons to this widget, please install the %sExtra User Details%s plugin and fill in the details in the &quot;Users&quot; section.', 'proteuswidgets' ), '<a href="https://wordpress.org/plugins/extra-user-details/" target="_blank">', '</a>' ); ?></small></p>

			<?php


		}

	}
	register_widget( 'PW_Author' );
}