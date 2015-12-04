{{{ args.before_widget }}}

	<div id="carousel-people-{{ args.widget_id }}" class="carousel slide" data-ride="carousel" data-interval="{{ instance.slider_settings }}">
		<div class="carousel-inner" role="listbox">
			{{# people }}
				<div class="item {{ active }}">
					{{# tag }}
						{{# link }}
							<a href="{{ link }}" class="about-us__tag">{{ tag }}</a>
						{{/ link }}
						{{^ link }}
							<div class="about-us__tag">{{ tag }}</div>
						{{/ link }}
					{{/ tag }}

					{{# image }}
						<img class="about-us__image" src="{{ image }}" alt="{{ text.image-alt }}" />
					{{/ image }}

					<h5 class="about-us__name">{{ name }}</h5>
					<p class="about-us__description">{{ description }}</p>
					{{# link }}
						<a href="{{ link }}" class="read-more  about-us__link">{{ text.read-more }}</a>
					{{/ link }}
				</div>
			{{/ people }}
		</div>
	</div>

	{{# instance.navigation }}
		<div class="about-us__navigation">
			<a class="person__carousel  person__carousel--left about-us__navigation__left" href="#carousel-people-{{ args.widget_id }}" data-slide="prev">
				<i class="fa  fa-chevron-left" aria-hidden="true"></i>
				<span class="sr-only" role="button">{{ text.previous }}</span>
			</a>
			<a class="person__carousel  person__carousel--right about-us__navigation__right" href="#carousel-people-{{ args.widget_id }}" data-slide="next">
				<i class="fa  fa-chevron-right" aria-hidden="true"></i>
				<span class="sr-only" role="button">{{ text.next }}</span>
			</a>
		</div>
	{{/ instance.navigation }}

{{{ args.after_widget }}}