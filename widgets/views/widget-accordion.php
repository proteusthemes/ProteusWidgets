<?php
	echo $args['before_widget'];

	if ( ! empty( $instance['title'] ) ) {
		echo $args['before_title'] . wp_kses_post( $instance['preped_title'] ) . $args['after_title'];
	}
?>
	<div class="accordion panel-group" id="accordion-<?php echo esc_attr( $args['widget_id'] ); ?>">
	<?php foreach ( $items as $item ) : ?>
		<div class="accordion__panel  panel  panel-default">
			<div class="accordion__heading  panel-heading" id="heading-<?php echo esc_attr( $args['widget_id'] ) . '-' . esc_attr( $item['id'] ); ?>">
				<h4 class="panel-title">
					<button type="button" class="accordion__toggle  collapsed" data-toggle="collapse" data-parent="#accordion-<?php echo esc_attr( $args['widget_id'] ); ?>" data-target="#collapse-<?php echo esc_attr( $args['widget_id'] ) . '-' . esc_attr( $item['id'] ); ?>" aria-expanded="false" aria-controls="collapse-<?php echo esc_attr( $args['widget_id'] ) . '-' . esc_attr( $item['id'] ); ?>">
						<?php echo wp_kses_post( $item['title'] ); ?>
					</button>
				</h4>
			</div>
			<div id="collapse-<?php echo esc_attr( $args['widget_id'] ) . '-' . esc_attr( $item['id'] ); ?>" class="accordion__content  panel-collapse  collapse" role="region" aria-labelledby="heading-<?php echo esc_attr( $args['widget_id'] ) . '-' . esc_attr( $item['id'] ); ?>">
				<div class="panel-body">
					<?php echo wp_kses_post( $item['content'] ); ?>
				</div>
			</div>
		</div>
	<?php endforeach; ?>
	<?php if ( ! empty( $instance['read_more_link'] ) ) : ?>
		<a href="<?php echo esc_url( $instance['read_more_link'] ); ?>" class="more-link"><?php echo esc_html( $text['read_more'] ); ?></a>
	<?php endif; ?>
	</div>

<?php echo $args['after_widget']; ?>
