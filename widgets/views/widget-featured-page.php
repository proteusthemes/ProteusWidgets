<?php echo $args['before_widget']; ?>

	<div class="page-box  page-box--<?php echo esc_attr( $instance['layout'] ); ?>">
		<?php
		if ( $page['thumbnail'] ) :
			if ( 'block' === $instance['layout'] ) :
		?>
			<a class="page-box__picture" href="<?php echo esc_url( $page['link'] ); ?>">
				<img src="<?php echo esc_url( $page['image_url'] ); ?>" width="<?php echo esc_attr( $page['image_width'] ); ?>" height="<?php echo esc_attr( $page['image_height'] ); ?>" srcset="<?php echo esc_attr( $page['srcset'] ); ?>" sizes="(min-width: 992px) 360px, calc(100vw - 30px)" class="wp-post-image" alt="<?php echo esc_attr( $page['post_title'] ); ?>">
			</a>
		<?php else : ?>
			<a class="page-box__picture" href="<?php echo esc_url( $page['link'] ); ?>"><?php echo $page['thumbnail']; ?></a>
		<?php
			endif;
		endif;
		?>
		<div class="page-box__content">
			<h4 class="page-box__title"><a href="<?php echo esc_url( $page['link'] ); ?>"><?php echo wp_kses_post( $page['post_title'] ); ?></a></h4>
			<p class="page-box__text"><?php echo wp_kses_post( $page['post_excerpt'] ); ?></p>
			<?php if ( 'block' === $instance['layout'] ) : ?>
				<a href="<?php echo esc_url( $page['link'] ); ?>" class="page-box__more-link"><?php echo esc_html( $instance['read_more_text'] ); ?></a>
			<?php endif; ?>
		</div>
	</div>

<?php echo $args['after_widget']; ?>