<?php echo $this->e( $args['before_widget'] ) ?>

	<a class="skype-button" href="<?php echo $this->e( $instance['skype_username'] ) ?>">
		<i class="fa  fa-<?php echo $this->e( $instance['icon'] ) ?>"></i>
		<p class="skype-button__title"><?php echo $this->e( $instance['title'] ) ?></p>
	</a>

<?php echo $this->e( $args['after_widget'] ) ?>