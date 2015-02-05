/**
 * Utilities for the administration when using ProteusThemes products
 */

jQuery( document ).ready( function( $ ) {
	'use strict';

	/**
	 * Select Icon on Click
	 */
	$( 'body' ).on( 'click', '.js-selectable-icon', function ( ev ) {
		ev.preventDefault();
		var $this = $( this );
		$this.siblings( '.js-icon-input' ).val( $this.data( 'iconname' ) );
	} );

} );
