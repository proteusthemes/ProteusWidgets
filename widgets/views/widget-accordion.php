{{{ args.before_widget }}}

	{{# instance.title_is_set }}
		{{{ args.before_title }}}{{ instance.preped_title }}{{{ args.after_title }}}
	{{/ instance.title_is_set }}
	<div class="accordion panel-group" id="accordion-{{ instance.uniqid }}" role="tablist" aria-multiselectable="true">
	{{# items  }}
		<div class="accordion__panel  panel  panel-default">
			<div class="accordion__heading  panel-heading" role="tab" id="heading-{{ instance.uniqid }}-{{ key }}">
				<h4 class="panel-title">
					<a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion-{{ instance.uniqid }}" href="#collapse-{{ instance.uniqid }}-{{ key }}" aria-expanded="false" aria-controls="collapse-{{ instance.uniqid }}-{{ key }}">
						{{ title }}
					</a>
				</h4>
			</div>
			<div id="collapse-{{ instance.uniqid }}-{{ key }}" class="accordion__content  panel-collapse  collapse" role="tabpanel" aria-labelledby="heading-{{ instance.uniqid }}-{{ key }}">
				<div class="panel-body">
					{{{ content }}}
				</div>
			</div>
		</div>
	{{/ items  }}
	{{# instance.read_more_link }}
		<a href="{{ instance.read_more_link }}" class="more-link">{{ text.read_more }}</a>
	{{/ instance.read_more_link }}
	</div>

{{{ args.after_widget }}}