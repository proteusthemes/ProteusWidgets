{{{ args.before_widget }}}

	{{# instance.title_is_set }}
		<h3 class="widget-title  steps__title">{{ instance.preped_title }}</h3>
	{{/ instance.title_is_set }}

	<div class="steps">
		{{# items }}
			<div class="step">
				<div class="step__title">
					<i class="fa  {{ icon }}"></i> {{ title }}
				</div>
				<p class="step__content">
					{{{ content }}}
				</p>
				<div class="step__number">
					{{ step }}
				</div>
			</div>
		{{/ items }}
	</div>

{{{ args.after_widget }}}