<?php echo $args['before_widget']; ?>

	<a class="skype-button" href="<?php echo esc_attr( $instance['skype_username'] ); ?>">
		<i class="<?php echo esc_attr( PW_Functions::get_full_fa_class( $instance['icon'] ) ); ?>"></i>
		<p class="skype-button__title"><?php echo wp_kses_post( $instance['title'] ); ?></p>
	</a>

<?php echo $args['after_widget']; ?>
