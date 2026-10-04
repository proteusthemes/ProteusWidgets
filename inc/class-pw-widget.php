<?php

/*
 * Abstract class that extends WP_Widget and will be extended by individual widget
 */

if ( ! class_exists( 'PW_Widget' ) ) {
	abstract class PW_Widget extends WP_Widget {

		protected $template_engine, $widget_id_base, $widget_class, $widget_description, $widget_name;

		public function __construct() {
			parent::__construct(
				'pw_' . $this->widget_id_base,
				sprintf( 'ProteusThemes: %s', $this->widget_name ),
				array(
					'description' => $this->widget_description,
					'classname'   => $this->widget_class,
				)
			);

			// Define the ProteusWidgets PHP templating engine *Singleton*.
			$this->template_engine = PW_Templating::get_instance();
		}

		/**
		 * Helper function to order items by ids.
		 * Used for sorting widget setting items.
		 *
		 * @param int $a first comparable parameter.
		 * @param int $b second comparable parameter.
		 */
		function sort_by_id( $a, $b ) {
			return (int) $a['id'] <=> (int) $b['id'];
		}

		/**
		 * Give setting items without an id the next free id.
		 * The widget form keeps one item per id, so items sharing an id would be lost.
		 *
		 * @param array $items widget setting items.
		 * @return array
		 */
		protected function fill_missing_row_ids( $items ) {
			$items  = is_array( $items ) ? $items : array();
			$max_id = -1;

			foreach ( $items as $item ) {
				if ( is_array( $item ) && isset( $item['id'] ) && is_numeric( $item['id'] ) ) {
					$max_id = max( $max_id, (int) $item['id'] );
				}
			}

			foreach ( $items as $key => $item ) {
				$item = (array) $item;

				if ( ! isset( $item['id'] ) || '' === $item['id'] ) {
					$item['id'] = ++$max_id;
				}

				$items[ $key ] = $item;
			}

			return $items;
		}

		/**
		 * Keep the saved rows of lists that the submitted form did not show.
		 * A list that was shown posts a "<list>_ready" field, so a missing list without it was never on screen.
		 *
		 * @param array $new_instance submitted widget settings.
		 * @param array $old_instance saved widget settings.
		 * @param array $lists        names of the list settings.
		 * @return array
		 */
		protected function keep_unshown_rows( $new_instance, $old_instance, $lists ) {
			$new_instance = (array) $new_instance;

			// Page Builder passes the whole stored widget and pairs $old_instance by a widget id that need not be unique.
			if ( isset( $new_instance['panels_info'] ) ) {
				return $new_instance;
			}

			foreach ( $lists as $list ) {
				if ( ! isset( $new_instance[ $list ] ) && empty( $new_instance[ $list . '_ready' ] ) && isset( $old_instance[ $list ] ) ) {
					$new_instance[ $list ] = $old_instance[ $list ];
				}
			}

			return $new_instance;
		}
	}
}
