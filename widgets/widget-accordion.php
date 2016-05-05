<?php
/**
 * Accordion Widget
 *
 * @package ProteusWidgets
 * @since 2.4.0
 */

if ( ! class_exists( 'PW_Accordion' ) ) {
	class PW_Accordion extends PW_Widget {

		public function __construct() {

			// Overwrite the widget variables of the parent class
			$this->widget_id_base     = 'accordion';
			$this->widget_name        = esc_html__( 'Accordion', 'proteuswidgets' );
			$this->widget_description = '';
			$this->widget_class       = 'widget-accordion';

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
			// Prepare data for template
			$items = isset( $instance['items'] ) ? array_values( $instance['items'] ) : array();
			$instance['preped_title'] = apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base );

			$text = array(
				'read_more' => esc_html__( 'Read more', 'proteuswidgets' ),
			);

			// widget-accordion template rendering
			echo $this->template_engine->render_template( apply_filters( 'pw/widget_accordion_view', 'widget-accordion' ), array(
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

			// Sort items by ids, because order might have changed.
			usort( $instance['items'], array( $this, 'sort_by_id' ) );

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
				<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title:', 'proteuswidgets' ); ?></label>
				<input class="widefat" type="text" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" value="<?php echo esc_attr( $title ); ?>" />
			</p>

			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'read_more_link' ) ); ?>"><?php esc_html_e( 'Read more URL:','proteuswidgets' ); ?></label>
				<input class="widefat" type="text" id="<?php echo esc_attr( $this->get_field_id( 'read_more_link' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'read_more_link' ) ); ?>" value="<?php echo esc_attr( $read_more_link ); ?>" />
				<br>
				<small><?php esc_html_e( 'If you leave this field empty the read more link will not be displayed in the widget.', 'proteuswidgets' ); ?></small>
			</p>

			<hr>

			<h4><?php esc_html_e( 'Accordion items', 'proteuswidgets' ); ?></h4>

			<script type="text/template" id="js-pt-accordion-item-<?php echo esc_attr( $this->current_widget_id ); ?>">
				<div class="pt-sortable-setting  ui-widget  ui-widget-content  ui-helper-clearfix  ui-corner-all">
					<div class="pt-sortable-setting__header  ui-widget-header  ui-corner-all">
						<span class="dashicons  dashicons-sort"></span>
						<span><?php esc_html_e( 'Accordion', 'proteuswidgets' ); ?> - </span>
						<span class="pt-sortable-setting__header-title">{{title}}</span>
						<span class="pt-sortable-setting__toggle  dashicons  dashicons-minus"></span>
					</div>
					<div class="pt-sortable-setting__content">
						<p>
							<label for="<?php echo esc_attr( $this->get_field_id( 'items' ) ); ?>-{{id}}-title"><?php esc_html_e( 'Title:','proteuswidgets' ); ?></label>
							<input class="widefat  js-pt-sortable-setting-title" id="<?php echo esc_attr( $this->get_field_id( 'items' ) ); ?>-{{id}}-title" name="<?php echo esc_attr( $this->get_field_name( 'items' ) ); ?>[{{id}}][title]" type="text" value="{{title}}" />
						</p>
						<p>
							<label for="<?php echo esc_attr( $this->get_field_id( 'items' ) ); ?>-{{id}}-content"><?php esc_html_e( 'Content:', 'proteuswidgets' ); ?></label>
							<textarea rows="4" class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'items' ) ); ?>-{{id}}-content" name="<?php echo esc_attr( $this->get_field_name( 'items' ) ); ?>[{{id}}][content]">{{content}}</textarea>
						</p>
						<p>
							<input name="<?php echo esc_attr( $this->get_field_name( 'items' ) ); ?>[{{id}}][id]" class="js-pt-accordion-id" type="hidden" value="{{id}}" />
							<a href="#" class="pt-remove-accordion-item  js-pt-remove-accordion-item"><span class="dashicons dashicons-dismiss"></span> <?php esc_html_e( 'Remove Item', 'proteuswidgets' ); ?></a>
						</p>
					</div>
				</div>
			</script>
			<div class="pt-widget-accordion-items" id="accordion-items-<?php echo esc_attr( $this->current_widget_id ); ?>">
				<div class="accordion-items  js-pt-sortable-accordions"></div>
				<p>
					<a href="#" class="button  js-pt-add-accordion-item"><?php esc_html_e( 'Add New Item','proteuswidgets' ); ?></a>
				</p>
			</div>
			<script type="text/javascript">
				(function( $ ) {
					// repopulate the form
					var accordionItemsJSON = <?php echo wp_json_encode( $instance['items'] ) ?>;

					// get the right widget id and remove the added < > characters at the start and at the end.
					var widgetId = '<<?php echo esc_js( $this->current_widget_id ); ?>>'.slice( 1, -1 );

					if ( _.isFunction( ProteusWidgets.Utils.repopulateAccordionItems ) ) {
						ProteusWidgets.Utils.repopulateAccordionItems( accordionItemsJSON, widgetId );
					}

					// Make accordion settings sortable.
					$( '.js-pt-sortable-accordions' ).sortable({
						items: '.pt-widget-single-accordion-item',
						handle: '.pt-sortable-setting__header',
						cancel: '.pt-sortable-setting__toggle',
						placeholder: 'pt-sortable-setting__placeholder',
						stop: function( event, ui ) {
							$( this ).find( '.js-pt-accordion-id' ).each( function( index ) {
								$( this ).val( index );
							});
						}
					});
				})( jQuery );
			</script>

			<?php
		}
	}
}
