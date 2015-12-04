{{{ args.before_widget }}}

	{{# instance.block }}
		{{# posts }}
			<a href="{{ link }}" class="latest-news  latest-news--{{ instance.type }}">
				<div class="latest-news__date">
					<div class="latest-news__date__month">
						{{ month }}
					</div>
					<div class="latest-news__date__day">
						{{ day }}
					</div>
				</div>
				{{# image_url }}
				<div class="latest-news__image">
					<img src="{{ image_url }}" width="{{ image_width }}" height="{{ image_height }}" srcset="{{ srcset }}" sizes="(min-width: 781px) 360px, calc(100vw - 30px)" class="wp-post-image" alt="{{ title }}">
				</div>
				{{/ image_url }}

				<div class="latest-news__content">
					<h4 class="latest-news__title">{{ title }}</h4>
					<div class="latest-news__author">
						{{ text.by }} {{ author }}
					</div>
				</div>
			</a>
		{{/ posts }}
		{{# instance.more_news_on }}
			<a href="{{ instance.link_to_more_news }}" class="latest-news  latest-news--more-news">
				{{ text.more_news }}
			</a>
		{{/ instance.more_news_on }}
	{{/ instance.block }}

	{{^ instance.block }}
		{{# posts }}
			<a href="{{ link }}" class="latest-news  latest-news--{{ instance.type }}">
				<div class="latest-news__content">
					<h4 class="latest-news__title">{{ title }}</h4>
						<div class="latest-news__author">
							{{ text.by }} {{ author }}
						</div>
				</div>
			</a>
		{{/ posts }}

		{{# instance.more_news_on }}
			<a href="{{ instance.link_to_more_news }}" class="latest-news  latest-news--more-news">
				{{ text.more_news }}
			</a>
		{{/ instance.more_news_on }}
	{{/ instance.block }}

{{{ args.after_widget }}}