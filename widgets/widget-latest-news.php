<?php
/**
 * Latest News Widget
 */

if ( ! class_exists( 'PW_Latest_News' ) ) {
	class PW_Latest_News extends WP_Widget {

		const MAX_POST_NUMBER = 10;
		private $current_widget_id;

		// Basic widget settings
		function widget_id_base() { return 'latest_news'; }
		function widget_name() { return __( 'Latest News', 'legalpress-pt' ); }
		function widget_description() { return __( 'Latest news widget for Page Builder.', 'legalpress-pt' ); }
		function widget_class() { return 'widget-latest-news'; }

		public function __construct() {
			parent::__construct(
				'pw_' . $this->widget_id_base(),
				sprintf( 'ProteusThemes: %s', $this->widget_name() ), // Name
				array(
					'description' => $this->widget_description(),
					'classname'   => $this->widget_class(),
				)
			);
		}

		/*
		 * Get the post excerpt. If post_excerpt data is defined use that, otherwise
		 * trim down the content to the proper size.
		 *
		 * @param string $post_excerpt
		 * @param string $post_content
		 *
		 */
		function get_post_excerpt( $post_excerpt, $post_content ) {
			if ( ! empty( $post_excerpt ) ) {
				return $post_excerpt;
			}

			$text = strip_shortcodes( $post_content );
			$text = strip_tags( $text );
			$text = str_replace( ']]>', ']]&gt;', $text );
			$excerpt_length = apply_filters( 'excerpt_length', 55 );
			$excerpt_more = apply_filters( 'excerpt_more', ' ' . '[&hellip;]' );
			$text = wp_trim_words( $text, $excerpt_length, $excerpt_more );

			return $text;
		}

		/*
		 * Fetch recent posts data from DB (cache it on first widget instance) and then
		 * fetch the data from cache for all other widget instances.
		 */
		function get_cached_data( $cache_name ) {
			// Get/set cache data just once for multiple widgets
			$recent_posts_data = wp_cache_get( $cache_name );
			if ( false === $recent_posts_data ) {
				$recent_posts_original_args = array(
					'numberposts'         => self::MAX_POST_NUMBER,
					'orderby'             => 'post_date',
					'order'               => 'DESC',
					'post_type'           => 'post',
					'post_status'         => 'publish',
					// 'suppress_filters' => false // If some WPML problems occur, uncomment this line
				);
				$recent_posts_original = wp_get_recent_posts( $recent_posts_original_args );

				// var_dump($recent_posts_original);

				// Prepare the data that we need for display
				$recent_posts_data = array();
				foreach ( $recent_posts_original as $key => $post ) {
					$recent_posts_data[ $key ]['id']        = $post['ID'];
					$recent_posts_data[ $key ]['date']      = get_the_date( 'M j', $post['ID'] );
					$split_date                             = explode( ' ', $recent_posts_data[ $key ]['date'] );
					$recent_posts_data[ $key ]['day']       = $split_date[1];
					$recent_posts_data[ $key ]['month']     = $split_date[0];
					$recent_posts_data[ $key ]['full_date'] = get_the_date( get_option( 'date_format' ), $post['ID'] );
					$recent_posts_data[ $key ]['image']     = get_the_post_thumbnail( $post['ID'] );
					$recent_posts_data[ $key ]['link']      = get_permalink( $post['ID'] );
					$recent_posts_data[ $key ]['title']     = $post['post_title'];
					$recent_posts_data[ $key ]['author']    = get_the_author_meta( 'display_name', $post['post_author'] );
					$recent_posts_data[ $key ]['excerpt']   = $this->get_post_excerpt( $post['post_excerpt'], $post['post_content'] );
				}

				wp_cache_set( $cache_name, $recent_posts_data );
			}
			return $recent_posts_data;
		}

		/**
		 * Front-end display of widget.
		 *
		 * @see WP_Widget::widget()
		 *
		 * @param array $args
		 * @param array $instance
		 */
		public function widget( $args, $instance ) {
			$type      = ! empty( $instance['type'] ) ? $instance['type'] : '';
			$from      = ! empty( $instance['from'] ) ? $instance['from'] : '';
			$to        = ! empty( $instance['to'] ) ? $instance['to'] : '';
			$more_news = ! empty( $instance['more_news'] ) ? $instance['more_news'] : '';

			// Get/set cache data just once for multiple widgets
			$recent_posts_data = $this->get_cached_data( 'legalpress_recent_posts' );

			// Array with posts to display
			$recent_posts = array();

			switch ( $type ) {
				case 'block':
					if ( $from <= count( $recent_posts_data ) ) {
						$recent_posts[] = $recent_posts_data[ $from - 1 ];
					}
					break;

				case 'inline':
					$recent_posts = array_intersect_key( $recent_posts_data, array_flip( range( $from - 1, $to - 1 ) ) );
					break;
			}

			echo $args['before_widget']; ?>
				<?php if ( 'inline' === $type ) : ?>
					<div class="latest-news__container">
				<?php endif; ?>
				<?php foreach ( $recent_posts as $post ) :?>
						<a href="<?php echo esc_url( $post['link'] ); ?>" class="latest-news  latest-news--<?php echo esc_attr( $type ); ?>">
							<?php if ( 'block' === $type ) : ?>
								<div class="latest-news__date">
										<?php echo esc_html( $post['day'] ); ?>
										<?php echo esc_html( $post['month'] ); ?>
								</div>
								<div class="latest-news__image">
									<?php echo $post['image']; ?>
								</div>
							<?php endif; ?>
							<div class="latest-news__content">
								<h4 class="latest-news__title"><?php echo esc_html( $post['title'] ); ?></h4>
								<?php if ( 'block' === $type ) : ?>
									<div class="latest-news__excerpt">
										<?php echo $post['excerpt']; ?>
									</div>
								<?php else : ?>
									<div class="latest-news__full-date">
										<?php echo $post['full_date']; ?>
									</div>
								<?php endif; ?>
							</div>
						</a>
					<?php
					endforeach;
					if ( 'on' === $more_news ) :
					?>
						<a href="<?php get_permalink( get_option( 'page_for_posts' ) ) ?>" class="latest-news  latest-news--more-news">
							<?php _e( 'More news', 'legalpress-pt' ); ?>
						</a>
					<?php endif; ?>
				<?php if ( 'inline' === $type ) : ?>
					</div>
				<?php endif; ?>
			<?php
				echo $args['after_widget'];
		}

		/**
		 * Sanitize widget form values as they are saved.
		 *
		 * @param array $new_instance The new options
		 * @param array $old_instance The previous options
		 */
		public function update( $new_instance, $old_instance ) {
			$instance = array();

			$instance['type']      = sanitize_key( $new_instance['type'] );
			$instance['from']      = sanitize_text_field( $new_instance['from'] );
			$instance['to']        = sanitize_text_field( $new_instance['to'] );

			if ( ! empty( $new_instance['more_news'] ) ) {
				$instance['more_news'] = sanitize_text_field( $new_instance['more_news'] );
			}
			else {
				$instance['more_news'] = '';
			}

			if ( $instance['from'] < 1 ) {
				$instance['from'] = 1;
			}
			elseif ( $instance['from'] > self::MAX_POST_NUMBER ) {
				$instance['from'] = self::MAX_POST_NUMBER;
			}

			if ( $instance['to'] < 1 ) {
				$instance['to'] = 1;
			}
			elseif ( $instance['to'] > self::MAX_POST_NUMBER ) {
				$instance['to'] = self::MAX_POST_NUMBER;
			}

			// to can't be lower than from
			if ( $instance['from'] > $instance['to'] ) {
				$instance['to'] = $instance['from'];
			}

			return $instance;
		}

		/**
		 * Back-end widget form.
		 *
		 * @param array $instance The widget options
		 */
		public function form( $instance ) {
			$type      = ! empty( $instance['type'] ) ? $instance['type'] : '';
			$from      = ! empty( $instance['from'] ) ? $instance['from'] : '';
			$to        = ! empty( $instance['to'] ) ? $instance['to'] : '';
			$more_news = ! empty( $instance['more_news'] ) ? $instance['more_news'] : '';

			// Page Builder fix for widget id used in Backbone and in the surrounding div below
			if ( 'temp' === $this->id ) {
				$this->current_widget_id = $this->number;
			}
			else {
				$this->current_widget_id = $this->id;
			}

			?>

			<div id="<?php echo esc_attr( $this->current_widget_id ); ?>">
				<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'type' ) ); ?>"><?php _ex( 'Display type:', 'backend', 'legalpress-pt' ); ?></label>
					<select id="<?php echo esc_attr( $this->get_field_id( 'type' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'type' ) ); ?>" class="latest-news-select-type">
						<option value="block" <?php selected( $type, 'block' ); ?>><?php _ex( 'Box (one post)', 'backend', 'legalpress-pt' ); ?></option>
						<option value="inline" <?php selected( $type, 'inline' ); ?>><?php _ex( 'Inline (multiple posts)', 'backend', 'legalpress-pt' ); ?></option>
					</select>
				</p>

				<p>
					<label for="<?php echo esc_attr( $this->get_field_id( 'from' ) ); ?>"><?php _ex( 'Post order number from:', 'backend', 'legalpress-pt' ); ?></label>
					<input id="<?php echo esc_attr( $this->get_field_id( 'from' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'from' ) ); ?>" type="number" min="1" max="<?php echo self::MAX_POST_NUMBER; ?>" value="<?php echo esc_attr( $from ); ?>" />
					<span class="latest-news-to-fields-group" id="<?php echo esc_attr( $this->get_field_id( 'to' ) ); ?>-fields-group">
					<label for="<?php echo esc_attr( $this->get_field_id( 'to' ) ); ?>"><?php _ex( 'To:', 'backend', 'legalpress-pt' ); ?></label>
					<input id="<?php echo esc_attr( $this->get_field_id( 'to' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'to' ) ); ?>" type="number" min="1" max="<?php echo esc_attr( self::MAX_POST_NUMBER ); ?>" value="<?php echo esc_attr( $to ); ?>" />
				</span>
				</p>

				<p>
					<label for="<?php echo esc_attr( $this->get_field_id( 'more_news' ) ); ?>"><?php _ex( 'More news link:', 'backend', 'legalpress-pt' ); ?></label>
					<input id="<?php echo esc_attr( $this->get_field_id( 'more_news' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'more_news' ) ); ?>" type="checkbox" <?php checked( $more_news, 'on' ); ?> />
				</p>
			</div>

			<script type="text/javascript">
				(function( $ ) {

					var toFieldsGroup = '<<?php echo esc_js( $this->get_field_id( "to" ) ); ?>>'.slice( 1, -1 );
					var selectedType  = '<<?php echo esc_js( $this->get_field_id( "type" ) ); ?>>'.slice( 1, -1 );
					var widgetId      = '<<?php echo esc_js( $this->current_widget_id ); ?>>'.slice( 1, -1 );

					toFieldsGroup = '#' + toFieldsGroup + '-fields-group';
					selectedType  = '#' + selectedType;


					var LatestNewsView = Backbone.View.extend({
						events: {
							'change .latest-news-select-type': 'toggle',
						},

						initialize: function( params ){
							this.toFieldsGroup = params.toFieldsGroup;
							this.selectedType  = params.selectedType;

							if ( 'block' == $(this.selectedType).val() ) {
								$(this.toFieldsGroup).hide();
								$(this.toFieldsGroup).siblings('label').html("<?php _ex( 'Post order number:', 'backend', 'legalpress-pt' ); ?>");
							}
						},

						toggle: function(event){
							if ( 'block' == event.target.value ) {
								$(this.toFieldsGroup).siblings('label').html("<?php _ex( 'Post order number:', 'backend', 'legalpress-pt' ); ?>");
								$(this.toFieldsGroup).hide();
							}
							else {
								$(this.toFieldsGroup).siblings('label').html("<?php _ex( 'Post order number from:', 'backend', 'legalpress-pt' ); ?>");
								$(this.toFieldsGroup).show();
							}
						},
					});

					new LatestNewsView( { el: $('#' + widgetId), toFieldsGroup: toFieldsGroup, selectedType: selectedType, } );

				})( jQuery );
			</script>

			<?php
		}

	}
	register_widget( 'PW_Latest_News' );
}