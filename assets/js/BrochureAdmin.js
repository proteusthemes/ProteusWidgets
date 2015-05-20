var ProteusWidgetsUploader = ProteusWidgetsUploader || {};

/**
 * MediaUploader class, constructor
 * @return void
 */
ProteusWidgetsUploader.mediaUploader = function( frameOptions ) {
	'use strict';

	// Cache jQuery variable to local property
	this.$ = jQuery;

	// urlInputId is undefined at first
	this.urlInputId = undefined;

	// Prepare options and create new frame
	this.frameOptions = this.$.extend( {}, this.frameDefaults, frameOptions );
	this.fileFrame = this.createFileFrame();

	// Event listeners
	this.fileFrame.on( 'select', this.onFrameSelect, this );

	// Good practice, for chaining
	return this;
};

jQuery.extend( ProteusWidgetsUploader.mediaUploader.prototype, {

	// Default options, can be overriden when initialized
	frameDefaults: {
		title:    'Choose a file',
		button:   { text: 'Select' },
		multiple: false
	},

	/**
	 * Create new file frame. This function should be only called once.
	 *
	 * Should be improved with _.once() from underscorejs
	 *
	 * @return {wp media frame}
	 */
	createFileFrame: function() {
		var fileFrame = wp.media.frames.fileFrame = wp.media( this.frameOptions );

		return fileFrame;
	},

	/**
	 * Set the URL where the file frame will return value and open it.
	 * @param  {string} urlInputId
	 * @return {this}
	 */
	openFileFrame: function ( urlInputId ) {
		// Set the prop urlInputId to passed value
		this.urlInputId = urlInputId;

		this.fileFrame.open();

		return this;
	},

	/**
	 * Event handler - when the user confirms the selection he made.
	 * @return {this}
	 */
	onFrameSelect: function() {
		// Eead the json data returned from the Media uploader
		var json = this.fileFrame.state().get( 'selection' ).first().toJSON();

		// Test if the URL is here
		if ( 0 > this.$.trim( json.url.length ) ) {
			return;
		}

		// Add the URL value to the appropriate URL input field
		this.$( '#' + this.urlInputId ).val( json.url );

		this.urlInputId = undefined;

		return this;
	},

} );

// Initialization
ProteusWidgetsUploader.fileUploader = new ProteusWidgetsUploader.mediaUploader();
ProteusWidgetsUploader.imageUploader = new ProteusWidgetsUploader.mediaUploader( {
		title:    'Choose a image',
		library: { type: 'image' },
		button:   { text: 'Select' },
		multiple: false
	} );