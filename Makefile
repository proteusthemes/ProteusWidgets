.PHONY: deploy

deploy:
	tar czf proteuswidgets.tar.gz proteuswidgets.php widgets/
	scp proteuswidgets.tar.gz pt:./
	ssh primoz@pt "rm -rf ~/root/sandbox.proteusthemes.com/wp-content/plugins/proteuswidgets/* && tar xf proteuswidgets.tar.gz -C ~/root/sandbox.proteusthemes.com/wp-content/plugins/proteuswidgets"
	rm proteuswidgets.tar.gz