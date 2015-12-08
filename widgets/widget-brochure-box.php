<?php
/**
 * Brochure Box Widget
 *
 * @package ProteusWidgets
 * @since 1.0.0
 */


if ( ! class_exists( 'PW_Brochure_Box' ) ) {
	class PW_Brochure_Box extends PW_Widget {

		/**
		 * Register widget with WordPress.
		 */
		public function __construct() {

			// Overwrite the widget variables of the parent class
			$this->widget_id_base     = 'brochure_box';
			$this->widget_name        = esc_html__( 'Brochure Box', 'proteuswidgets' );
			$this->widget_description = esc_html__( 'Widget for displaying downloadable files.', 'proteuswidgets' );
			$this->widget_class       = 'widget-brochure-box';

			parent::__construct();
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
			$instance['preped_title'] = apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base );

			// widget-brochure-box template rendering
			echo $this->template_engine->render_template( apply_filters( 'pw/widget_brochure_box_view', 'widget-brochure-box' ), array(
				'args'        => $args,
				'instance'    => $instance,
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

			$instance['title']         = wp_kses_post( $new_instance['title'] );
			$instance['brochure_url']  = esc_url_raw( $new_instance['brochure_url'] );
			$instance['new_tab']       = ! empty ( $new_instance['new_tab'] ) ? sanitize_key( $new_instance['new_tab'] ) : '';
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
				<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title:', 'proteuswidgets' ); ?></label>
				<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
			</p>

			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'brochure_url' ) ); ?>"><?php esc_html_e( 'File URL:', 'proteuswidgets' ); ?></label> <br />
				<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'brochure_url' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'brochure_url' ) ); ?>" type="text" value="<?php echo esc_url( $brochure_url ); ?>" />
				<input type="button" onclick="ProteusWidgetsUploader.fileUploader.openFileFrame('<?php echo esc_attr( $this->get_field_id( 'brochure_url' ) ); ?>');" class="upload-brochure-file button button-secondary pull-right" value="<?php esc_html_e( 'Upload file', 'proteuswidgets' ); ?>" /> <!-- Media uploader button -->
			</p>

			<p>
				<input class="checkbox" type="checkbox" <?php checked( $new_tab, 'on' ); ?> id="<?php echo esc_attr( $this->get_field_id( 'new_tab' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'new_tab' ) ); ?>" value="on" />
				<label for="<?php echo esc_attr( $this->get_field_id( 'new_tab' ) ); ?>"><?php esc_html_e( 'Open link in new tab', 'proteuswidgets' ); ?></label>
			</p>

			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'brochure_text' ) ); ?>"><?php esc_html_e( 'Brochure text:', 'proteuswidgets' ); ?></label>
				<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'brochure_text' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'brochure_text' ) ); ?>" type="text" value="<?php echo esc_attr( $brochure_text ); ?>" />
			</p>

			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'brochure_icon' ) ); ?>"><?php esc_html_e( 'Brochure icon:', 'proteuswidgets' ); ?></label> <br />
				<small><?php printf( esc_html__( 'Click on the icon below or manually select from the %s website.', 'proteuswidgets' ), '<a href="http://fontawesome.io/icons/" target="_blank">FontAwesome</a>' ); ?></small>
				<input id="<?php echo esc_attr( $this->get_field_id( 'brochure_icon' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'brochure_icon' ) ); ?>" type="text" value="<?php echo esc_attr( $brochure_icon ); ?>" class="widefat  js-icon-input" /> <br><br>
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

	}
}