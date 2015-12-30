<?php echo $args['before_widget']; ?>

	<div class="card  person-profile">
		<?php if ( ! empty( $instance['image'] ) ) : ?>
		<img class="person-profile__image  wp-post-image" src="<?php echo esc_url( $instance['image'] ); ?>" alt="<?php echo esc_attr( $text['picture_of'] ) . ' ' . esc_attr( $instance['name'] ); ?>">
		<?php endif; ?>
		<div class="card-block  person-profile__container">
			<?php if ( ! empty( $instance['social_icons'] ) ) : ?>
				<div class="person-profile__social-icons">
					<?php foreach ( $instance['social_icons'] as $icon ) : ?>
						<a class="person-profile__social-icon" href="<?php echo esc_url( $icon['link'] ); ?>" target="<?php echo ( ! empty( $instance['new_tab'] ) ) ? '_blank' : '_self' ?>"><i class="fa  <?php echo esc_attr( $icon['icon'] ); ?>"></i></a>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>
			<div class="person-profile__content">
				<?php if ( ! empty( $instance['tag'] ) ) : ?>
					<span class="person-profile__tag"><?php echo esc_html( $instance['tag'] ); ?></span>
				<?php endif; ?>
				<h4 class="card-title  person-profile__name"><?php echo esc_html( $instance['name'] ); ?></h4>
				<p class="card-text  person-profile__description"><?php echo wp_kses_post( $instance['description'] ); ?></p>
			</div>
		</div>
	</div>

<?php echo $args['after_widget']; ?>
