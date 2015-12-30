<?php echo $args['before_widget']; ?>

	<div id="carousel-people-<?php echo esc_attr( $args['widget_id'] ); ?>" class="carousel slide" data-ride="carousel" data-interval="<?php echo esc_attr( $instance['slider_settings'] ); ?>">
		<div class="carousel-inner" role="listbox">
			<?php foreach ( $people as $person ) : ?>
				<div class="item <?php echo isset( $person['active'] ) ? esc_attr( $person['active'] ) : ''; ?>">
					<?php if ( ! empty( $person['tag'] ) ) : ?>
						<?php if ( ! empty( $person['link'] ) ) : ?>
							<a href="<?php echo esc_url( $person['link'] ); ?>" class="about-us__tag"><?php echo esc_html( $person['tag'] ); ?></a>
						<?php else : ?>
							<div class="about-us__tag"><?php echo esc_html( $person['tag'] ); ?></div>
						<?php endif; ?>
					<?php endif; ?>

					<?php if ( ! empty( $person['image'] ) ) : ?>
						<img class="about-us__image" src="<?php echo esc_url( $person['image'] ); ?>" alt="<?php echo esc_attr( $text['image-alt'] ); ?>" />
					<?php endif; ?>

					<h5 class="about-us__name"><?php echo esc_html( $person['name'] ); ?></h5>
					<p class="about-us__description"><?php echo wp_kses_post( $person['description'] ); ?></p>
					<?php if ( ! empty( $person['link'] ) ) : ?>
						<a href="<?php echo esc_url( $person['link'] ); ?>" class="read-more  about-us__link"><?php echo esc_attr( $text['read-more'] ); ?></a>
					<?php endif; ?>
				</div>
			<?php endforeach; ?>
		</div>
	</div>

	<?php if ( count( $people ) > 1 ) : ?>
		<div class="about-us__navigation">
			<a class="person__carousel  person__carousel--left about-us__navigation__left" href="#carousel-people-<?php echo esc_attr( $args['widget_id'] ); ?>" data-slide="prev">
				<i class="fa  fa-chevron-left" aria-hidden="true"></i>
				<span class="sr-only" role="button"><?php echo esc_html( $text['previous'] ); ?></span>
			</a>
			<a class="person__carousel  person__carousel--right about-us__navigation__right" href="#carousel-people-<?php echo esc_attr( $args['widget_id'] ); ?>" data-slide="next">
				<i class="fa  fa-chevron-right" aria-hidden="true"></i>
				<span class="sr-only" role="button"><?php echo esc_html( $text['next'] ); ?></span>
			</a>
		</div>
	<?php endif; ?>

<?php echo $args['after_widget']; ?>
