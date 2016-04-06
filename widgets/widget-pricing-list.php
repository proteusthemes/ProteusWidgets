<?php
/**
 * Pricing List Widget
 */

if ( ! class_exists( 'PW_Pricing_List' ) ) {
	class PW_Pricing_List extends PW_Widget {

		private $price_list_allowed_html, $current_widget_id;

		/**
		 * Register widget with WordPress.
		 */
		public function __construct() {

			// Overwrite the widget variables of the parent class.
			$this->widget_id_base     = 'pricing_list';
			$this->widget_name        = esc_html__( 'Pricing List', 'proteuswidgets' );
			$this->widget_description = esc_html__( 'Displays a simple pricing list', 'proteuswidgets' );
			$this->widget_class       = 'widget-pricing-list';

			parent::__construct();

			$this->price_list_allowed_html = apply_filters(
				'pw/price_list_allowed_html',
				array(
					'span' => array( 'class' => array() ),
					'i'    => array( 'class' => array() ),
					'b'    => array( 'class' => array() ),
					's'    => array( 'class' => array() ),
				)
			);
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

			// Prepare data.
			$items                    = isset( $instance['items'] ) ? $instance['items'] : array();
			$instance['widget_title'] = empty( $instance['widget_title'] ) ? '' : apply_filters( 'widget_title', $instance['widget_title'], $instance );

			// widget-pricing-list template rendering.
			echo $this->template_engine->render_template( apply_filters( 'pw/widget_pricing_list_view', 'widget-pricing-list' ), array(
				'args'         => $args,
				'instance'     => $instance,
				'items'        => $items,
				'allowed_html' => $this->price_list_allowed_html,
			));
		}

		/**
		 * Sanitize widget form values as they are saved.
		 *
		 * @param array $new_instance The new options.
		 * @param array $old_instance The previous options.
		 */
		public function update( $new_instance, $old_instance ) {
			$instance = array();

			$instance['widget_title'] = sanitize_text_field( $new_instance['widget_title'] );

			if ( ! empty( $new_instance['items'] )  ) {
				foreach ( $new_instance['items'] as $key => $item ) {
					$instance['items'][ $key ]['id']          = sanitize_key( $item['id'] );
					$instance['items'][ $key ]['badge']       = sanitize_text_field( $item['badge'] );
					$instance['items'][ $key ]['title']       = sanitize_text_field( $item['title'] );
					$instance['items'][ $key ]['price']       = wp_kses( $item['price'], $this->price_list_allowed_html );
					$instance['items'][ $key ]['description'] = wp_kses_post( $item['description'] );
				}
			}

			// Sort items by ids, because order might have changed.
			usort( $instance['items'], array( $this, 'sort_by_id' ) );

			return $instance;
		}

		/**
		 * Back-end widget form.
		 *
		 * @param array $instance The widget options.
		 */
		public function form( $instance ) {

			$widget_title = empty( $instance['widget_title'] ) ? '' : $instance['widget_title'];
			$items        = isset( $instance['items'] ) ? $instance['items'] : array();

			// Page Builder fix when using repeating fields.
			if ( 'temp' === $this->id ) {
				$this->current_widget_id = $this->number;
			}
			else {
				$this->current_widget_id = $this->id;
			}

			?>

			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'widget_title' ) ); ?>"><?php esc_html_e( 'Widget title:', 'proteuswidgets' ); ?></label>
				<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'widget_title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'widget_title' ) ); ?>" type="text" value="<?php echo esc_attr( $widget_title ); ?>" />
			</p>

			<hr>

			<h3><?php esc_html_e( 'Items:', 'proteuswidgets' ); ?></h3>

			<script type="text/template" id="js-pt-pricing-list-item-<?php echo esc_attr( $this->current_widget_id ); ?>">
				<div class="pt-sortable-setting  ui-widget  ui-widget-content  ui-helper-clearfix  ui-corner-all">
					<div class="pt-sortable-setting__header  ui-widget-header  ui-corner-all">
						<span class="dashicons  dashicons-sort"></span>
						<span><?php esc_html_e( 'Pricing list', 'proteuswidgets' ); ?> - </span>
						<span class="pt-sortable-setting__header-title">{{title}}</span>
						<span class="pt-sortable-setting__toggle  dashicons  dashicons-minus"></span>
					</div>
					<div class="pt-sortable-setting__content">
						<p>
							<label for="<?php echo esc_attr( $this->get_field_id( 'items' ) ); ?>-{{id}}-badge"><?php esc_html_e( 'Badge:', 'proteuswidgets' ); ?></label>
							<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'items' ) ); ?>-{{id}}-badge" name="<?php echo esc_attr( $this->get_field_name( 'items' ) ); ?>[{{id}}][badge]" type="text" value="{{badge}}" />
						</p>
						<p>
							<label for="<?php echo esc_attr( $this->get_field_id( 'items' ) ); ?>-{{id}}-title"><?php esc_html_e( 'Title:', 'proteuswidgets' ); ?></label>
							<input class="widefat  js-pt-sortable-setting-title" id="<?php echo esc_attr( $this->get_field_id( 'items' ) ); ?>-{{id}}-title" name="<?php echo esc_attr( $this->get_field_name( 'items' ) ); ?>[{{id}}][title]" type="text" value="{{title}}" />
						</p>
						<p>
							<label for="<?php echo esc_attr( $this->get_field_id( 'items' ) ); ?>-{{id}}-price"><?php esc_html_e( 'Price:', 'proteuswidgets' ); ?></label>
							<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'items' ) ); ?>-{{id}}-price" name="<?php echo esc_attr( $this->get_field_name( 'items' ) ); ?>[{{id}}][price]" type="text" value="{{price}}" />
						</p>

						<p>
							<label for="<?php echo esc_attr( $this->get_field_id( 'items' ) ); ?>-{{id}}-description"><?php esc_html_e( 'Description:', 'proteuswidgets' ); ?></label>
							<textarea rows="4" class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'items' ) ); ?>-{{id}}-description" name="<?php echo esc_attr( $this->get_field_name( 'items' ) ); ?>[{{id}}][description]">{{description}}</textarea>
						</p>

						<p>
							<input name="<?php echo esc_attr( $this->get_field_name( 'items' ) ); ?>[{{id}}][id]" class="js-pt-pricing-list-id" type="hidden" value="{{id}}" />
							<a href="#" class="pt-remove-pricing-list-item  js-pt-remove-pricing-list-item"><span class="dashicons dashicons-dismiss"></span> <?php esc_html_e( 'Remove item', 'proteuswidgets' ); ?></a>
						</p>
					</div>
				</div>
			</script>
			<div class="pt-widget-pricing-list-items" id="pricing-list-items-<?php echo esc_attr( $this->current_widget_id ); ?>">
				<div class="pricing-list-items  js-pt-sortable-pricing-lists"></div>
				<p>
					<a href="#" class="button  js-pt-add-pricing-list-item"><?php esc_html_e( 'Add New Item', 'proteuswidgets' ); ?></a>
				</p>
			</div>

			<script type="text/javascript">
				(function( $ ) {
					var pricingListItemJSON = <?php echo wp_json_encode( $items ) ?>;

					// Get the right widget id and remove the added < > characters at the start and at the end.
					var widgetId = '<<?php echo esc_js( $this->current_widget_id ); ?>>'.slice( 1, -1 );

					if ( _.isFunction( ProteusWidgets.Utils.repopulatePricingListItems ) ) {
						ProteusWidgets.Utils.repopulatePricingListItems( pricingListItemJSON, widgetId );
					}

					// Make pricing list settings sortable.
					$( '.js-pt-sortable-pricing-lists' ).sortable({
						items: '.pt-widget-single-pricing-list-item',
						handle: '.pt-sortable-setting__header',
						cancel: '.pt-sortable-setting__toggle',
						placeholder: 'pt-sortable-setting__placeholder',
						stop: function( event, ui ) {
							$( this ).find( '.js-pt-pricing-list-id' ).each( function( index ) {
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
