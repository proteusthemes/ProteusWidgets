<?php
/**
 * Testimonials Widget
 *
 * @package ProteusWidgets
 * @since 1.1.0
 */

if ( ! class_exists( 'PW_Number_Counter' ) ) {
	class PW_Number_Counter extends PW_Widget {

		private $current_widget_id;

		// Basic widget settings
		function widget_id_base() { return 'number-counter'; }
		function widget_name() { return __( 'Number Counter', 'proteuswidgets' ); }
		function widget_description() { return __( 'Number Counter widget used in Page Builder editor.', 'proteuswidgets' ); }
		function widget_class() { return 'widget-number-counter'; }

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
			if ( isset( $instance['counters'] ) ) {
				$counters = $instance['counters'];
			}
			else {
				$counters = array(
					array(
						'id'     => 1,
						'title'  => '',
						'number' => '',
						'icon'   => '',
					),
				);
			}

			$counters = PW_Functions::reorder_widget_array_key_values( $counters );

			// Mustache widget-number-counter template rendering
			echo $this->mustache->render( apply_filters( 'pw/widget_number_counter', 'widget-number-counter' ), array(
				'args'     => $args,
				'instance' => $instance,
				'counters' => $counters,
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

			$instance['speed'] = absint( $new_instance['speed'] );

			foreach ( $new_instance['counters'] as $key => $counter ) {
				$instance['counters'][ $key ]['id']     = sanitize_key( $counter['id'] );
				$instance['counters'][ $key ]['title']  = sanitize_text_field( $counter['title'] );
				$instance['counters'][ $key ]['number'] = absint( $counter['number'] );
				$instance['counters'][ $key ]['icon']   = sanitize_text_field( $counter['icon'] );
			}

			return $instance;
		}

		/**
		 * Back-end widget form.
		 *
		 * @param array $instance The widget options
		 */
		public function form( $instance ) {

			$speed = empty( $instance['speed'] ) ? 3000 : $instance['speed'];

			if ( isset( $instance['counters'] ) ) {
				$counters = $instance['counters'];
			}
			else {
				$counters = array(
					array(
						'id'     => 1,
						'title'  => '',
						'number' => '',
						'icon'   => '',
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

			$this->current_widget_id = esc_attr( $this->current_widget_id );

			?>

			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'speed' ) ); ?>"><?php _e( 'Counting Speed (in miliseconds):', 'proteuswidgets' ); ?></label>
				<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'speed' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'speed' ) ); ?>" type="number" min="0" step="500" value="<?php echo esc_attr( $speed ); ?>" />
			</p>

			<hr>

			<h4><?php _e( 'Counters:', 'proteuswidgets' ); ?></h4>

			<script type="text/template" id="js-pt-counter-<?php echo $this->current_widget_id; ?>">
				<p>
					<label for="<?php echo esc_attr( $this->get_field_id( 'counters' ) ); ?>-{{id}}-title"><?php _e( 'Title:', 'proteuswidgets' ); ?></label>
					<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'counters' ) ); ?>-{{id}}-title" name="<?php echo esc_attr( $this->get_field_name( 'counters' ) ); ?>[{{id}}][title]" type="text" value="{{title}}" />
				</p>

				<p>
					<label for="<?php echo esc_attr( $this->get_field_id( 'counters' ) ); ?>-{{id}}-number"><?php _e( 'Number:', 'proteuswidgets' ); ?></label>
					<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'counters' ) ); ?>-{{id}}-number" name="<?php echo esc_attr( $this->get_field_name( 'counters' ) ); ?>[{{id}}][number]" type="text" value="{{number}}" />
				</p>

				<p>
				</p>

				<p>
					<label for="<?php echo esc_attr( $this->get_field_id( 'counters' ) ); ?>-{{id}}-icon"><?php _e( 'Icon:', 'proteuswidgets' ); ?></label>
					<small><?php printf( __( 'Click on the icon below or manually select from the %s website. Example of the input: fa-bed', 'proteuswidgets' ), '<a href="http://fontawesome.io/icons/" target="_blank">FontAwesome</a>' ); ?>.</small>
					<input class="widefat  js-icon-input" id="<?php echo esc_attr( $this->get_field_id( 'counters' ) ); ?>-{{id}}-icon" name="<?php echo esc_attr( $this->get_field_name( 'counters' ) ); ?>[{{id}}][icon]" type="text" value="{{icon}}" />
					<br><br>
					<a class="js-selectable-icon  icon-widget" href="#" data-iconname="fa-building-o"><i class="fa fa-lg fa-building-o"></i></a>
					<a class="js-selectable-icon  icon-widget" href="#" data-iconname="fa-users"><i class="fa fa-lg fa-users"></i></a>
					<a class="js-selectable-icon  icon-widget" href="#" data-iconname="fa-globe"><i class="fa fa-lg fa-globe"></i></a>
					<a class="js-selectable-icon  icon-widget" href="#" data-iconname="fa-suitcase"><i class="fa fa-lg fa-suitcase"></i></a>
					<a class="js-selectable-icon  icon-widget" href="#" data-iconname="fa-car"><i class="fa fa-lg fa-car"></i></a>
					<a class="js-selectable-icon  icon-widget" href="#" data-iconname="fa-ship"><i class="fa fa-lg fa-ship"></i></a>
					<a class="js-selectable-icon  icon-widget" href="#" data-iconname="fa-truck"><i class="fa fa-lg fa-truck"></i></a>
					<a class="js-selectable-icon  icon-widget" href="#" data-iconname="fa-train"><i class="fa fa-lg fa-train"></i></a>
					<a class="js-selectable-icon  icon-widget" href="#" data-iconname="fa-plane"><i class="fa fa-lg fa-plane"></i></a>
					<a class="js-selectable-icon  icon-widget" href="#" data-iconname="fa-road"><i class="fa fa-lg fa-road"></i></a>
					<a class="js-selectable-icon  icon-widget" href="#" data-iconname="fa-home"><i class="fa fa-lg fa-home"></i></a>
					<a class="js-selectable-icon  icon-widget" href="#" data-iconname="fa-phone"><i class="fa fa-lg fa-phone"></i></a>
					<a class="js-selectable-icon  icon-widget" href="#" data-iconname="fa-clock-o"><i class="fa fa-lg fa-clock-o"></i></a>
					<a class="js-selectable-icon  icon-widget" href="#" data-iconname="fa-beer"><i class="fa fa-lg fa-beer"></i></a>
					<a class="js-selectable-icon  icon-widget" href="#" data-iconname="fa-camera-retro"><i class="fa fa-lg fa-camera-retro"></i></a>
					<a class="js-selectable-icon  icon-widget" href="#" data-iconname="fa-check-circle-o"><i class="fa fa-lg fa-check-circle-o"></i></a>
					<a class="js-selectable-icon  icon-widget" href="#" data-iconname="fa-cog"><i class="fa fa-lg fa-cog"></i></a>
					<a class="js-selectable-icon  icon-widget" href="#" data-iconname="fa-cogs"><i class="fa fa-lg fa-cogs"></i></a>
					<a class="js-selectable-icon  icon-widget" href="#" data-iconname="fa-comments-o"><i class="fa fa-lg fa-comments-o"></i></a>
					<a class="js-selectable-icon  icon-widget" href="#" data-iconname="fa-compass"><i class="fa fa-lg fa-compass"></i></a>
					<a class="js-selectable-icon  icon-widget" href="#" data-iconname="fa-dashboard"><i class="fa fa-lg fa-dashboard"></i></a>
					<a class="js-selectable-icon  icon-widget" href="#" data-iconname="fa-exclamation-circle"><i class="fa fa-lg fa-exclamation-circle"></i></a>
					<a class="js-selectable-icon  icon-widget" href="#" data-iconname="fa-male"><i class="fa fa-lg fa-male"></i></a>
					<a class="js-selectable-icon  icon-widget" href="#" data-iconname="fa-female"><i class="fa fa-lg fa-female"></i></a>
					<a class="js-selectable-icon  icon-widget" href="#" data-iconname="fa-flag"><i class="fa fa-lg fa-flag"></i></a>
					<a class="js-selectable-icon  icon-widget" href="#" data-iconname="fa-folder-open-o"><i class="fa fa-lg fa-folder-open-o"></i></a>
					<a class="js-selectable-icon  icon-widget" href="#" data-iconname="fa-heart"><i class="fa fa-lg fa-heart"></i></a>
					<a class="js-selectable-icon  icon-widget" href="#" data-iconname="fa-inbox"><i class="fa fa-lg fa-inbox"></i></a>
					<a class="js-selectable-icon  icon-widget" href="#" data-iconname="fa-info-circle"><i class="fa fa-lg fa-info-circle"></i></a>
					<a class="js-selectable-icon  icon-widget" href="#" data-iconname="fa-key"><i class="fa fa-lg fa-key"></i></a>
					<a class="js-selectable-icon  icon-widget" href="#" data-iconname="fa-laptop"><i class="fa fa-lg fa-laptop"></i></a>
					<a class="js-selectable-icon  icon-widget" href="#" data-iconname="fa-leaf"><i class="fa fa-lg fa-leaf"></i></a>
					<a class="js-selectable-icon  icon-widget" href="#" data-iconname="fa-map-marker"><i class="fa fa-lg fa-map-marker"></i></a>
					<a class="js-selectable-icon  icon-widget" href="#" data-iconname="fa-money"><i class="fa fa-lg fa-money"></i></a>
					<a class="js-selectable-icon  icon-widget" href="#" data-iconname="fa-archive"><i class="fa fa-lg fa-archive"></i></a>
					<a class="js-selectable-icon  icon-widget" href="#" data-iconname="fa-shopping-cart"><i class="fa fa-lg fa-shopping-cart"></i></a>
					<a class="js-selectable-icon  icon-widget" href="#" data-iconname="fa-star-o"><i class="fa fa-lg fa-star-o"></i></a>
					<a class="js-selectable-icon  icon-widget" href="#" data-iconname="fa-thumbs-up"><i class="fa fa-lg fa-thumbs-up"></i></a>
					<a class="js-selectable-icon  icon-widget" href="#" data-iconname="fa-wrench"><i class="fa fa-lg fa-wrench"></i></a>
				</p>


				<p>
					<input name="<?php echo esc_attr( $this->get_field_name( 'counters' ) ); ?>[{{id}}][id]" type="hidden" value="{{id}}" />
					<a href="#" class="pt-remove-counter  js-pt-remove-counter"><span class="dashicons dashicons-dismiss"></span> <?php _e( 'Remove Counter', 'proteuswidgets' ); ?></a>
				</p>
			</script>
			<div class="pt-widget-about-us" id="counters-<?php echo $this->current_widget_id; ?>">
				<div class="counters"></div>
				<p>
					<a href="#" class="button  js-pt-add-counter"><?php _e( 'Add New Counter', 'proteuswidgets' ); ?></a>
				</p>
			</div>
			<script type="text/javascript">
				(function() {
					// repopulate the form
					var countersJSON = <?php echo wp_json_encode( $counters ) ?>;

					// get the right widget id and remove the added < > characters at the start and at the end.
					var widgetId = '<<?php echo esc_js( $this->current_widget_id ); ?>>'.slice( 1, -1 );

					if ( _.isFunction( ProteusWidgets.Utils.repopulateCounters ) ) {
						ProteusWidgets.Utils.repopulateCounters( countersJSON, widgetId );
					}
				})();
			</script>

			<?php
		}

	}
	register_widget( 'PW_Number_Counter' );
}