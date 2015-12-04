{{{ args.before_widget }}}

	<div class="number-counters" data-speed="{{ instance.speed }}">
	{{# counters }}
		<div class="number-counter">
			{{# icon }}
			<i class="number-counter__icon  fa  {{ icon }}"></i>
			{{/ icon }}
			<div class="number-counter__number  js-number" data-to="{{ number }}">{{ leading_zeros }}</div>
			<div class="number-counter__title">{{ title }}</div>
		</div>
	{{/ counters }}
	</div>

{{{ args.after_widget }}}