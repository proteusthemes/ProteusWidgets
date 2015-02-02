<?php
/**
 * Opening Time Widget
 *
 * @package ProteusWidgets
 * @since 0.1.0
 */


/**************************************
 * Opening Time Widget
 * -----------------------------------
 * Adds the opening time, suitable for the sidebar or used above the slider
 **************************************/

if ( ! class_exists( 'PW_Opening_Time' ) ) {
	class PW_Opening_Time extends WP_Widget {

		/**
		 * Days of the week, needed for display and $instance variable
		 */
		private $days;

		/**
		 * Register widget with WordPress.
		 */
		public function __construct() {
			parent::__construct(
				false, // ID, auto generate when false
				sprintf( 'ProteusThemes: %s', __( 'Opening Time', 'proteusthemes' ) ), // Name
				array(
					'description' => __( 'Opening Time Widget for placing it into the sidebar or above the slider' , 'proteusthemes'),
					'classname' => 'opening-time'
				)
			);

			// set the right order of the days
			$start_of_week = get_option( 'start_of_week ' ); // integer [0,6], 0 = Sunday, 1 = Monday ...
			$this->days = array(
				'Sun' => __( 'Sunday', 'proteusthemes' ),
				'Mon' => __( 'Monday', 'proteusthemes' ),
				'Tue' => __( 'Tuesday', 'proteusthemes' ),
				'Wed' => __( 'Wednesday', 'proteusthemes' ),
				'Thu' => __( 'Thursday', 'proteusthemes' ),
				'Fri' => __( 'Friday', 'proteusthemes' ),
				'Sat' => __( 'Saturday', 'proteusthemes' ),
			);

			$this->rotate_days( $start_of_week );
		}

		/**
		 * Rotate the array for a given number of times
		 * @param  int $num shift the array for this number
		 * @return void
		 */
		private function rotate_days( $num ) {
			for ( $i=0; $i < $num; $i++ ) {
				$keys = array_keys( $this->days );
				$val  = $this->days[$keys[0]];
				unset( $this->days[$keys[0]] );
				$this->days[$keys[0]] = $val;
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
			extract( $args );
			$title = $instance['title'];

			$out = "";


			$out .= $before_widget;
			$out .= '<div class="time-table">' . "\n";

			if ( ! empty( $title ) ) {

				$out .= '<h3><span class="icon icons-ornament-left"></span> ' . apply_filters( 'widget_title', $title, $instance, $this->id_base ) . ' <span class="icon icons-ornament-right"></span></h3>';
			}
			$out .= '<div class="inner-bg">' . "\n";
			$current_time = intval( time() + ( (double)get_option('gmt_offset') * 3600 ) );

			$i = 0;
			foreach( $this->days as $day_label => $day ) {
				$class = $i%2==0 ? "" : " light-bg";

				if ( "1" != $instance[$day_label . '_opened'] ) {
					$class .= " closed";
				}

				if ( date( 'D', $current_time ) == $day_label ) {
					$class .= " today";
				}

				$out .= '<dl class="week-day' . $class . '">' . "\n";
				$out .= '<dt>' . $day . '</dt>' . "\n";
				if ( "1" == $instance[$day_label . '_opened'] ) {
					$out .= '<dd>' . $instance[$day_label . '_from'] . $instance['separator'] . $instance[$day_label . '_to'] . '</dd>' . "\n";
				} else {
					$out .= '<dd>' . $instance['closed_text'] . '</dd>' . "\n";
				}


				$out .= '</dl>' . "\n";
				$i++;
			}


			$out .= '</div>' . "\n"; // .inner-bg

			if ( ! empty( $instance['additional_info'] ) ) {
				$out .= '<div class="additional-info">' . $instance['additional_info'] . '</div>' . "\n"; // .inner-bg
			}

			$out .= '</div>' . "\n"; // .time-table
			$out .= $after_widget;


			echo $out;
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
			foreach( $this->days as $day_label => $day ) {
				$instance[$day_label . '_opened'] = strip_tags( $new_instance[$day_label . '_opened'] );
				$instance[$day_label . '_from'] = strip_tags( $new_instance[$day_label . '_from'] );
				$instance[$day_label . '_to'] = strip_tags( $new_instance[$day_label . '_to'] );
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
			if ( isset( $instance[ 'title' ] ) ) {
				$title = $instance[ 'title' ];
			}
			else {
				$title = __( 'Opening Time' , 'proteusthemes');
			}

			foreach ( $this->days as $day_label => $day ) {
				// opened/closed
				if ( isset( $instance[$day_label . '_opened'] ) ) {
					if ( "1" == $instance[$day_label . '_opened'] ) {
						$opened[$day_label] = 'checked="checked"';
					} else {
						$opened[$day_label] = '';
					}
				} else {
					$opened[$day_label] = 'checked="checked"';
				}
				// from time
				if ( isset( $instance[$day_label . '_from'] ) ) {
					$from[$day_label] = $instance[$day_label . '_from'];
				} else {
					$from[$day_label] = "8:00";
				}
				// to time
				if ( isset( $instance[$day_label . '_to'] ) ) {
					$to[$day_label] = $instance[$day_label . '_to'];
				} else {
					$to[$day_label] = "16:00";
				}
			}

			if ( isset( $instance[ 'separator' ] ) ) {
				$separator = $instance[ 'separator' ];
			}
			else {
				$separator = __( '-' , 'proteusthemes');
			}

			if ( isset( $instance[ 'closed_text' ] ) ) {
				$closed_text = $instance[ 'closed_text' ];
			}
			else {
				$closed_text = __( 'CLOSED' , 'proteusthemes');
			}

			if ( isset( $instance[ 'additional_info' ] ) ) {
				$additional_info = $instance[ 'additional_info' ];
			}
			else {
				$additional_info = '';
			}

			?>
			<p>
				<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' , 'proteusthemes'); ?></label>
				<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
			</p>
			<?php // days
			foreach ( $this->days as $day_label => $day ) : ?>
			<p>
				<label for="<?php echo $this->get_field_id( $day_label . '_from' ); ?>"><b><?php echo $day; ?></b></label> <br />
				<input type="checkbox" id="<?php echo $this->get_field_id( $day_label . '_opened' ) ?>" name="<?php echo $this->get_field_name( $day_label . '_opened' ); ?>" value="1" <?php echo $opened[$day_label]; ?> /> <?php _e( 'opened' , 'proteusthemes'); ?>
				<br />
				<input type="text" id="<?php echo $this->get_field_id( $day_label . '_from' ) ?>" name="<?php echo $this->get_field_name( $day_label . '_from' ); ?>" value="<?php echo esc_attr( $from[$day_label] ); ?>" size="5" /> <?php _e( "to" , 'proteusthemes') ?>
				<input type="text" id="<?php echo $this->get_field_id( $day_label . '_to' ) ?>" name="<?php echo $this->get_field_name( $day_label . '_to' ); ?>" value="<?php echo esc_attr( $to[$day_label] ) ?>" size="5" />
			</p>
			<?php endforeach; // end days ?>

			<p>
				<label for="<?php echo $this->get_field_id( 'separator' ); ?>"><?php _e( 'Separator between hours' , 'proteusthemes'); ?>:</label>
				<input id="<?php echo $this->get_field_id( 'separator' ); ?>" name="<?php echo $this->get_field_name( 'separator' ); ?>" type="text" value="<?php echo $separator; ?>" />
			</p>

			<p>
				<label for="<?php echo $this->get_field_id( 'closed_text' ); ?>"><?php _e( 'Text used for closed days' , 'proteusthemes'); ?>:</label>
				<input id="<?php echo $this->get_field_id( 'closed_text' ); ?>" name="<?php echo $this->get_field_name( 'closed_text' ); ?>" type="text" value="<?php echo esc_attr( $closed_text ); ?>" />
			</p>

			<p>
				<label for="<?php echo $this->get_field_id( 'additional_info' ); ?>"><?php _e( 'Text below the timetable for additional info (for example lunch time)' , 'proteusthemes'); ?>:</label>
				<input class="widefat" id="<?php echo $this->get_field_id( 'additional_info' ); ?>" name="<?php echo $this->get_field_name( 'additional_info' ); ?>" type="text" value="<?php echo esc_attr( $additional_info ); ?>" />
			</p>

			<?php
		}

	} // class PW_Opening_Time
	register_widget( 'PW_Opening_Time' );
}