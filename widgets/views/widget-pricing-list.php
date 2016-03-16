<?php echo $args['before_widget']; ?>

	<div class="pricing-list">
		<?php if ( ! empty( $instance['widget_title'] ) ) : ?>
			<div class="pricing-list__widget-title">
				<?php echo $args['before_title'] . wp_kses_post( $instance['widget_title'] ) . $args['after_title']; ?>
			</div>
		<?php endif; ?>
		<?php foreach ( $items as $item ) : ?>
			<div class="pricing-list__item">
				<?php if ( ! empty( $item['badge'] ) ) : ?>
					<span class="pricing-list__badge"><?php echo esc_html( $item['badge'] ); ?></span>
				<?php endif; ?>
				<span class="pricing-list__title"><?php echo esc_html( $item['title'] ); ?></span>
				<span class="pricing-list__price"><?php echo wp_kses( $item['price'], $allowed_html ); ?></span>
				<?php if ( ! empty( $item['description'] ) ) : ?>
					<p class="pricing-list__description">
						<?php echo wp_kses_post( $item['description'] ); ?>
					</p>
				<?php endif; ?>
			</div>
		<?php endforeach; ?>
	</div>

<?php echo $args['after_widget']; ?>
