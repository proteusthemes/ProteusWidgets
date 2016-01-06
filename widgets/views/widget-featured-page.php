<?php echo $args['before_widget']; ?>

	<div class="page-box  page-box--<?php echo esc_attr( $instance['layout'] ); ?>">
		<?php if ( has_post_thumbnail( $page['ID'] ) ) : ?>
			<?php if ( 'block' === $instance['layout'] ) : ?>
				<a class="page-box__picture" href="<?php echo esc_url( $page['link'] ); ?>">
					<img src="<?php echo esc_url( $page['image_url'] ); ?>" width="<?php echo esc_attr( $page['image_width'] ); ?>" height="<?php echo esc_attr( $page['image_height'] ); ?>" srcset="<?php echo esc_attr( $page['srcset'] ); ?>" sizes="(min-width: 992px) 360px, calc(100vw - 30px)" class="wp-post-image" alt="<?php echo esc_attr( $page['post_title'] ); ?>">
				</a>
			<?php else : ?>
				<a class="page-box__picture" href="<?php echo esc_url( $page['link'] ); ?>"><?php echo get_the_post_thumbnail( $page['ID'], $thumbnail_size, array( 'srcset' => '', 'sizes' => '100px' ) ); ?></a>
			<?php endif; ?>
		<?php endif; ?>
		<div class="page-box__content">
			<h5 class="page-box__title text-uppercase"><a href="<?php echo esc_url( $page['link'] ); ?>"><?php echo wp_kses_post( $page['post_title'] ); ?></a></h5>
			<p><?php echo wp_strip_all_tags( $page['post_excerpt'] ); ?></p>
			<?php if ( 'block' === $instance['layout'] ) : ?>
				<p><a href="<?php echo esc_url( $page['link'] ); ?>" class="read-more  read-more--page-box"><?php echo esc_html( $instance['read_more_text'] ); ?></a></p>
			<?php endif; ?>
		</div>
	</div>

<?php echo $args['after_widget']; ?>
