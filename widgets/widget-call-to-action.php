<?php
/**
 * Call to Action Widget
 *
 * @package ProteusWidgets
 * @since 1.1
 */

if ( ! class_exists( 'PW_Call_To_Action' ) ) {
	class PW_Call_To_Action extends PW_Widget {

		// Basic widget settings
		function widget_id_base() { return 'call_to_action'; }
		function widget_name() { return __( 'Call to Action', 'proteuswidgets' ); }
		function widget_description() { return __( 'Call to Action widget for Page Builder.', 'proteuswidgets' ); }
		function widget_class() { return 'widget-call-to-action'; }

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
			$instance['text']  = do_shortcode( $instance['text'] );
			$instance['button_text'] = do_shortcode( $instance['button_text'] );

			// Mustache widget-call-to-action template rendering
			echo $this->mustache->render( apply_filters( 'pw/widget_call_to_action_view', 'widget-call-to-action' ), array(
				'args'        => $args,
				'instance'    => $instance,
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

			$instance['text'] = wp_kses_post( $new_instance['text'] );
			$instance['button_text'] = wp_kses_post( $new_instance['button_text'] );

			return $instance;
		}

		/**
		 * Back-end widget form.
		 *
		 * @param array $instance The widget options
		 */
		public function form( $instance ) {
			$text = ! empty( $instance['text'] ) ? $instance['text'] : '';
			$button_text = ! empty( $instance['button_text'] ) ? $instance['button_text'] : '';
			?>

			<p>
				<label for="<?php echo $this->get_field_id( 'text' ); ?>"><?php _ex( 'Text:', 'backend', 'proteuswidgets' ); ?></label>
				<input class="widefat" id="<?php echo $this->get_field_id( 'text' ); ?>" name="<?php echo $this->get_field_name( 'text' ); ?>" type="text" value="<?php echo esc_attr( $text ); ?>" />
			</p>

			<p>
				<label for="<?php echo $this->get_field_id( 'button_text' ); ?>"><?php _ex( 'Button Area:', 'backend', 'proteuswidgets' ); ?></label>
				<input class="widefat" id="<?php echo $this->get_field_id( 'button_text' ); ?>" name="<?php echo $this->get_field_name( 'button_text' ); ?>" type="text" value="<?php echo esc_attr( $button_text ); ?>" /><br><br>
				<span class="button-shortcodes">
					For adding buttons you must use button shortcode which looks like that: <b>[button]Text[/button]</b>.<br>
					There is more option with different attributes - <b>text</b>, <b>style</b>, <b>href</b>, <b>target</b>.<br>
					<b>Text</b>: You can change the text of the button. Example: <b>[button]New Text[/button]</b>.<br>
					<b>Style</b>: You can choose betwen few styles - <b>primary</b>, <b>default</b>, <b>success</b>, <b>info</b>, <b>warning</b> or <b>danger</b>. Example: <b>[button style="default"]Text[/button]</b>.<br>
					<b>Href</b>: You can add any URL to the button. Example: <b>[button href="http://www.proteusthemes.com"]Text[/button]</b>.<br>
					<b>Target</b>: You can choose if you want to open link in same (<b>_self</b>) or new (<b>_blank</b>) window. Example: <b>[button target="_blank"]Text[/button]</b>.<br>
				</span>
			</p>

			<?php
		}

	} // Class PW_Call_To_Action
	register_widget( 'PW_Call_To_Action' );
}