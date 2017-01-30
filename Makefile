.PHONY: deploy bundle

deploy:
	rsync -avhz readme.txt *.php *.js *.json *.css assets languages widgets inc bower_components vendor deployer@as:/opt/proteusnet/www/demo.proteusthemes.com/wp-content/plugins/proteuswidgets/

bundle:
	composer install
	composer dumpautoload
	cd .. && zip -r proteuswidgets.zip \
		proteuswidgets/readme.txt \
		proteuswidgets/*.php \
		proteuswidgets/*.js \
		proteuswidgets/*.json \
		proteuswidgets/*.css \
		proteuswidgets/assets/ \
		proteuswidgets/languages/ \
		proteuswidgets/bower_components/bootstrap-sass/ \
		proteuswidgets/bower_components/fontawesome/ \
		proteuswidgets/bower_components/mustache/ \
		proteuswidgets/inc/ \
		proteuswidgets/vendor/ \
		proteuswidgets/widgets/
	mv ../proteuswidgets.zip ~/themes/mentalpress/bundled-plugins/