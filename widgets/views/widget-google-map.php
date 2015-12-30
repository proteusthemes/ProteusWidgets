<?php echo $args['before_widget']; ?>

	<div
		class="simple-map  js-where-we-are"
		data-latlng="<?php echo esc_attr( $instance['latLng'] ); ?>"
		data-markers="<?php echo esc_attr( $instance['locations'] ); ?>"
		data-zoom="<?php echo absint( $instance['zoom'] ); ?>"
		data-type="<?php echo esc_attr( $instance['type'] ); ?>"
		data-style="<?php echo esc_attr( $instance['style'] ); ?>"
		style="height: <?php echo absint( $instance['height'] ); ?>px;"
	></div>

<?php echo $args['after_widget']; ?>
