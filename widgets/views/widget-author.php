{{{ args.before_widget }}}

	<div class="widget-author__image-container">
		<div class="widget-author__avatar--blurred">
			{{{ author-avatar }}}
		</div>
		<a href="{{ author-posts }}" class="widget-author__avatar">
			{{{ author-avatar }}}
		</a>
	</div>
	<div class="row widget-author__content">
		<div class="col-xs-10  col-xs-offset-1">
			{{{ author-meta-name }}}
			{{{ author-meta-description }}}

			{{# author-meta-user-url }}
				<p>
					<a href="{{ author-meta-user-url }}">{{ author-meta-user-url }}</a>
				</p>
			{{/ author-meta-user-url }}

			{{# social-icons }}
				<p class="social-icons__author">
					{{# social-icons-list }}
						<a href="{{ url }}" class="social-icons__container">
							<i class="fa fa-{{icon}}"></i>
						</a>
					{{/ social-icons-list }}
				</p>
			{{/ social-icons }}
			<?php

			?>
		</div>
	</div>

{{{ args.after_widget }}}