<?php
	echo $args['before_widget'];

	if ( ! empty( $instance['title'] ) ) {
		echo $args['before_title'] . esc_html( $instance['preped_title'] ) . $args['after_title'];
	}
?>

	<div class="iframe-like-box">
		<iframe src="//www.facebook.com/plugins/likebox.php?<?php echo esc_attr( $http_query ); ?>" frameborder="0"></iframe>
	</div>

	<style type="text/css">
		.iframe-like-box > iframe { min-height: <?php echo absint( $instance['height'] ); ?>px; width: <?php echo absint( $instance['width'] ); ?>px; }
	</style>

<?php echo $args['after_widget']; ?>
