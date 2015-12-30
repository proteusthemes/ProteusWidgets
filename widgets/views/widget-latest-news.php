<?php echo $args['before_widget']; ?>

	<?php if ( $instance['block'] ) : ?>
		<?php foreach ( $posts as $post ): ?>
			<a href="<?php echo esc_url( $post['link'] ); ?>" class="latest-news  latest-news--<?php echo esc_attr( $instance['type'] ); ?>">
				<div class="latest-news__date">
					<div class="latest-news__date__month">
						<?php echo esc_html( $post['month'] ); ?>
					</div>
					<div class="latest-news__date__day">
						<?php echo esc_html( $post['day'] ); ?>
					</div>
				</div>
				<?php if ( isset( $post['image_url'] ) ) : ?>
				<div class="latest-news__image">
					<img src="<?php echo esc_url( $post['image_url'] ); ?>" width="<?php echo esc_attr( $post['image_width'] ); ?>" height="<?php echo esc_attr( $post['image_height'] ); ?>" srcset="<?php echo esc_attr( $post['srcset'] ); ?>" sizes="(min-width: 781px) 360px, calc(100vw - 30px)" class="  wp-post-image" alt="<?php echo esc_attr( $post['title'] ); ?>">
				</div>
				<?php endif; ?>

				<div class="latest-news__content">
					<h4 class="latest-news__title"><?php echo wp_kses_post( $post['title'] ); ?></h4>
					<div class="latest-news__author">
						<?php echo esc_html( $text['by'] ) . esc_html( $post['author'] ); ?>
					</div>
				</div>
			</a>
		<?php endforeach; ?>
		<?php if ( ! empty( $instance['more_news'] ) ) : ?>
			<a href="<?php echo esc_url( $instance['link_to_more_news'] ); ?>" class="latest-news  latest-news--more-news">
				<?php echo esc_html( $text['more_news'] ); ?>
			</a>
		<?php endif; ?>
	<?php else : ?>
		<?php foreach ( $posts as $post ): ?>
			<a href="<?php echo esc_url( $post['link'] ); ?>" class="latest-news  latest-news--<?php echo esc_attr( $instance['type'] ); ?>">
				<div class="latest-news__content">
					<h4 class="latest-news__title"><?php echo wp_kses_post( $post['title'] ); ?></h4>
						<div class="latest-news__author">
							<?php echo esc_html( $text['by'] ) . esc_html( $post['author'] ); ?>
						</div>
				</div>
			</a>
		<?php endforeach; ?>

		<?php if ( ! empty( $instance['more_news'] ) ) : ?>
			<a href="<?php echo esc_url( $instance['link_to_more_news'] ); ?>" class="latest-news  latest-news--more-news">
				<?php echo esc_html( $text['more_news'] ); ?>
			</a>
		<?php endif; ?>
	<?php endif; ?>

<?php echo $args['after_widget']; ?>
