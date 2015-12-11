<?php
/**
 * Opening Time Widget
 *
 * Adds the opening time, suitable for the sidebar or used above the slider
 *
 * @package ProteusWidgets
 * @since 1.0.0
 */


if ( ! class_exists( 'PW_Opening_Time' ) ) {
	class PW_Opening_Time extends PW_Widget {

		// Days of the week, needed for display and $instance variable
		private $days;

		/**
		 * Register widget with WordPress.
		 */
		public function __construct() {

			// Overwrite the widget variables of the parent class
			$this->widget_id_base     = 'opening_time';
			$this->widget_name        = esc_html__( 'Opening Time', 'proteuswidgets' );
			$this->widget_description = esc_html__( 'Widget shows opening times per day with optional text.', 'proteuswidgets' );
			$this->widget_class       = 'opening-time';

			parent::__construct();

			// Set the right order of the days
			$start_of_week = get_option( 'start_of_week ' ); // integer [0,6], 0 = Sunday, 1 = Monday ...
			$this->days = array(
				'Sun' => esc_html__( 'Sunday', 'proteuswidgets' ),
				'Mon' => esc_html__( 'Monday', 'proteuswidgets' ),
				'Tue' => esc_html__( 'Tuesday', 'proteuswidgets' ),
				'Wed' => esc_html__( 'Wednesday', 'proteuswidgets' ),
				'Thu' => esc_html__( 'Thursday', 'proteuswidgets' ),
				'Fri' => esc_html__( 'Friday', 'proteuswidgets' ),
				'Sat' => esc_html__( 'Saturday', 'proteuswidgets' ),
			);

			$this->rotate_days( $start_of_week );
		}

		/**
		 * Rotate the array for a given number of times
		 * @param  int $num shift the array for this number
		 * @return void
		 */
		private function rotate_days( $num ) {
			for ( $i = 0; $i < $num; $i++ ) {
				$keys = array_keys( $this->days );
				$val  = $this->days[ $keys[0] ];
				unset( $this->days[ $keys[0] ] );
				$this->days[ $keys[0] ] = $val;
			}
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
			$current_time = intval( time() + ( (double) get_option( 'gmt_offset' ) * 3600 ) );
			$opening_times = array();

			$i = 0;
			foreach ( $this->days as $day_label => $day ) {
				$current_line = array();

				$current_line['day'] = $day;

				$class = $i % 2 == 0 ? '' : ' light-bg';
				$class .= ( '1' != $instance[ $day_label . '_opened' ] ) ? ' closed' : '';
				$class .= ( date( 'D', $current_time ) == $day_label ) ? ' today' : '';
				$current_line['class'] = esc_attr( $class );

				if ( '1' == $instance[ $day_label . '_opened' ] ) {
					$current_line['day-time'] = $instance[ $day_label . '_from' ] . $instance['separator'] . $instance[ $day_label . '_to' ];
				} else {
					$current_line['day-time'] = $instance['closed_text'];
				}

				array_push( $opening_times , $current_line );
				$i++;
			}

			$instance['title'] = apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base );

			// widget-opening-time template rendering
			echo $this->template_engine->render_template( apply_filters( 'pw/widget_opening_time_view', 'widget-opening-time' ), array(
				'args' => $args,
				'instance' => $instance,
				'opening_times' => $opening_times,
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

			// title
			$instance['title'] = strip_tags( $new_instance['title'] );

			// days
			foreach ( $this->days as $day_label => $day ) {
				$instance[ $day_label . '_opened' ] = ! empty( $new_instance[ $day_label . '_opened' ] ) ? strip_tags( $new_instance[ $day_label . '_opened' ] ) : '';
				$instance[ $day_label . '_from' ]   = strip_tags( $new_instance[ $day_label . '_from' ] );
				$instance[ $day_label . '_to' ]     = strip_tags( $new_instance[ $day_label . '_to' ] );
			}

			// separator
			$instance['separator'] = $new_instance['separator'];
			// closed text
			$instance['closed_text'] = $new_instance['closed_text'];
			// additional info
			$instance['additional_info'] = $new_instance['additional_info'];

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
			if ( isset( $instance['title'] ) ) {
				$title = $instance['title'];
			}
			else {
				$title = esc_html__( 'Opening Time' , 'proteuswidgets' );
			}

			foreach ( $this->days as $day_label => $day ) {
				// opened/closed
				if ( isset( $instance[ $day_label . '_opened' ] ) ) {
					if ( '1' == $instance[ $day_label . '_opened' ] ) {
						$opened[ $day_label ] = 'checked="checked"';
					} else {
						$opened[ $day_label ] = '';
					}
				} else {
					$opened[ $day_label ] = 'checked="checked"';
				}
				// from time
				if ( isset( $instance[ $day_label . '_from' ] ) ) {
					$from[ $day_label ] = $instance[ $day_label . '_from' ];
				} else {
					$from[ $day_label ] = '8:00';
				}
				// to time
				if ( isset( $instance[ $day_label . '_to' ] ) ) {
					$to[ $day_label ] = $instance[ $day_label . '_to' ];
				} else {
					$to[ $day_label ] = '16:00';
				}
			}

			if ( isset( $instance['separator'] ) ) {
				$separator = $instance['separator'];
			}
			else {
				$separator = esc_html__( '-' , 'proteuswidgets' );
			}

			if ( isset( $instance['closed_text'] ) ) {
				$closed_text = $instance['closed_text'];
			}
			else {
				$closed_text = esc_html__( 'CLOSED' , 'proteuswidgets' );
			}

			if ( isset( $instance['additional_info'] ) ) {
				$additional_info = $instance['additional_info'];
			}
			else {
				$additional_info = '';
			}

			?>
			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title:' , 'proteuswidgets' ); ?></label>
				<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
			</p>
			<?php // days
			foreach ( $this->days as $day_label => $day ) : ?>
			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( $day_label . '_from' ) ); ?>"><b><?php echo esc_html( $day ); ?></b></label> <br />
				<input type="checkbox" id="<?php echo esc_attr( $this->get_field_id( $day_label . '_opened' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( $day_label . '_opened' ) ); ?>" value="1" <?php echo esc_attr( $opened[ $day_label ] ); ?> /> <?php esc_html_e( 'opened' , 'proteuswidgets' ); ?>
				<br />
				<input type="text" id="<?php echo esc_attr( $this->get_field_id( $day_label . '_from' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( $day_label . '_from' ) ); ?>" value="<?php echo esc_attr( $from[ $day_label ] ); ?>" size="5" /> <?php esc_html_e( 'to' , 'proteuswidgets' ) ?>
				<input type="text" id="<?php echo esc_attr( $this->get_field_id( $day_label . '_to' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( $day_label . '_to' ) ); ?>" value="<?php echo esc_attr( $to[ $day_label ] ) ?>" size="5" />
			</p>
			<?php endforeach; // end days ?>

			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'separator' ) ); ?>"><?php esc_html_e( 'Separator between hours:' , 'proteuswidgets' ); ?></label>
				<input id="<?php echo esc_attr( $this->get_field_id( 'separator' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'separator' ) ); ?>" type="text" value="<?php echo esc_attr( $separator ); ?>" />
			</p>

			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'closed_text' ) ); ?>"><?php esc_html_e( 'Text used for closed days:' , 'proteuswidgets' ); ?></label>
				<input id="<?php echo esc_attr( $this->get_field_id( 'closed_text' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'closed_text' ) ); ?>" type="text" value="<?php echo esc_attr( $closed_text ); ?>" />
			</p>

			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'additional_info' ) ); ?>"><?php esc_html_e( 'Text below the timetable for additional info (for example lunch time):' , 'proteuswidgets' ); ?></label>
				<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'additional_info' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'additional_info' ) ); ?>" type="text" value="<?php echo esc_attr( $additional_info ); ?>" />
			</p>

			<?php
		}

	}
}