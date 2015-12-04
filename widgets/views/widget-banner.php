{{{ args.before_widget }}}

	{{# instance.link }}
		<a class="banner" href="{{ instance.link }}" target="{{ link-target }}">
	{{/ instance.link }}
	{{^ instance.link }}
		<div class="banner">
	{{/ instance.link }}

		<div class="banner__title">
			{{ instance.title }}
		</div>
		<div class="banner__content">
			{{ instance.content }}
		</div>

	{{# instance.link }}
		</a>
	{{/ instance.link }}
	{{^ instance.link }}
		</div>
	{{/ instance.link }}

{{{ args.after_widget }}}