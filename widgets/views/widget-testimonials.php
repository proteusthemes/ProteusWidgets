<?php echo $args['before_widget']; ?>

	<?php echo $args['before_title'] . wp_kses_post( $instance['title'] ) . $args['after_title']; ?>

	<div class="testimonial__container">
		<div id="carousel-testimonials-<?php echo esc_attr( $args['widget_id'] ); ?>" class="carousel  slide  testimonial" data-ride="carousel" data-interval="<?php echo esc_attr( $instance['slider_settings'] ); ?>">
			<!-- Wrapper for slides -->
			<div class="carousel-inner" role="listbox">
				<div class="carousel-item active">
					<div class="row">
						<?php foreach ( $testimonials as $testimonial ) : ?>
							<?php echo wp_kses_post( $testimonial['more-at-once'] ); ?>
							<div class="col-xs-12  col-sm-<?php echo esc_attr( $instance['spans'] ); ?>">
								<blockquote>
									<?php if ( isset( $testimonial['author_avatar'] ) && $testimonial['author_avatar'] ) : ?>
										<div class="testimonial__author-avatar">
											<img src="<?php echo esc_url( $testimonial['author_avatar'] ); ?>" alt="">
										</div>
									<?php endif; ?>
									<div class="testimonial__author">
										<cite class="testimonial__author-name">
										<?php echo esc_html( $testimonial['author'] ); ?>
										</cite>
										<?php if ( isset( $testimonial['author_description'] ) && $testimonial['author_description'] ) : ?>
											<div class="testimonial__author-description">
												<?php echo wp_kses_post( $testimonial['author_description'] ); ?>
											</div>
										<?php endif; ?>
									</div>
									<p class="testimonial__quote">
										<?php echo wp_kses_post( $testimonial['quote'] ); ?>
									</p>
								</blockquote>
							</div>
						<?php endforeach; ?>
					</div>
				</div>
			</div>
		</div>
		<?php if ( isset( $instance['navigation'] ) && $instance['navigation'] ) : ?>
			<a class="testimonial__carousel  testimonial__carousel--left" href="#carousel-testimonials-<?php echo esc_attr( $args['widget_id'] ); ?>" data-slide="prev"><i class="fa  fa-caret-left" aria-hidden="true"></i><span class="sr-only" role="button"><?php echo esc_html( $text['next'] ); ?></span></a>
			<a class="testimonial__carousel  testimonial__carousel--right" href="#carousel-testimonials-<?php echo esc_attr( $args['widget_id'] ); ?>" data-slide="next"><i class="fa  fa-caret-right" aria-hidden="true"></i><span class="sr-only" role="button"><?php echo esc_html( $text['previous'] ); ?></span></a>
		<?php endif; ?>
	</div>

<?php echo $args['after_widget']; ?>