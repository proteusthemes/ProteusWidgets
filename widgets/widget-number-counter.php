<?php
/**
 * Number Counter Widget
 *
 * @package ProteusWidgets
 * @since 2.4.0
 */

if ( ! class_exists( 'PW_Number_Counter' ) ) {
	class PW_Number_Counter extends PW_Widget {

		private $current_widget_id;
		private $font_awesome_icons_list;
		private $fields;

		// Basic widget settings
		function widget_id_base() { return 'number-counter'; }
		function widget_name() { return esc_html__( 'Number Counter', 'proteuswidgets' ); }
		function widget_description() { return esc_html__( 'Widget with multiple animated counters.', 'proteuswidgets' ); }
		function widget_class() { return 'widget-number-counter'; }

		public function __construct() {
			parent::__construct();

			// Get the settings for the number counter widgets
			$this->fields = apply_filters( 'pw/number_counter_widget', array(
				'icon' => false,
			) );

			// A list of icons to choose from in the widget backend
			$this->font_awesome_icons_list = apply_filters(
				'pw/number_counter_fa_icons_list',
				array(
					'fa-building-o',
					'fa-users',
					'fa-globe',
					'fa-suitcase',
					'fa-car',
					'fa-road',
					'fa-home',
					'fa-phone',
					'fa-clock-o',
					'fa-money',
					'fa-cog',
					'fa-archive',
					'fa-compass',
					'fa-comments-o',
					'fa-dashboard',
					'fa-exclamation-circle',
					'fa-female',
					'fa-male',
					'fa-heart',
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
			// Prepare the data for mustache template
			$counters = isset( $instance['counters'] ) ? array_values( $instance['counters'] ) : array();

			foreach ( $counters as $key => $counter ) {
				$counters[ $key ]['title']         = esc_html( $counter['title'] );
				$counters[ $key ]['number']        = absint( $counter['number'] );
				$counters[ $key ]['leading_zeros'] = esc_html( PW_Functions::leading_zeros( strlen( $counter['number'] ) ) );
				if ( $this->fields['icon'] ) {
					$counters[ $key ]['icon'] = esc_attr( $counter['icon'] );
				}
			}

			// Mustache widget-number-counter template rendering
			echo $this->mustache->render( apply_filters( 'pw/widget_number_counter_view', 'widget-number-counter' ), array(
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

				if ( $this->fields['icon'] ) {
					$instance['counters'][ $key ]['icon'] = sanitize_html_class( $counter['icon'] );
				}
			}

			return $instance;
		}

		/**
		 * Back-end widget form.
		 *
		 * @param array $instance The widget options
		 */
		public function form( $instance ) {

			$speed = empty( $instance['speed'] ) ? 1000 : $instance['speed'];

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

			?>

			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'speed' ) ); ?>"><?php _e( 'Counting Speed (in milliseconds):', 'proteuswidgets' ); ?></label>
				<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'speed' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'speed' ) ); ?>" type="number" min="0" step="500" value="<?php echo esc_attr( $speed ); ?>" />
			</p>

			<hr>

			<h4><?php _e( 'Counters', 'proteuswidgets' ); ?></h4>

			<script type="text/template" id="js-pt-counter-<?php echo esc_attr( $this->current_widget_id ); ?>">
				<p>
					<label for="<?php echo esc_attr( $this->get_field_id( 'counters' ) ); ?>-{{id}}-title"><?php _e( 'Title:','proteuswidgets' ); ?></label>
					<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'counters' ) ); ?>-{{id}}-title" name="<?php echo esc_attr( $this->get_field_name( 'counters' ) ); ?>[{{id}}][title]" type="text" value="{{title}}" />
				</p>

				<p>
					<label for="<?php echo esc_attr( $this->get_field_id( 'counters' ) ); ?>-{{id}}-number"><?php _e( 'Number:', 'proteuswidgets' ); ?></label>
					<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'counters' ) ); ?>-{{id}}-number" name="<?php echo esc_attr( $this->get_field_name( 'counters' ) ); ?>[{{id}}][number]" type="text" value="{{number}}" />
				</p>

			<?php if ( $this->fields['icon'] ) : ?>
				<p>
					<label for="<?php echo esc_attr( $this->get_field_id( 'counters' ) ); ?>-{{id}}-icon"><?php _e( 'Icon:', 'proteuswidgets' ); ?></label> <br />
					<small><?php printf( esc_html__( 'Click on the icon below or manually select from the %s website.', 'proteuswidgets' ), '<a href="http://fontawesome.io/icons/" target="_blank">FontAwesome</a>' ); ?></small>
					<input id="<?php echo esc_attr( $this->get_field_id( 'counters' ) ); ?>-{{id}}-icon" name="<?php echo esc_attr( $this->get_field_name( 'counters' ) ); ?>[{{id}}][icon]" type="text" value="{{icon}}" class="widefat  js-icon-input" /> <br><br>
					<?php foreach ( $this->font_awesome_icons_list as $icon ) : ?>
						<a class="js-selectable-icon  icon-widget" href="#" data-iconname="<?php echo esc_attr( $icon ); ?>"><i class="fa fa-lg <?php echo esc_attr( $icon ); ?>"></i></a>
					<?php endforeach; ?>
				</p>
			<?php endif;?>

				<p>
					<input name="<?php echo esc_attr( $this->get_field_name( 'counters' ) ); ?>[{{id}}][id]" type="hidden" value="{{id}}" />
					<a href="#" class="pt-remove-counter  js-pt-remove-counter"><span class="dashicons dashicons-dismiss"></span> <?php _e( 'Remove Counter', 'proteuswidgets' ); ?></a>
				</p>
			</script>
			<div class="pt-widget-about-us" id="counters-<?php echo esc_attr( $this->current_widget_id ); ?>">
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
}
