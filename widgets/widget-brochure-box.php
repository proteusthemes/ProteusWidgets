<?php
/**
 * Brochure Box Widget
 *
 * @package ProteusWidgets
 * @since 0.1.0
 */

if ( ! class_exists( 'PW_Brochure_Box' ) ) {
	class PW_Brochure_Box extends WP_Widget {

		/**
		 * Register widget with WordPress.
		 */
		public function __construct() {
			parent::__construct(
				false, // ID, auto generate when false
				_x( 'ProteusThemes: Brochure Box' , 'backend', 'proteuswidgets'), // Name
				array(
					'description' => _x( 'Brochure Box for the Sidebar and Page Builder.', 'backend', 'proteuswidgets'),
					'classname'   => 'widget-brochure-box',
				)
			);
		}

		/**
		 * Enqueue the JS and CSS files with the right action
		 * @see admin_enqueue_scripts
		 * @return void
		 */
		public static function enqueue_js_css() {
			wp_enqueue_style( 'fontawesome-icons', get_template_directory_uri() . '/bower_components/fontawesome/css/font-awesome.min.css' );
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
			echo $args['before_widget'];
			if ( ! empty ( $instance['title'] ) ) :
			?>

			<h4 class="sidebar__headings"><?php echo $instance['title']; ?></h4>
			<?php
				else :
					endif;
			?>
			<a class="brochure-box" href="<?php echo $instance['brochure_url']; ?>" <?php echo empty ( $instance['new_tab'] ) ? '' : 'target="_blank"'; ?>>
				<i class="fa  <?php echo $instance['brochure_icon']; ?>"></i>
				<h5 class="brochure-box__text"><?php echo $instance['brochure_text']; ?></h5>
			</a>

			<?php
			echo $args['after_widget'];
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

			$instance['title']         = wp_kses_post( $new_instance['title'] );
			$instance['brochure_url']  = esc_url( $new_instance['brochure_url'] );
			$instance['new_tab']       = sanitize_key( $new_instance['new_tab'] );
			$instance['brochure_text'] = wp_kses_post( $new_instance['brochure_text'] );
			$instance['brochure_icon'] = sanitize_key( $new_instance['brochure_icon'] );

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
			$title         = empty( $instance['title'] ) ? '' : $instance['title'];
			$brochure_url  = empty( $instance['brochure_url'] ) ? '' : $instance['brochure_url'];
			$new_tab       = empty( $instance['new_tab'] ) ? '' : $instance['new_tab'];
			$brochure_text = empty( $instance['brochure_text'] ) ? '' : $instance['brochure_text'];
			$brochure_icon = empty( $instance['brochure_icon'] ) ? '' : $instance['brochure_icon'];

			?>

			<p>
				<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _ex( 'Title', 'backend', 'proteuswidgets'); ?>:</label>
				<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
			</p>

			<p>
				<label for="<?php echo $this->get_field_id( 'brochure_url' ); ?>"><?php _ex( 'Brochure URL', 'backend', 'proteuswidgets'); ?>:</label> <br />
				<input class="widefat" id="<?php echo $this->get_field_id( 'brochure_url' ); ?>" name="<?php echo $this->get_field_name( 'brochure_url' ); ?>" type="text" value="<?php echo $brochure_url; ?>" />
			</p>

			<p>
				<input class="checkbox" type="checkbox" <?php checked( $new_tab, 'on' ); ?> id="<?php echo $this->get_field_id( 'new_tab' ); ?>" name="<?php echo $this->get_field_name( 'new_tab' ); ?>" value="on" />
				<label for="<?php echo $this->get_field_id( 'new_tab' ); ?>"><?php _ex('Open link in new tab', 'backend', 'proteuswidgets'); ?></label>
			</p>

			<p>
				<label for="<?php echo $this->get_field_id( 'brochure_text' ); ?>"><?php _ex( 'Brochure Text', 'backend', 'proteuswidgets'); ?>:</label>
				<input class="widefat" id="<?php echo $this->get_field_id( 'brochure_text' ); ?>" name="<?php echo $this->get_field_name( 'brochure_text' ); ?>" type="text" value="<?php echo esc_attr( $brochure_text ); ?>" />
			</p>

			<p>
				<label for="<?php echo $this->get_field_id( 'brochure_icon' ); ?>"><?php _ex( 'Brochure Icon', 'backend', 'proteuswidgets'); ?>:</label> <br />
				<small><?php printf( _x( 'Click on the icon below or manually select from the %s website', 'backend', 'proteuswidgets'), '<a href="http://fontawesome.io/icons/" target="_blank">FontAwesome</a>' ); ?>.</small>
				<input id="<?php echo $this->get_field_id( 'brochure_icon' ); ?>" name="<?php echo $this->get_field_name( 'brochure_icon' ); ?>" type="text" value="<?php echo $brochure_icon; ?>" class="widefat  js-icon-input" /> <br><br>
				<a class="js-selectable-icon  icon-widget" href="#" data-iconname="fa-file-o"><i class="fa fa-lg fa-file-o"></i></a>
				<a class="js-selectable-icon  icon-widget" href="#" data-iconname="fa-file-pdf-o"><i class="fa fa-lg fa-file-pdf-o"></i></a>
				<a class="js-selectable-icon  icon-widget" href="#" data-iconname="fa-file-word-o"><i class="fa fa-lg fa-file-word-o"></i></a>
				<a class="js-selectable-icon  icon-widget" href="#" data-iconname="fa-file-text-o"><i class="fa fa-lg fa-file-text-o"></i></a>
				<a class="js-selectable-icon  icon-widget" href="#" data-iconname="fa-file-image-o"><i class="fa fa-lg fa-file-image-o"></i></a>
				<a class="js-selectable-icon  icon-widget" href="#" data-iconname="fa-file-powerpoint-o"><i class="fa fa-lg fa-file-powerpoint-o"></i></a>
				<a class="js-selectable-icon  icon-widget" href="#" data-iconname="fa-file-excel-o"><i class="fa fa-lg fa-file-excel-o"></i></a>
				<a class="js-selectable-icon  icon-widget" href="#" data-iconname="fa-file-audio-o"><i class="fa fa-lg fa-file-audio-o"></i></a>
				<a class="js-selectable-icon  icon-widget" href="#" data-iconname="fa-file-video-o"><i class="fa fa-lg fa-file-video-o"></i></a>
				<a class="js-selectable-icon  icon-widget" href="#" data-iconname="fa-file-archive-o"><i class="fa fa-lg fa-file-archive-o"></i></a>
				<a class="js-selectable-icon  icon-widget" href="#" data-iconname="fa-file-code-o"><i class="fa fa-lg fa-file-code-o"></i></a>
				<a class="js-selectable-icon  icon-widget" href="#" data-iconname="fa-save"><i class="fa fa-lg fa-save"></i></a>
				<a class="js-selectable-icon  icon-widget" href="#" data-iconname="fa-download"><i class="fa fa-lg fa-download"></i></a>
				<a class="js-selectable-icon  icon-widget" href="#" data-iconname="fa-print"><i class="fa fa-lg fa-print"></i></a>
				<a class="js-selectable-icon  icon-widget" href="#" data-iconname="fa-info-circle"><i class="fa fa-lg fa-info-circle"></i></a>
				<a class="js-selectable-icon  icon-widget" href="#" data-iconname="fa-question-circle"><i class="fa fa-lg fa-question-circle"></i></a>
				<a class="js-selectable-icon  icon-widget" href="#" data-iconname="fa-cog"><i class="fa fa-lg fa-cog"></i></a>
				<a class="js-selectable-icon  icon-widget" href="#" data-iconname="fa-link"><i class="fa fa-lg fa-link"></i></a>
			</p>

			<?php
		}

	} // Class PW_Brochure_Box
	add_action( 'widgets_init', create_function( '', 'register_widget( "PW_Brochure_Box" );' ) );
	add_action( 'admin_enqueue_scripts', array( 'PW_Brochure_Box', 'enqueue_js_css' ), 20 );
}