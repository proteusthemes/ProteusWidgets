.PHONY: deploy bundle

deploy:
	tar czf proteuswidgets.tar.gz proteuswidgets.php widgets/ inc/ bower_components/ vendor/ assets/ main.css readme.txt
	scp proteuswidgets.tar.gz pt:./

	# extract to prod
	ssh primoz@pt "rm -rf ~/root/demo.proteusthemes.com/wp-content/plugins/proteuswidgets/* && tar xf proteuswidgets.tar.gz -C ~/root/demo.proteusthemes.com/wp-content/plugins/proteuswidgets"

	rm proteuswidgets.tar.gz

bundle:
	composer dumpautoload
	cd .. && zip -r proteuswidgets.zip \
		proteuswidgets/readme.txt \
		proteuswidgets/*.php \
		proteuswidgets/*.js \
		proteuswidgets/*.json \
		proteuswidgets/*.css \
		proteuswidgets/assets/ \
		proteuswidgets/bower_components/bootstrap-sass/ \
		proteuswidgets/bower_components/fontawesome/ \
		proteuswidgets/bower_components/mustache/ \
		proteuswidgets/inc/ \
		proteuswidgets/widgets/
	mv ../proteuswidgets.zip ~/themes/mentalpress/bundled-plugins/