<?php echo $args['before_widget']; ?>

	<div class="number-counters" data-speed="<?php echo esc_attr( $instance['speed'] ); ?>">
	<?php foreach ( $counters as $counter ) : ?>
		<div class="number-counter">
			<?php if ( ! empty( $counter['icon'] ) ) : ?>
				<i class="number-counter__icon  fa  <?php echo esc_attr( $counter['icon'] ); ?>"></i>
			<?php endif; ?>
			<div class="number-counter__number  js-number" data-to="<?php echo absint( $counter['number'] ); ?>"><?php echo esc_html( PW_Functions::leading_zeros( strlen( $counter['number'] ) ) ); ?></div>
			<div class="number-counter__title"><?php echo esc_html( $counter['title'] ); ?></div>
		</div>
	<?php endforeach; ?>
	</div>

<?php echo $args['after_widget']; ?>
