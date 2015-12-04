{{{ args.before_widget }}}

	<div class="time-table">

		{{# instance.title }}
			<h3><span class="icon icons-ornament-left"></span>{{{ instance.title }}}<span class="icon icons-ornament-right"></span></h3>
		{{/ instance.title }}

		<div class="inner-bg">
			{{# opening-times }}
				<dl class="week-day {{ class }}">
					<dt>{{ day }}</dt>
					<dd>{{ day-time }}</dd>
				</dl>
			{{/ opening-times }}
		</div>

		{{# instance.additional_info }}
			<div class="additional-info">{{ instance.additional_info }}</div>
		{{/ instance.additional_info }}

	</div>

{{{ args.after_widget }}}