<?php echo $args['before_widget']; ?>

	<a class="skype-button" href="<?php echo esc_attr( $instance['skype_username'] ); ?>">
		<i class="fa  fa-<?php echo esc_attr( $instance['icon'] ); ?>"></i>
		<p class="skype-button__title"><?php echo wp_kses_post( $instance['title'] ); ?></p>
	</a>

<?php echo $args['after_widget']; ?>
