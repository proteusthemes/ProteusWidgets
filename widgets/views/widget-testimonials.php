<?php echo $args['before_widget']; ?>

	<div class="testimonial">
		<?php if ( isset( $instance['navigation'] ) && $instance['navigation'] ) : ?>
			<a class="testimonial__carousel  testimonial__carousel--left" href="#carousel-testimonials-<?php echo esc_attr( $args['widget_id'] ); ?>" data-slide="prev"><i class="fa  fa-chevron-left" aria-hidden="true"></i><span class="sr-only" role="button"><?php echo esc_html( $text['next'] ); ?></span></a>
		<?php endif; ?>

		<?php echo $args['before_title'] . wp_kses_post( $instance['title'] ) . $args['after_title']; ?>

		<?php if ( isset( $instance['navigation'] ) && $instance['navigation'] ) : ?>
			<a class="testimonial__carousel  testimonial__carousel--right" href="#carousel-testimonials-<?php echo esc_attr( $args['widget_id'] ); ?>" data-slide="next"><i class="fa  fa-chevron-right" aria-hidden="true"></i><span class="sr-only" role="button"><?php echo esc_html( $text['previous'] ); ?></span></a>
		<?php endif; ?>

		<div id="carousel-testimonials-<?php echo esc_attr( $args['widget_id'] ); ?>" class="carousel slide" data-ride="carousel" data-interval="<?php echo esc_attr( $instance['slider_settings'] ); ?>">
			<!-- Wrapper for slides -->
			<div class="carousel-inner" role="listbox">
				<div class="item active">
					<div class="row">
						<?php foreach ( $testimonials as $testimonial ) : ?>
							<?php echo wp_kses_post( $testimonial['more-at-once'] ); ?>
							<div class="col-xs-12  col-sm-<?php echo esc_attr( $instance['spans'] ); ?>">
								<blockquote>
									<p class="testimonial__quote">
										<?php echo wp_kses_post( $testimonial['quote'] ); ?>
									</p>
									<cite class="testimonial__author">
										<?php echo esc_html( $testimonial['author'] ); ?>
									</cite>

									<?php if ( isset( $testimonial['author_description'] ) && $testimonial['author_description'] ) : ?>
										<div class="testimonial__author-description">
											<?php echo wp_kses_post( $testimonial['author_description'] ); ?>
										</div>
									<?php endif; ?>

									<?php if ( isset( $testimonial['display-ratings'] ) && $testimonial['display-ratings'] ) : ?>
										<div class="testimonial__rating">
											<?php foreach ( $testimonial['rating'] as $rating ) : ?>
												<i class="fa  fa-star"></i>
											<?php endforeach; ?>
										</div>
									<?php endif; ?>

									<?php if ( isset( $testimonial['author_avatar'] ) && $testimonial['author_avatar'] ) : ?>
										<div class="testimonial__author-avatar">
											<img src="<?php echo esc_url( $testimonial['author_avatar'] ); ?>" alt="<?php esc_html_e( 'Testimonial author avatar', 'proteuswidgets' ) ?>">
										</div>
									<?php endif; ?>
								</blockquote>
							</div>
						<?php endforeach; ?>
					</div>
				</div>
			</div>
		</div>
	</div>

<?php echo $args['after_widget']; ?>
