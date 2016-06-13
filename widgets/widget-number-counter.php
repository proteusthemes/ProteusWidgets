<?php
/**
 * Number Counter Widget
 *
 * @package ProteusWidgets
 * @since 2.4.0
 */

if ( ! class_exists( 'PW_Number_Counter' ) ) {
	class PW_Number_Counter extends PW_Widget {

		private $current_widget_id, $font_awesome_icons_list, $fields;

		public function __construct() {

			// Overwrite the widget variables of the parent class.
			$this->widget_id_base     = 'number-counter';
			$this->widget_name        = esc_html__( 'Number Counter', 'proteuswidgets' );
			$this->widget_description = esc_html__( 'Widget with multiple animated counters.', 'proteuswidgets' );
			$this->widget_class       = 'widget-number-counter';

			parent::__construct();

			// Get the settings for the number counter widgets.
			$this->fields = apply_filters( 'pw/number_counter_widget', array(
				'icon'         => false,
				'progress_bar' => false,
			) );

			// A list of icons to choose from in the widget backend.
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
			// Prepare the data for template.
			$counters = isset( $instance['counters'] ) ? array_values( $instance['counters'] ) : array();

			// The widget-number-counter template rendering.
			echo $this->template_engine->render_template( apply_filters( 'pw/widget_number_counter_view', 'widget-number-counter' ), array(
				'args'     => $args,
				'instance' => $instance,
				'counters' => $counters,
			) );

		}

		/**
		 * Sanitize widget form values as they are saved.
		 *
		 * @param array $new_instance The new options.
		 * @param array $old_instance The previous options.
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

				if ( $this->fields['progress_bar'] ) {
					$instance['counters'][ $key ]['progress_bar_value'] = PW_Functions::bound( $counter['progress_bar_value'], 0, 100 );
				}
			}

			return $instance;
		}

		/**
		 * Back-end widget form.
		 *
		 * @param array $instance The widget options.
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

			// Page Builder fix when using repeating fields.
			if ( 'temp' === $this->id ) {
				$this->current_widget_id = $this->number;
			}
			else {
				$this->current_widget_id = $this->id;
			}

			?>

			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'speed' ) ); ?>"><?php esc_html_e( 'Counting Speed (in milliseconds):', 'proteuswidgets' ); ?></label>
				<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'speed' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'speed' ) ); ?>" type="number" min="0" step="500" value="<?php echo esc_attr( $speed ); ?>" />
			</p>

			<hr>

			<h4><?php esc_html_e( 'Counters', 'proteuswidgets' ); ?></h4>

			<script type="text/template" id="js-pt-counter-<?php echo esc_attr( $this->current_widget_id ); ?>">
				<p>
					<label for="<?php echo esc_attr( $this->get_field_id( 'counters' ) ); ?>-{{id}}-title"><?php esc_html_e( 'Title:','proteuswidgets' ); ?></label>
					<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'counters' ) ); ?>-{{id}}-title" name="<?php echo esc_attr( $this->get_field_name( 'counters' ) ); ?>[{{id}}][title]" type="text" value="{{title}}" />
				</p>

				<p>
					<label for="<?php echo esc_attr( $this->get_field_id( 'counters' ) ); ?>-{{id}}-number"><?php esc_html_e( 'Number:', 'proteuswidgets' ); ?></label>
					<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'counters' ) ); ?>-{{id}}-number" name="<?php echo esc_attr( $this->get_field_name( 'counters' ) ); ?>[{{id}}][number]" type="text" value="{{number}}" />
				</p>

			<?php if ( $this->fields['progress_bar'] ) : ?>
				<p>
					<label for="<?php echo esc_attr( $this->get_field_id( 'counters' ) ); ?>-{{id}}-progress_bar_value"><?php esc_html_e( 'Progress bar value:', 'proteuswidgets' ); ?></label> <br />
					<input id="<?php echo esc_attr( $this->get_field_id( 'counters' ) ); ?>-{{id}}-progress_bar_value" name="<?php echo esc_attr( $this->get_field_name( 'counters' ) ); ?>[{{id}}][progress_bar_value]" type="number" min="0" max="100" value="{{progress_bar_value}}" />
					<small><?php esc_html_e( 'Input a number from 0 to 100, which corresponds to the percentage of the progress bar.', 'proteuswidgets' ); ?></small>
				</p>
			<?php endif;?>

			<?php if ( $this->fields['icon'] ) : ?>
				<p>
					<label for="<?php echo esc_attr( $this->get_field_id( 'counters' ) ); ?>-{{id}}-icon"><?php esc_html_e( 'Icon:', 'proteuswidgets' ); ?></label> <br />
					<small><?php printf( esc_html__( 'Click on the icon below or manually select from the %s website.', 'proteuswidgets' ), '<a href="http://fontawesome.io/icons/" target="_blank">FontAwesome</a>' ); ?></small>
					<input id="<?php echo esc_attr( $this->get_field_id( 'counters' ) ); ?>-{{id}}-icon" name="<?php echo esc_attr( $this->get_field_name( 'counters' ) ); ?>[{{id}}][icon]" type="text" value="{{icon}}" class="widefat  js-icon-input" /> <br><br>
					<?php foreach ( $this->font_awesome_icons_list as $icon ) : ?>
						<a class="js-selectable-icon  icon-widget" href="#" data-iconname="<?php echo esc_attr( $icon ); ?>"><i class="fa fa-lg <?php echo esc_attr( $icon ); ?>"></i></a>
					<?php endforeach; ?>
				</p>
			<?php endif;?>

				<p>
					<input name="<?php echo esc_attr( $this->get_field_name( 'counters' ) ); ?>[{{id}}][id]" type="hidden" value="{{id}}" />
					<a href="#" class="pt-remove-counter  js-pt-remove-counter"><span class="dashicons dashicons-dismiss"></span> <?php esc_html_e( 'Remove Counter', 'proteuswidgets' ); ?></a>
				</p>
			</script>
			<div class="pt-widget-about-us" id="counters-<?php echo esc_attr( $this->current_widget_id ); ?>">
				<div class="counters"></div>
				<p>
					<a href="#" class="button  js-pt-add-counter"><?php esc_html_e( 'Add New Counter', 'proteuswidgets' ); ?></a>
				</p>
			</div>
			<script type="text/javascript">
				(function() {
					// Repopulate the form.
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
