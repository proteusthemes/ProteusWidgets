<?php echo $args['before_widget']; ?>

	<?php if ( ! empty( $instance['link'] ) ) : ?>
		<a class="banner" href="<?php echo esc_url( $instance['link'] ); ?>" target="<?php echo ( '1' == $instance['open_new'] ) ? '_blank' : '_self' ?>">
	<?php else : ?>
		<div class="banner">
	<?php endif; ?>

		<div class="banner__title">
			<?php echo esc_html( $instance['title'] ); ?>
		</div>
		<div class="banner__content">
			<?php echo esc_html( $instance['content'] ); ?>
		</div>

	<?php if ( ! empty( $instance['link'] ) ) : ?>
		</a>
	<?php else : ?>
		</div>
	<?php endif; ?>

<?php echo $args['after_widget']; ?>
