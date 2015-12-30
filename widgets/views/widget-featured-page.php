<?php echo $args['before_widget']; ?>

	<div class="page-box  page-box--<?php echo esc_attr( $instance['layout'] ); ?>">
		<?php if ( has_post_thumbnail() ) : ?>
			<a class="page-box__picture" href="<?php the_permalink(); ?>"><?php the_post_thumbnail( $thumbnail_size ); ?></a>
		<?php endif; ?>
		<div class="page-box__content">
			<h5 class="page-box__title  text-uppercase"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h5>
			<p><?php echo esc_html( $excerpt ); ?></p>
			<?php if ( $is_block ) : ?>
				<p><a href="<?php the_permalink(); ?>" class="read-more  read-more--page-box"><?php echo esc_html( $instance['read_more_text'] ); ?></a></p>
			<?php endif; ?>
		</div>
	</div>

<?php echo $args['after_widget']; ?>
