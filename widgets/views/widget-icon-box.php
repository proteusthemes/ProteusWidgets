<?php echo $args['before_widget']; ?>

	<?php if ( ! empty( $instance['btn_link'] ) ) : ?>
		<a class="icon-box" href="<?php echo esc_url( $instance['btn_link'] ); ?>" target="<?php echo esc_attr( $instance['target'] ); ?>">
	<?php else : ?>
		<div class="icon-box">
	<?php endif; ?>

		<i class="fa  <?php echo esc_attr( $instance['icon'] ); ?>  fa-3x"></i>
		<div class="icon-box__text">
			<h4 class="icon-box__title"><?php echo wp_kses_post( $instance['title'] ); ?></h4>
			<span class="icon-box__subtitle"><?php echo wp_kses_post( $instance['text'] ); ?></span>
		</div>

	<?php if ( ! empty( $instance['btn_link'] ) ) : ?>
		</a>
	<?php else : ?>
		</div>
	<?php endif; ?>

<?php echo $args['after_widget']; ?>
