<?php echo $this->args['before_widget'] ?>

	<a class="skype-button" href="<?php echo $this->e( $instance['skype_username'], 'esc_attr' ) ?>">
		<i class="fa  fa-<?php echo $this->e( $instance['icon'], 'esc_attr' ) ?>"></i>
		<p class="skype-button__title"><?php echo $this->e( $instance['title'], 'wp_kses_post' ) ?></p>
	</a>

<?php echo $this->args['after_widget'] ?>