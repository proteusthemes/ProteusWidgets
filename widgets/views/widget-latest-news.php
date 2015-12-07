<?php echo $args['before_widget']; ?>

	<?php if ( $instance['block'] ) : ?>
		<?php foreach ( $posts as $post ): ?>
			<div class="card  latest-news  latest-news--<?php echo esc_attr( $instance['type'] ); ?>">
				<?php if ( isset( $post['image_url'] ) ) : ?>
					<a href="<?php echo esc_url( $post['link'] ); ?>" class="latest-news__image">
						<img src="<?php echo esc_url( $post['image_url'] ); ?>" width="<?php echo esc_attr( $post['image_width'] ); ?>" height="<?php echo esc_attr( $post['image_height'] ); ?>" srcset="<?php echo esc_attr( $post['srcset'] ); ?>" sizes="(min-width: 992px) 360px, calc(100vw - 30px)" class="card-img-top  wp-post-image" alt="<?php echo esc_attr( $post['title'] ); ?>">
					</a>
				<?php endif; ?>

				<div class="card-block  latest-news__content">
					<time class="latest-news__date" datetime="<?php echo esc_attr( $post['full_date_time'] ); ?>"><?php echo esc_html( $post['full_date'] ); ?></time>
					<h4 class="card-title  latest-news__title"><a href="<?php echo esc_attr( $post['link'] ); ?>"><?php echo wp_kses_post( $post['title'] ); ?></a></h4>
					<div class="card-text  latest-news__text">
					<?php echo wp_kses_post( $post['excerpt'] ); ?>
					</div>
				</div>
			</div>
		<?php endforeach; ?>
		<?php if ( isset( $instance['more_news_on'] ) && $instance['more_news_on'] ) : ?>
			<a href="<?php echo esc_url( $instance['link_to_more_news'] ); ?>" class="card-block  latest-news  latest-news--more-news">
				<?php echo wp_kses_post( $text['more_news'] ); ?>
			</a>
		<?php endif; ?>
	<?php else : ?>
		<div class="latest-news__container">
			<?php foreach ( $posts as $post ): ?>
				<a href="<?php echo esc_url( $post['link'] ); ?>" class="card  latest-news  latest-news--<?php echo esc_attr( $instance['type'] ); ?>">
					<div class="card-block  latest-news__content">
						<h4 class="card-title  latest-news__title"><?php echo wp_kses_post( $post['title'] ); ?></h4>
							<time class="latest-news__date" datetime="<?php echo esc_attr( $post['full_date_time'] ); ?>">
								<?php echo esc_html( $post['full_date'] ); ?>
							</time>
					</div>
				</a>
			<?php endforeach; ?>

			<?php if ( isset( $instance['more_news_on'] ) && $instance['more_news_on'] ) : ?>
				<a href="<?php echo esc_url( $instance['link_to_more_news'] ); ?>" class="card-block  latest-news  latest-news--more-news">
					<?php echo wp_kses_post( $text['more_news'] ); ?>
				</a>
			<?php endif; ?>
		</div>
	<?php endif; ?>

<?php echo $args['after_widget']; ?>