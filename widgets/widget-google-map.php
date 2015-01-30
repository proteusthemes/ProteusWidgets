<?php
/**
 * Google map for the page builder
 *
 * @package ProteusWidgets
 * @since 0.1.0
 */

if ( ! class_exists( 'PW_Google_Map' ) ) {
	class PW_Google_Map extends WP_Widget {

		private $map_styles = array(
			'Default'          => '[]',
			'Subtle Grayscale' => '[{"featureType":"landscape","stylers":[{"saturation":-100},{"lightness":65},{"visibility":"on"}]},{"featureType":"poi","stylers":[{"saturation":-100},{"lightness":51},{"visibility":"simplified"}]},{"featureType":"road.highway","stylers":[{"saturation":-100},{"visibility":"simplified"}]},{"featureType":"road.arterial","stylers":[{"saturation":-100},{"lightness":30},{"visibility":"on"}]},{"featureType":"road.local","stylers":[{"saturation":-100},{"lightness":40},{"visibility":"on"}]},{"featureType":"transit","stylers":[{"saturation":-100},{"visibility":"simplified"}]},{"featureType":"administrative.province","stylers":[{"visibility":"off"}]},{"featureType":"water","elementType":"labels","stylers":[{"visibility":"on"},{"lightness":-25},{"saturation":-100}]},{"featureType":"water","elementType":"geometry","stylers":[{"hue":"#ffff00"},{"lightness":-25},{"saturation":-97}]}]',
			'Pale Dawn'        => '[{"featureType":"water","stylers":[{"visibility":"on"},{"color":"#acbcc9"}]},{"featureType":"landscape","stylers":[{"color":"#f2e5d4"}]},{"featureType":"road.highway","elementType":"geometry","stylers":[{"color":"#c5c6c6"}]},{"featureType":"road.arterial","elementType":"geometry","stylers":[{"color":"#e4d7c6"}]},{"featureType":"road.local","elementType":"geometry","stylers":[{"color":"#fbfaf7"}]},{"featureType":"poi.park","elementType":"geometry","stylers":[{"color":"#c5dac6"}]},{"featureType":"administrative","stylers":[{"visibility":"on"},{"lightness":33}]},{"featureType":"road"},{"featureType":"poi.park","elementType":"labels","stylers":[{"visibility":"on"},{"lightness":20}]},{},{"featureType":"road","stylers":[{"lightness":20}]}]',
			'Blue Water'       => '[{"featureType":"water","stylers":[{"color":"#46bcec"},{"visibility":"on"}]},{"featureType":"landscape","stylers":[{"color":"#f2f2f2"}]},{"featureType":"road","stylers":[{"saturation":-100},{"lightness":45}]},{"featureType":"road.highway","stylers":[{"visibility":"simplified"}]},{"featureType":"road.arterial","elementType":"labels.icon","stylers":[{"visibility":"off"}]},{"featureType":"administrative","elementType":"labels.text.fill","stylers":[{"color":"#444444"}]},{"featureType":"transit","stylers":[{"visibility":"off"}]},{"featureType":"poi","stylers":[{"visibility":"off"}]}]',
			'Gowalla'          => '[{"featureType":"road","elementType":"labels","stylers":[{"visibility":"simplified"},{"lightness":20}]},{"featureType":"administrative.land_parcel","elementType":"all","stylers":[{"visibility":"off"}]},{"featureType":"landscape.man_made","elementType":"all","stylers":[{"visibility":"off"}]},{"featureType":"transit","elementType":"all","stylers":[{"visibility":"off"}]},{"featureType":"road.local","elementType":"labels","stylers":[{"visibility":"simplified"}]},{"featureType":"road.local","elementType":"geometry","stylers":[{"visibility":"simplified"}]},{"featureType":"road.highway","elementType":"labels","stylers":[{"visibility":"simplified"}]},{"featureType":"poi","elementType":"labels","stylers":[{"visibility":"off"}]},{"featureType":"road.arterial","elementType":"labels","stylers":[{"visibility":"off"}]},{"featureType":"water","elementType":"all","stylers":[{"hue":"#a1cdfc"},{"saturation":30},{"lightness":49}]},{"featureType":"road.highway","elementType":"geometry","stylers":[{"hue":"#f49935"}]},{"featureType":"road.arterial","elementType":"geometry","stylers":[{"hue":"#fad959"}]}]',
		);

		/**
		 * Register widget with WordPress.
		 */
		public function __construct() {
			parent::__construct(
				false, // ID, auto generate when false
				_x( 'ProteusThemes: Google Map', 'backend', 'proteuswidgets' ), // Name
				array(
					'description' => _x( 'For use in the page builder', 'backend', 'proteuswidgets' )
				)
			);
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
			extract( $instance );

			$locations = json_encode( array_values( $locations ) );

			?>

			<?php echo $before_widget; ?>
				<div
					class="simple-map  js-where-we-are"
					data-latlng="<?php echo esc_attr( $latLng ); ?>"
					data-markers="<?php echo esc_attr( $locations ); ?>"
					data-zoom="<?php echo absint( $zoom ); ?>"
					data-type="<?php echo esc_attr( $type ); ?>"
					data-style="<?php echo esc_attr( $this->map_styles[$style] ); ?>"
					style="height: <?php echo absint( $height ); ?>px;"
				></div>
			<?php echo $after_widget; ?>

			<?php
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

			$instance['latLng'] = wp_kses_post( $new_instance['latLng'] );
			$instance['zoom']   = absint( $new_instance['zoom'] );
			$instance['type']   = sanitize_key( $new_instance['type'] );
			$instance['style']  = wp_kses_post( $new_instance['style'] );
			$instance['height'] = absint( $new_instance['height'] );

			$instance['locations'] = $new_instance['locations'];

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
			$latLng = isset( $instance['latLng'] ) ? $instance['latLng'] : '51.507331,-0.127668';
			$zoom   = isset( $instance['zoom'] ) ? $instance['zoom'] : 12;
			$type   = isset( $instance['type'] ) ? $instance['type'] : 'roadmap';
			$style  = isset( $instance['style'] ) ? $instance['style'] : 'Subtle Grayscale';
			$height = isset( $instance['height'] ) ? $instance['height'] : 380;

			$locations = isset( $instance['locations'] ) ? array_values( $instance['locations'] ) : array(
				array(
					'id'             => 1,
					'title'          => 'London',
					'locationlatlng' => '51.507331,-0.127668',
					'custompinimage' => '',
				)
			);

			$map_types = array( 'roadmap', 'satellite', 'hybrid', 'terrain' );

			?>

			<p>
				<label for="<?php echo $this->get_field_id( 'latLng' ); ?>"><?php _ex( 'Latitude and longitude of the map center:', 'backend', 'proteuswidgets' ); ?></label> <br>
				<small><?php printf( _x( "Get this from %s (right click on map and select What's here?) or %s. Latitude and longitude separated by comma.", 'backend', 'proteuswidgets' ), '<a href="https://maps.google.com/" target="_blank">Google Maps</a>', '<a href="http://www.findlatitudeandlongitude.com/" target="_blank">this site</a>' ); ?></small>
				<input class="widefat" id="<?php echo $this->get_field_id( 'latLng' ); ?>" name="<?php echo $this->get_field_name( 'latLng' ); ?>" value="<?php echo esc_attr( $latLng ); ?>" />
			</p>

			<p>
				<label for="<?php echo $this->get_field_id( 'zoom' ); ?>"><?php _ex( 'Zoom (more is closer view):', 'backend', 'proteuswidgets' ); ?></label>
				<select class="widefat" id="<?php echo $this->get_field_id( 'zoom' ); ?>" name="<?php echo $this->get_field_name( 'zoom' ); ?>">
				<?php for ( $i=1; $i < 25; $i++ ) : ?>
					<option value="<?php echo $i; ?>" <?php selected( $zoom, $i ); ?>><?php echo $i; ?></option>
				<?php endfor; ?>
				</select>
			</p>

			<p>
				<label for="<?php echo $this->get_field_id( 'type' ); ?>"><?php _ex( 'Type:', 'backend', 'proteuswidgets' ); ?></label>
				<select class="widefat" id="<?php echo $this->get_field_id( 'type' ); ?>" name="<?php echo $this->get_field_name( 'type' ); ?>">
				<?php foreach ( $map_types as $map_type ) : ?>
					<option value="<?php echo $map_type; ?>" <?php selected( $type, $map_type ); ?>><?php echo ucfirst( $map_type ); ?></option>
				<?php endforeach; ?>
				</select>
			</p>

			<p>
				<label for="<?php echo $this->get_field_id( 'style' ); ?>"><?php _ex( 'Style:', 'backend', 'proteuswidgets' ); ?></label>
				<select class="widefat" id="<?php echo $this->get_field_id( 'style' ); ?>" name="<?php echo $this->get_field_name( 'style' ); ?>">
				<?php foreach ( $this->map_styles as $style_name => $val ) : ?>
					<option value="<?php echo $style_name; ?>" <?php selected( $style, $style_name ); ?>><?php echo $style_name; ?></option>
				<?php endforeach; ?>
				</select>
			</p>

			<p>
				<label for="<?php echo $this->get_field_id( 'height' ); ?>"><?php _ex( 'Height of map (in pixels):', 'backend', 'proteuswidgets' ); ?></label>
				<input class="widefat" id="<?php echo $this->get_field_id( 'height' ); ?>" name="<?php echo $this->get_field_name( 'height' ); ?>" type="number" min="0" step="20" value="<?php echo esc_attr( $height ); ?>" />
			</p>


			<h4><?php _ex( 'Locations:', 'backend', 'proteuswidgets' ); ?></h4>

			<script type="text/template" id="js-pt-location-<?php echo $this->id; ?>">
				<p>
					<label for="<?php echo $this->get_field_id( 'locations' ); ?>-<%- id %>-title"><?php _ex( 'Title of location:', 'backend', 'proteuswidgets' ); ?></label> <br>
					<small><?php _ex( 'This is shown on pin mouse hover.', 'backend', 'proteuswidgets' ); ?></small>
					<input class="widefat" id="<?php echo $this->get_field_id( 'locations' ); ?>-<%- id %>-title" name="<?php echo $this->get_field_name( 'locations' ); ?>[<%- id %>][title]" type="text" value="<%- title %>" />
				</p>

				<p>
					<label for="<?php echo $this->get_field_id( 'locations' ); ?>-<%- id %>-locationlatlng"><?php _ex( 'Latitude and longitude of this location:', 'backend', 'proteuswidgets' ); ?></label> <br>
					<small><?php _ex( 'The same format as above for the map center.', 'backend', 'proteuswidgets' ); ?></small>
					<input class="widefat" id="<?php echo $this->get_field_id( 'locations' ); ?>-<%- id %>-locationlatlng" name="<?php echo $this->get_field_name( 'locations' ); ?>[<%- id %>][locationlatlng]" type="text" placeholder="40.724885,-74.00264" value="<%- locationlatlng %>" />
				</p>

				<p>
					<label for="<?php echo $this->get_field_id( 'locations' ); ?>-<%- id %>-custompinimage"><?php _ex( 'Custom pin icon URL:', 'backend', 'proteuswidgets' ); ?></label>
					<input class="widefat" id="<?php echo $this->get_field_id( 'locations' ); ?>-<%- id %>-custompinimage" name="<?php echo $this->get_field_name( 'locations' ); ?>[<%- id %>][custompinimage]" type="text" value="<%- custompinimage %>" />
				</p>

				<p>
					<input name="<?php echo $this->get_field_name( 'locations' ); ?>[<%- id %>][id]" type="hidden" value="<%- id %>" />
					<a href="#" class="pt-remove-location  js-pt-remove-location"><span class="dashicons dashicons-dismiss"></span> <?php _ex( 'Remove Location', 'backend', 'proteuswidgets' ); ?></a>
				</p>
			</script>
			<div class="pt-widget-locations" id="locations-<?php echo $this->id; ?>">
				<div class="locations"></div>
				<p>
					<a href="#" class="button  js-pt-add-location">Add New Location</a>
				</p>
			</div>
			<script type="text/javascript">
				// repopulate the form
				var locationsJSON = <?php echo json_encode( $locations ) ?>;

				if ( _.isFunction( repopulateLocations ) ) {
					repopulateLocations( locationsJSON, '<?php echo $this->id; ?>' );
				}
			</script>

			<?php
		}

	} // class PW_Google_Map
	register_widget( 'PW_Google_Map' );
}