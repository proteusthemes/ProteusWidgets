<?php echo $args['before_widget']; ?>

	<?php foreach ( $instance['social_icons'] as $social_icon ) : ?>
		<a class="social-icons__link" href="<?php echo esc_url( $social_icon['link'] ); ?>" target="<?php echo esc_attr( $instance['target'] ); ?>"><i class="fa  <?php echo esc_attr( $social_icon['icon'] ); ?>" aria-hidden="true"></i><span class="screen-reader-text"><?php echo esc_html( PW_Functions::get_social_network_name( $social_icon['icon'], $social_icon['link'] ) ); ?></span></a>
	<?php endforeach; ?>

<?php echo $args['after_widget']; ?>
