<?php
/**
 * Icon Box Widget
 *
 * @package ProteusWidgets
 * @since 1.0.0
 */


if ( ! class_exists( 'PW_Icon_Box' ) ) {
	class PW_Icon_Box extends PW_Widget {

		private $fields, $font_awesome_icons_list;

		/**
		 * Register widget with WordPress.
		 */
		public function __construct() {

			// Overwrite the widget variables of the parent class
			$this->widget_id_base     = 'icon_box';
			$this->widget_name        = esc_html__( 'Icon Box', 'proteuswidgets' );
			$this->widget_description = esc_html__( 'Linkable block with title, text and font awesome icon.', 'proteuswidgets' );
			$this->widget_class       = 'widget-icon-box';

			parent::__construct();

			// Get the settings for the icon box widgets
			$this->fields = apply_filters( 'pw/icon_box_widget', array(
				'featured_setting' => false,
			) );

			// A list of icons to choose from in the widget backend.
			$this->font_awesome_icons_list = apply_filters(
				'pw/icon_box_fa_icons_list',
				array(
					'fas fa-home',
					'fas fa-phone',
					'fas fa-clock',
					'fas fa-camera-retro',
					'fas fa-check-circle',
					'fas fa-cog',
					'fas fa-cogs',
					'fas fa-comments',
					'fas fa-compass',
					'fas fa-download',
					'fas fa-exclamation-circle',
					'fas fa-male',
					'fas fa-female',
					'fas fa-fire',
					'fas fa-flag',
					'fas fa-folder-open',
					'fas fa-heart',
					'fas fa-inbox',
					'fas fa-info-circle',
					'fas fa-key',
					'fas fa-laptop',
					'fas fa-leaf',
					'fas fa-map-marker',
					'fas fa-dollar-sign',
					'fas fa-plus-circle',
					'fas fa-print',
					'fas fa-quote-right',
					'fas fa-quote-left',
					'fas fa-shopping-cart',
					'fas fa-sitemap',
					'fas fa-star',
					'fas fa-suitcase',
					'fas fa-thumbs-up',
					'fas fa-tint',
					'fas fa-truck',
					'fas fa-users',
					'fas fa-exclamation',
					'fas fa-wrench',
					'fas fa-chevron-right',
					'fas fa-chevron-circle-right',
					'fas fa-chevron-down',
					'fas fa-chevron-circle-down',
				)
			);
		}

		/**
		 * Front-end display of widget.
		 *
		 * @see WP_Widget::widget()
		 *
		 * @param array $args     Widget arguments.
		 * @param array $instance Saved values from database.
		 */
		public function widget( $args, $instance ) {
			// Prepare data for template
			$instance['target']   = ! empty ( $instance['new_tab'] ) ? '_blank' : '_self';

			// widget-icon-box template rendering
			echo $this->template_engine->render_template( apply_filters( 'pw/widget_icon_box_view', 'widget-icon-box' ), array(
				'args'     => $args,
				'instance' => $instance,
			));
		}

		/**
		 * Sanitize widget form values as they are saved.
		 *
		 * @see WP_Widget::update()
		 *
		 * @param array $new_instance Values just sent to be saved.
		 * @param array $old_instance Previously saved values from database.
		 *
		 * @return array Updated safe values to be saved.
		 */
		public function update( $new_instance, $old_instance ) {
			$instance = array();

			$instance['title']    = wp_kses_post( $new_instance['title'] );
			$instance['text']     = wp_kses_post( $new_instance['text'] );
			$instance['btn_link'] = esc_url_raw( $new_instance['btn_link'] );
			$instance['icon']     = sanitize_text_field( $new_instance['icon'] );
			$instance['new_tab']  = ! empty ( $new_instance['new_tab'] ) ? sanitize_key( $new_instance['new_tab'] ) : '';

			if ( $this->fields['featured_setting'] ) {
				$instance['featured'] = ! empty ( $new_instance['featured'] ) ? sanitize_key( $new_instance['featured'] ) : '';
			}

			return $instance;
		}

		/**
		 * Back-end widget form.
		 *
		 * @see WP_Widget::form()
		 *
		 * @param array $instance Previously saved values from database.
		 */
		public function form( $instance ) {
			$title    = empty( $instance['title'] ) ? '' : $instance['title'];
			$text     = empty( $instance['text'] ) ? '' : $instance['text'];
			$btn_link = empty( $instance['btn_link'] ) ? '' : $instance['btn_link'];
			$icon     = empty( $instance['icon'] ) ? '' : $instance['icon'];
			$new_tab  = empty( $instance['new_tab'] ) ? '' : $instance['new_tab'];

			if ( $this->fields['featured_setting'] ) {
				$instance['featured'] = empty ( $instance['featured'] ) ? '' : $instance['featured'];
			}

			?>

			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title:', 'proteuswidgets' ); ?></label>
				<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
			</p>

			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'text' ) ); ?>"><?php esc_html_e( 'Text:', 'proteuswidgets' ); ?></label> <br />
				<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'text' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'text' ) ); ?>" type="text" value="<?php echo esc_attr( $text ); ?>" />
			</p>

			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'btn_link' ) ); ?>"><?php esc_html_e( 'Link:', 'proteuswidgets' ); ?></label> <br />
				<small><?php esc_html_e( 'URL to any page, optional.', 'proteuswidgets' ); ?></small> <br>
				<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'btn_link' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'btn_link' ) ); ?>" type="text" value="<?php echo esc_url( $btn_link ); ?>" />
			</p>

			<p>
				<input class="checkbox" type="checkbox" <?php checked( $new_tab, 'on' ); ?> id="<?php echo esc_attr( $this->get_field_id( 'new_tab' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'new_tab' ) ); ?>" value="on" />
				<label for="<?php echo esc_attr( $this->get_field_id( 'new_tab' ) ); ?>"><?php esc_html_e( 'Open link in new tab', 'proteuswidgets' ); ?></label>
			</p>

			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'icon' ) ); ?>"><?php esc_html_e( 'Icon:', 'proteuswidgets' ); ?></label> <br />
				<small><?php echo wp_kses_post( apply_filters( 'pw/icons_input_field_notice', sprintf( esc_html__( 'Click on the icon below or manually select from the %s website.', 'proteuswidgets' ), '<a href="http://fontawesome.io/icons/" target="_blank">FontAwesome</a>' ) ) ); ?></small>
				<input id="<?php echo esc_attr( $this->get_field_id( 'icon' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'icon' ) ); ?>" type="text" value="<?php echo esc_attr( $icon ); ?>" class="widefat  js-icon-input" /> <br><br>
				<?php foreach ( $this->font_awesome_icons_list as $icon ) : ?>
					<a class="js-selectable-icon  icon-widget" href="#" data-iconname="<?php echo esc_attr( PW_Functions::get_full_fa_class( $icon ) ); ?>"><i class="<?php echo esc_html( PW_Functions::get_full_fa_class( $icon ) ) ?> fa-lg"></i></a>
				<?php endforeach; ?>
			</p>

			<?php if ( $this->fields['featured_setting'] ) : ?>
				<p>
					<input class="checkbox" type="checkbox" <?php checked( $instance['featured'], 'on' ); ?> id="<?php echo esc_attr( $this->get_field_id( 'featured' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'featured' ) ); ?>" value="on" />
					<label for="<?php echo esc_attr( $this->get_field_id( 'featured' ) ); ?>"><?php esc_html_e( 'Highlight this widget.', 'proteuswidgets' ); ?></label>
				</p>
			<?php endif; ?>

			<?php
		}

	}
}