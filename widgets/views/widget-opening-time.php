<?php echo $args['before_widget']; ?>

	<div class="time-table">

		<?php if ( ! empty( $instance['title'] ) ) : ?>
			<h3><span class="icon icons-ornament-left"></span><?php echo esc_html( $instance['title'] ); ?><span class="icon icons-ornament-right"></span></h3>
		<?php endif; ?>

		<div class="inner-bg">
			<?php foreach ( $opening_times as $line ) : ?>
				<dl class="week-day <?php echo esc_html( $line['class'] ); ?>">
					<dt><?php echo esc_html( $line['day'] ); ?></dt>
					<dd><?php echo esc_html( $line['day-time'] ); ?></dd>
				</dl>
			<?php endforeach; ?>
		</div>

		<?php if ( ! empty( $instance['additional_info'] ) ) : ?>
			<div class="additional-info"><?php echo esc_html( $instance['additional_info'] ); ?></div>
		<?php endif; ?>

	</div>

<?php echo $args['after_widget']; ?>
