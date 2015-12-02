<?php
/**
 * Social Icons Widget
 *
 * @package ProteusWidgets
 * @since 1.0.0
 */


if ( ! class_exists( 'PW_Social_Icons' ) ) {
	class PW_Social_Icons extends PW_Widget {

		private $num_social_icons = 8;
		private $current_widget_id;

		private $font_awesome_icons_list;

		// Basic widget settings
		function widget_id_base() { return 'social_icons'; }
		function widget_name() { return esc_html__( 'Social Icons', 'proteuswidgets' ); }
		function widget_description() { ''; }
		function widget_class() { return 'widget-social-icons'; }

		/**
		 * Register widget with WordPress.
		 */
		public function __construct() {
			parent::__construct();

			// A list of icons to choose from in the widget backend
			$this->font_awesome_icons_list = apply_filters(
				'pw/social_icons_fa_icons_list',
				array(
					'fa-facebook',
					'fa-twitter',
					'fa-youtube',
					'fa-skype',
					'fa-google-plus',
					'fa-pinterest',
					'fa-instagram',
					'fa-vine',
					'fa-tumblr',
					'fa-foursquare',
					'fa-xing',
					'fa-flickr',
					'fa-vimeo',
					'fa-linkedin',
					'fa-dribble',
					'fa-wordpress',
					'fa-rss',
					'fa-github',
					'fa-bitbucket',
				)
			);
		}

		/**
		 * Front-end display of widget.
		 *
		 * @see WP_Widget::widget()
		 *
		 * @param array $args     Widget arguments.
		 * @param array $instance Saved values from database.
		 */
		public function widget( $args, $instance ) {
			// Prepare data for mustache template
			if ( ! isset( $instance['social_icons'] ) ) {
				$instance['social_icons'] = array(
					array(
						'link' => '',
						'icon' => '',
					),
				);
			}

			$instance['social_icons'] = array_values( (array) $instance['social_icons'] );
			// Escape data
			for ( $i = 0; $i < count( $instance['social_icons'] ); $i++ ) {
				// Cast object to array for one click demo import
				$instance['social_icons'][ $i ] = (array) $instance['social_icons'][ $i ];

				$instance['social_icons'][ $i ]['link'] = esc_url( $instance['social_icons'][ $i ]['link'] );
				$instance['social_icons'][ $i ]['icon'] = esc_attr( $instance['social_icons'][ $i ]['icon'] );
			}
			$instance['target'] = ! empty ( $instance['new_tab'] ) ? '_blank' : '_self';

			// Mustache widget-social-icons template rendering
			echo $this->mustache->render( apply_filters( 'pw/widget_social_icons_view', 'widget-social-icons' ), array(
				'args'         => $args,
				'instance'     => $instance,
			));
		}

		/**
		 * Sanitize widget form values as they are saved.
		 *
		 * @see WP_Widget::update()
		 *
		 * @param array $new_instance Values just sent to be saved.
		 * @param array $old_instance Previously saved values from database.
		 *
		 * @return array Updated safe values to be saved.
		 */
		public function update( $new_instance, $old_instance ) {
			$instance = array();

			foreach ( $new_instance['social_icons'] as $key => $social_icon ) {
				$instance['social_icons'][ $key ]['id']   = sanitize_key( $social_icon['id'] );
				$instance['social_icons'][ $key ]['link'] = sanitize_text_field( $social_icon['link'] );
				$instance['social_icons'][ $key ]['icon'] = sanitize_html_class( $social_icon['icon'] );
			}

			$instance['new_tab'] = ! empty ( $new_instance['new_tab'] ) ? sanitize_key( $new_instance['new_tab'] ) : '';

			return $instance;
		}

		/**
		 * Back-end widget form.
		 *
		 * @see WP_Widget::form()
		 *
		 * @param array $instance Previously saved values from database.
		 */
		public function form( $instance ) {
			if ( ! isset( $instance['social_icons'] ) ) {
				$instance['social_icons'] = array(
					array(
						'id'   => 1,
						'link' => '',
						'icon' => '',
					),
				);
			}

			$new_tab  = empty( $instance['new_tab'] ) ? '' : $instance['new_tab'];

			// Page Builder fix when using repeating fields
			if ( 'temp' === $this->id ) {
				$this->current_widget_id = $this->number;
			}
			else {
				$this->current_widget_id = $this->id;
			}

		?>

			<p>
				<input class="checkbox" type="checkbox" <?php checked( $new_tab, 'on' ); ?> id="<?php echo esc_attr( $this->get_field_id( 'new_tab' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'new_tab' ) ); ?>" value="on" />
				<label for="<?php echo esc_attr( $this->get_field_id( 'new_tab' ) ); ?>"><?php esc_html_e( 'Open link in new tab', 'proteuswidgets' ); ?></label>
			</p>
			<hr>


			<h4><?php esc_html_e( 'Social icons', 'proteuswidgets' ); ?></h4>

			<script type="text/template" id="js-pt-social-icon-<?php echo esc_attr( $this->current_widget_id ); ?>">
				<p>
					<label for="<?php echo esc_attr( $this->get_field_id( 'social_icons' ) ); ?>-{{id}}-link"><?php esc_html_e( 'Link:', 'proteuswidgets' ); ?></label>
					<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'social_icons' ) ); ?>-{{id}}-link" name="<?php echo esc_attr( $this->get_field_name( 'social_icons' ) ); ?>[{{id}}][link]" type="text" value="{{link}}" />
				</p>

				<p>
					<label for="<?php echo esc_attr( $this->get_field_id( 'social_icons' ) ); ?>-{{id}}-icon"><?php esc_html_e( 'Select social icon:', 'proteuswidgets' ); ?></label> <br />
					<small><?php printf( esc_html__( 'Click on the icon below or manually select from the %s website.', 'proteuswidgets' ), '<a href="http://fontawesome.io/icons/" target="_blank">FontAwesome</a>' ); ?></small>
					<input id="<?php echo esc_attr( $this->get_field_id( 'social_icons' ) ); ?>-{{id}}-icon" name="<?php echo esc_attr( $this->get_field_name( 'social_icons' ) ); ?>[{{id}}][icon]" type="text" value="{{icon}}" class="widefat  js-icon-input" /> <br><br>
					<?php foreach ( $this->font_awesome_icons_list as $icon ) : ?>
						<a class="js-selectable-icon  icon-widget" href="#" data-iconname="<?php echo esc_attr( $icon ); ?>"><i class="fa fa-lg <?php echo esc_html( $icon ) ?>"></i></a>
					<?php endforeach; ?>
				</p>

				<p>
					<input name="<?php echo esc_attr( $this->get_field_name( 'social_icons' ) ); ?>[{{id}}][id]" type="hidden" value="{{id}}" />
					<a href="#" class="pt-remove-social-icon  js-pt-remove-social-icon"><span class="dashicons dashicons-dismiss"></span> <?php esc_html_e( 'Remove Social Icon', 'proteuswidgets' ); ?></a>
				</p>
			</script>
			<div class="pt-widget-social-icons" id="social-icons-<?php echo esc_attr( $this->current_widget_id ); ?>">
				<div class="social-icons"></div>
				<p>
					<a href="#" class="button  js-pt-add-social-icon"><?php esc_html_e( 'Add New Social Icon', 'proteuswidgets' ); ?></a>
				</p>
			</div>
			<script type="text/javascript">
				(function() {
					// repopulate the form
					var socialIconsJSON = <?php echo wp_json_encode( $instance['social_icons'] ) ?>;

					// get the right widget id and remove the added < > characters at the start and at the end.
					var widgetId = '<<?php echo esc_js( $this->current_widget_id ); ?>>'.slice( 1, -1 );

					if ( _.isFunction( ProteusWidgets.Utils.repopulateSocialIcons ) ) {
						ProteusWidgets.Utils.repopulateSocialIcons( socialIconsJSON, widgetId );
					}
				})();
			</script>

			<?php
		}
	}
}