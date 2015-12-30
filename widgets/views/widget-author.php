<?php echo $args['before_widget']; ?>

	<div class="widget-author__image-container">
		<div class="widget-author__avatar--blurred">
			<?php echo wp_kses_post( $author_avatar ); ?>
		</div>
		<a href="<?php echo esc_url( $author_posts ); ?>" class="widget-author__avatar">
			<?php echo wp_kses_post( $author_avatar ); ?>
		</a>
	</div>
	<div class="row widget-author__content">
		<div class="col-xs-10  col-xs-offset-1">
			<?php echo wp_kses_post( $author_meta_name ); ?>
			<?php echo esc_html( $author_meta_description ); ?>

			<?php if ( ! empty( $author_meta_user_url ) ) : ?>
				<p>
					<a href="<?php echo esc_url( $author_meta_user_url ); ?>"><?php echo wp_strip_all_tags( $author_meta_user_url ); ?></a>
				</p>
			<?php endif; ?>

			<?php if ( ! empty( $social_icons ) ) : ?>
				<p class="social-icons__author">
					<?php foreach ( $social_icons as $item ) : ?>
						<a href="<?php echo esc_url( $item['url'] ); ?>" class="social-icons__container">
							<i class="fa fa-<?php echo esc_attr( $item['icon'] ); ?>"></i>
						</a>
					<?php endforeach; ?>
				</p>
			<?php endif; ?>
		</div>
	</div>

<?php echo $args['after_widget']; ?>
