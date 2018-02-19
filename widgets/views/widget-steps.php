<?php echo $args['before_widget']; ?>

	<?php if ( $instance['title_is_set'] ) : ?>
		<h3 class="widget-title  steps__title"><?php echo wp_kses_post( $instance['preped_title'] ); ?></h3>
	<?php endif; ?>

	<div class="steps">
		<?php foreach ( $items as $step ): ?>
			<div class="step">
				<div class="step__title">
					<i class="<?php echo esc_attr( PW_Functions::get_full_fa_class( $step['icon'] ) ); ?>"></i> <?php echo esc_html( $step['title'] ); ?>
				</div>
				<p class="step__content">
				<?php echo wp_kses_post( $step['content'] ); ?>
				</p>
				<div class="step__number">
					<?php echo esc_html( $step['step'] ); ?>
				</div>
			</div>
		<?php endforeach; ?>
	</div>

<?php echo $args['after_widget']; ?>
