<?php
	echo $args['before_widget'];

	if ( ! empty( $instance['title'] ) ) {
		echo $args['before_title'] . esc_html( $instance['preped_title'] ) . $args['after_title'];
	}
?>
	<div class="accordion panel-group" id="accordion-<?php echo esc_attr( $args['widget_id'] ); ?>" role="tablist" aria-multiselectable="true">
	<?php foreach ( $items as $item ) : ?>
		<div class="accordion__panel  panel  panel-default">
			<div class="accordion__heading  panel-heading" role="tab" id="heading-<?php echo esc_attr( $args['widget_id'] ) . '-' . esc_attr( $item['id'] ); ?>">
				<h4 class="panel-title">
					<a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion-<?php echo esc_attr( $args['widget_id'] ); ?>" href="#collapse-<?php echo esc_attr( $args['widget_id'] ) . '-' . esc_attr( $item['id'] ); ?>" aria-expanded="false" aria-controls="collapse-<?php echo esc_attr( $args['widget_id'] ) . '-' . esc_attr( $item['id'] ); ?>">
						<?php echo wp_kses_post( $item['title'] ); ?>
					</a>
				</h4>
			</div>
			<div id="collapse-<?php echo esc_attr( $args['widget_id'] ) . '-' . esc_attr( $item['id'] ); ?>" class="accordion__content  panel-collapse  collapse" role="tabpanel" aria-labelledby="heading-<?php echo esc_attr( $args['widget_id'] ) . '-' . esc_attr( $item['id'] ); ?>">
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
