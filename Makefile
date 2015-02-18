.PHONY: deploy

deploy:
	tar czf proteuswidgets.tar.gz proteuswidgets.php widgets/ inc/ bower_components/ assets/ main.css readme.txt
	scp proteuswidgets.tar.gz pt:./

	# extract to sandbox
	ssh primoz@pt "rm -rf ~/root/sandbox.proteusthemes.com/wp-content/plugins/proteuswidgets/* && tar xf proteuswidgets.tar.gz -C ~/root/sandbox.proteusthemes.com/wp-content/plugins/proteuswidgets"

	# extract to prod
	ssh primoz@pt "rm -rf ~/root/demo.proteusthemes.com/wp-content/plugins/proteuswidgets/* && tar xf proteuswidgets.tar.gz -C ~/root/demo.proteusthemes.com/wp-content/plugins/proteuswidgets"

	rm proteuswidgets.tar.gz