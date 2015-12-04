{{{ args.before_widget }}}

	<div
		class="simple-map  js-where-we-are"
		data-latlng="{{ instance.latLng }}"
		data-markers="{{{ instance.locations }}}"
		data-zoom="{{ instance.zoom }}"
		data-type="{{ instance.type }}"
		data-style="{{{ instance.style }}}"
		style="height: {{ instance.height }}px;"
	></div>

{{{ args.after_widget }}}