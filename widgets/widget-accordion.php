<?php
/**
 * Accordion Widget
 *
 * @package ProteusWidgets
 * @since 2.4.0
 */

if ( ! class_exists( 'PW_Accordion' ) ) {
	class PW_Accordion extends PW_Widget {

		// Basic widget settings
		function widget_id_base() { return 'accordion'; }
		function widget_name() { return esc_html__( 'Accordion', 'proteuswidgets' ); }
		function widget_description() { return ''; }
		function widget_class() { return 'widget-accordion'; }

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
			$items = isset( $instance['items'] ) ? array_values( $instance['items'] ) : array();
			$instance['title_is_set'] = ! empty( $instance['title'] );
			$instance['preped_title'] = esc_html( apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base ) );
			$instance['read_more_link'] = esc_url( $instance['read_more_link'] );

			foreach ( $items as $key => $item ) {
				$items[ $key ]['key']     = esc_attr( $key );
				$items[ $key ]['title']   = wp_kses( $item['title'], array() );
				$items[ $key ]['content'] = wp_kses_post( $item['content'] );
			}

			// Unique ID for this accordion
			$instance['uniqid'] = sanitize_html_class( $args['widget_id'] );

			$text = array(
				'read_more' => esc_html__( 'Read more', 'proteuswidgets' ),
			);

			// Mustache widget-accordion template rendering
			echo $this->mustache->render( apply_filters( 'pw/widget_accordion_view', 'widget-accordion' ), array(
				'args'     => $args,
				'instance' => $instance,
				'items'    => $items,
				'text'     => $text,
			) );
		}

		/**
		 * Sanitize widget form values as they are saved.
		 *
		 * @param array $new_instance The new options
		 * @param array $old_instance The previous options
		 */
		public function update( $new_instance, $old_instance ) {
			$instance = array();

			foreach ( $new_instance['items'] as $key => $item ) {
				$instance['items'][ $key ]['id']      = sanitize_key( $item['id'] );
				$instance['items'][ $key ]['title']   = sanitize_text_field( $item['title'] );
				$instance['items'][ $key ]['content'] = wp_kses_post( $item['content'] );
			}

			$instance['title']          = sanitize_text_field( $new_instance['title'] );
			$instance['read_more_link'] = esc_url_raw( $new_instance['read_more_link'] );

			return $instance;
		}

		/**
		 * Back-end widget form.
		 *
		 * @param array $instance The widget options
		 */
		public function form( $instance ) {
			if ( ! isset( $instance['items'] ) ) {
				$instance['items'] = array(
					array(
						'id'      => 1,
						'title'   => '',
						'content' => '',
					),
				);
			}

			$title          = empty( $instance['title'] ) ? '' : $instance['title'];
			$read_more_link = empty( $instance['read_more_link'] ) ? '' : $instance['read_more_link'];

			// Page Builder fix when using repeating fields
			if ( 'temp' === $this->id ) {
				$this->current_widget_id = $this->number;
			}
			else {
				$this->current_widget_id = $this->id;
			}

		?>

			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php _e( 'Title:', 'proteuswidgets' ); ?></label>
				<input class="widefat" type="text" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" value="<?php echo esc_attr( $title ); ?>" />
			</p>

			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'read_more_link' ) ); ?>"><?php _e( 'Read more URL:','proteuswidgets' ); ?></label>
				<input class="widefat" type="text" id="<?php echo esc_attr( $this->get_field_id( 'read_more_link' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'read_more_link' ) ); ?>" value="<?php echo esc_attr( $read_more_link ); ?>" />
				<br>
				<small><?php _e( 'If you leave this field empty the read more link will not be displayed in the widget.', 'proteuswidgets' ); ?></small>
			</p>

			<hr>

			<h4><?php _e( 'Accordion items', 'proteuswidgets' ); ?></h4>

			<script type="text/template" id="js-pt-accordion-item-<?php echo esc_attr( $this->current_widget_id ); ?>">
				<p>
					<label for="<?php echo esc_attr( $this->get_field_id( 'items' ) ); ?>-{{id}}-title"><?php _e( 'Title:','proteuswidgets' ); ?></label>
					<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'items' ) ); ?>-{{id}}-title" name="<?php echo esc_attr( $this->get_field_name( 'items' ) ); ?>[{{id}}][title]" type="text" value="{{title}}" />
				</p>
				<p>
					<label for="<?php echo esc_attr( $this->get_field_id( 'items' ) ); ?>-{{id}}-content"><?php _e( 'Content:', 'proteuswidgets' ); ?></label>
					<textarea rows="4" class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'items' ) ); ?>-{{id}}-content" name="<?php echo esc_attr( $this->get_field_name( 'items' ) ); ?>[{{id}}][content]">{{content}}</textarea>
				</p>
				<p>
					<input name="<?php echo esc_attr( $this->get_field_name( 'items' ) ); ?>[{{id}}][id]" type="hidden" value="{{id}}" />
					<a href="#" class="pt-remove-accordion-item  js-pt-remove-accordion-item"><span class="dashicons dashicons-dismiss"></span> <?php _e( 'Remove Item', 'proteuswidgets' ); ?></a>
				</p>
			</script>
			<div class="pt-widget-accordion-items" id="accordion-items-<?php echo esc_attr( $this->current_widget_id ); ?>">
				<div class="accordion-items"></div>
				<p>
					<a href="#" class="button  js-pt-add-accordion-item"><?php _e( 'Add New Item','proteuswidgets' ); ?></a>
				</p>
			</div>
			<script type="text/javascript">
				(function() {
					// repopulate the form
					var accordionItemsJSON = <?php echo wp_json_encode( $instance['items'] ) ?>;

					// get the right widget id and remove the added < > characters at the start and at the end.
					var widgetId = '<<?php echo esc_js( $this->current_widget_id ); ?>>'.slice( 1, -1 );

					if ( _.isFunction( ProteusWidgets.Utils.repopulateAccordionItems ) ) {
						ProteusWidgets.Utils.repopulateAccordionItems( accordionItemsJSON, widgetId );
					}
				})();
			</script>

			<?php
		}
	}
}
