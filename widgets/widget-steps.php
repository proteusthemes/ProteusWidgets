<?php
/**
 * Steps Widget
 *
 * @package ProteusWidgets
 * @since 2.4.0
 */

if ( ! class_exists( 'PW_Steps' ) ) {
	class PW_Steps extends PW_Widget {

		private $allowed_html_in_content_field;
		private $font_awesome_icons_list;
		private $current_widget_id;

		// Basic widget settings
		function widget_id_base() { return 'steps'; }
		function widget_name() { return esc_html__( 'Steps', 'proteuswidgets' ); }
		function widget_description() { return esc_html__( 'Displays multiple steps for your process.', 'proteuswidgets' ); }
		function widget_class() { return 'widget-steps'; }

		public function __construct() {
			parent::__construct();

			// Allowed HTML in content field
			$this->allowed_html_in_content_field = apply_filters(
				'pw/allowed_html_in_content_field',
				array(
					'strong' => array(),
					'b'      => array(),
					'a'      => array(
						'href'  => array(),
						'class' => array(),
					),
				)
			);

			// A list of icons to choose from in the widget backend
			$this->font_awesome_icons_list = apply_filters(
				'pw/steps_fa_icons_list',
				array(
					'fa-mobile',
					'fa-envelope',
					'fa-wrench',
					'fa-reply',
					'fa-laptop',
					'fa-gamepad',
					'fa-television',
					'fa-music',
					'fa-battery-full',
					'fa-ellipsis-v',
					'fa-apple',
					'fa-linux',
					'fa-windows',
					'fa-android',
					'fa-cogs',
					'fa-plug',
					'fa-volume-up',
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
			// Prepare data for mustache template
			$items = isset( $instance['items'] ) ? array_values( $instance['items'] ) : array();
			$instance['title_is_set'] = ! empty( $instance['title'] );
			$instance['preped_title'] = apply_filters( 'widget_title', $instance['title'] , $instance, $this->id_base );

			foreach ( $items as $key => $item ) {
				$items[ $key ]['title']   = wp_kses( $item['title'], array() );
				$items[ $key ]['content'] = wp_kses( $item['content'], $this->allowed_html_in_content_field );
				$items[ $key ]['icon']    = esc_attr( $item['icon'] );
				$items[ $key ]['step']    = esc_html( $item['step'] );
			}

			// Mustache widget-steps template rendering
			echo $this->mustache->render( apply_filters( 'pw/widget_steps_view', 'widget-steps' ), array(
				'args'     => $args,
				'instance' => $instance,
				'items'    => $items,
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

			$instance['title'] = sanitize_text_field( $new_instance['title'] );

			foreach ( $new_instance['items'] as $key => $item ) {
				$instance['items'][ $key ]['id']      = sanitize_key( $item['id'] );
				$instance['items'][ $key ]['title']   = sanitize_text_field( $item['title'] );
				$instance['items'][ $key ]['content'] = wp_kses( $item['content'], $this->allowed_html_in_content_field );
				$instance['items'][ $key ]['icon']    = sanitize_html_class( $item['icon'] );
				$instance['items'][ $key ]['step']    = sanitize_text_field( $item['step'] );
			}

			return $instance;
		}

		/**
		 * Back-end widget form.
		 *
		 * @param array $instance The widget options
		 */
		public function form( $instance ) {
			$title = empty( $instance['title'] ) ? '' : $instance['title'];

			if ( ! isset( $instance['items'] ) ) {
				$instance['items'] = array(
					array(
						'id'      => 1,
						'title'   => '',
						'icon'    => '',
						'content' => '',
						'step'    => '',
					),
				);
			}

			// Page Builder fix when using repeating fields
			if ( 'temp' === $this->id ) {
				$this->current_widget_id = $this->number;
			}
			else {
				$this->current_widget_id = $this->id;
			}

		?>

			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php _e( 'Widget title:', 'proteuswidgets' ); ?></label>
				<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
			</p>

			<hr>

			<h4><?php _e( 'Steps:', 'proteuswidgets' ); ?></h4>

			<script type="text/template" id="js-pt-step-item-<?php echo esc_attr( $this->current_widget_id ); ?>">
				<p>
					<label for="<?php echo esc_attr( $this->get_field_id( 'items' ) ); ?>-{{id}}-title"><?php _e( 'Title:','proteuswidgets' ); ?></label>
					<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'items' ) ); ?>-{{id}}-title" name="<?php echo esc_attr( $this->get_field_name( 'items' ) ); ?>[{{id}}][title]" type="text" value="{{title}}" />
				</p>
				<p>
					<label for="<?php echo esc_attr( $this->get_field_id( 'items' ) ); ?>-{{id}}-icon"><?php _e( 'Icon:', 'proteuswidgets' ); ?></label> <br />
					<small><?php printf( esc_html__( 'Click on the icon below or manually input icon class from the %s website.', 'proteuswidgets' ), '<a href="http://fontawesome.io/icons/" target="_blank">FontAwesome</a>' ); ?></small>
					<input id="<?php echo esc_attr( $this->get_field_id( 'items' ) ); ?>-{{id}}-icon" name="<?php echo esc_attr( $this->get_field_name( 'items' ) ); ?>[{{id}}][icon]" type="text" value="{{icon}}" class="widefat  js-icon-input" /> <br><br>
					<?php foreach ( $this->font_awesome_icons_list as $icon ) : ?>
						<a class="js-selectable-icon  icon-widget" href="#" data-iconname="<?php echo esc_attr( $icon ); ?>"><i class="fa fa-lg <?php echo esc_attr( $icon ); ?>"></i></a>
					<?php endforeach; ?>
				</p>
				<p>
					<label for="<?php echo esc_attr( $this->get_field_id( 'items' ) ); ?>-{{id}}-content"><?php _e( 'Content:', 'proteuswidgets' ); ?></label>
					<textarea rows="4" class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'items' ) ); ?>-{{id}}-content" name="<?php echo esc_attr( $this->get_field_name( 'items' ) ); ?>[{{id}}][content]">{{content}}</textarea>
				</p>
				<p>
					<label for="<?php echo esc_attr( $this->get_field_id( 'items' ) ); ?>-{{id}}-step"><?php _e( 'Step:', 'proteuswidgets' ); ?></label>
					<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'items' ) ); ?>-{{id}}-step" name="<?php echo esc_attr( $this->get_field_name( 'items' ) ); ?>[{{id}}][step]" type="text" value="{{step}}" />
				</p>
				<p>
					<input name="<?php echo esc_attr( $this->get_field_name( 'items' ) ); ?>[{{id}}][id]" type="hidden" value="{{id}}" />
					<a href="#" class="pt-remove-step-item  js-pt-remove-step-item"><span class="dashicons dashicons-dismiss"></span> <?php _e( 'Remove Step', 'proteuswidgets' ); ?></a>
				</p>
			</script>
			<div class="pt-widget-step-items" id="step-items-<?php echo esc_attr( $this->current_widget_id ); ?>">
				<div class="step-items"></div>
				<p>
					<a href="#" class="button  js-pt-add-step-item"><?php _e( 'Add New Step', 'proteuswidgets' ); ?></a>
				</p>
			</div>
			<script type="text/javascript">
				(function() {
					// repopulate the form
					var stepItemsJSON = <?php echo wp_json_encode( $instance['items'] ) ?>;

					// get the right widget id and remove the added < > characters at the start and at the end.
					var widgetId = '<<?php echo esc_js( $this->current_widget_id ); ?>>'.slice( 1, -1 );

					if ( _.isFunction( ProteusWidgets.Utils.repopulateStepItems ) ) {
						ProteusWidgets.Utils.repopulateStepItems( stepItemsJSON, widgetId );
					}
				})();
			</script>

			<?php
		}
	}
}