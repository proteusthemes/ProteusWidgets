<?php

class WidgetAboutUsTest extends WP_UnitTestCase {

	var $widget_params_array = array(

		'minimal params' => array(
			'instance'     => array(
				'autocycle' => 'no',
			),
			'args'         => array(
				'widget_id' => 'widget_pw_about_us_999',
			),
			'expectedHTML' => '<div class="widget widget-about-us"><div class="carousel slide" data-interval="false" data-ride="carousel" id="carousel-people-widget_pw_about_us_999"><div class="carousel-inner" role="listbox"><div class="item active"><h5 class="about-us__name"/><p class="about-us__description"/></div></div></div></div>',
		),

		'custom autocycle params' => array(
			'instance'     => array(
				'autocycle' => 'yes',
				'interval'  => 1235,
			),
			'args'         => array(
				'widget_id' => 'widget_pw_about_us_999',
			),
			'expectedHTML' => '<div class="widget widget-about-us"><div class="carousel slide" data-interval="1235" data-ride="carousel" id="carousel-people-widget_pw_about_us_999"><div class="carousel-inner" role="listbox"><div class="item active"><h5 class="about-us__name"/><p class="about-us__description"/></div></div></div></div>',
		),

		'1 person, no link' => array(
			'instance'     => array(
				'autocycle' => 'no',
				'people'    => array(
					array(
						'id'          => 76,
						'tag'         => 'PhD',
						'image'       => 'http://lorempixel.com/120/100/nature',
						'name'        => 'Primoz Cigler',
						'description' => 'I am developer since I was 3 months old',
						'link'        => '',
					),
				),
			),
			'args'         => array(
				'widget_id' => 'widget_pw_about_us_999',
			),
			'expectedHTML' => '<div class="widget widget-about-us"><div class="carousel slide" data-interval="false" data-ride="carousel" id="carousel-people-widget_pw_about_us_999"><div class="carousel-inner" role="listbox"><div class="item active"><div class="about-us__tag">PhD</div><img alt="About us image" class="about-us__image" src="http://lorempixel.com/120/100/nature"/><h5 class="about-us__name">Primoz Cigler</h5><p class="about-us__description">I am developer since I was 3 months old</p></div></div></div></div>',
		),

		'1 person, with link' => array(
			'instance'     => array(
				'autocycle' => 'no',
				'people'    => array(
					array(
						'id'          => 76,
						'tag'         => 'PhD',
						'image'       => 'http://lorempixel.com/120/100/nature',
						'name'        => 'Primoz Cigler',
						'description' => 'I am developer since 3 months old',
						'link'        => '//primozcigler.net',
					),
				),
			),
			'args'         => array(
				'widget_id' => 'widget_pw_about_us_999',
			),
			'expectedHTML' => '<div class="widget widget-about-us"><div class="carousel slide" data-interval="false" data-ride="carousel" id="carousel-people-widget_pw_about_us_999"><div class="carousel-inner" role="listbox"><div class="item active"><a class="about-us__tag" href="//primozcigler.net">PhD</a><img alt="About us image" class="about-us__image" src="http://lorempixel.com/120/100/nature"/><h5 class="about-us__name">Primoz Cigler</h5><p class="about-us__description">I am developer since 3 months old</p><a class="read-more  about-us__link" href="//primozcigler.net">Read more</a></div></div></div></div>',
		),

		'2 people, one with everything, one just with title' => array(
			'instance'     => array(
				'autocycle' => 'yes',
				'interval'  => 42,
				'people'    => array(
					array(
						'id'          => 76,
						'tag'         => 'PhD',
						'image'       => 'http://lorempixel.com/120/100/nature',
						'name'        => 'Primoz Cigler',
						'description' => 'I am developer since 3 months old',
						'link'        => 'http://primozcigler.net/projects/index.html',
					),
					array(
						'id'          => 120,
						'tag'         => '',
						'image'       => '',
						'name'        => 'Marko Capuder',
						'description' => '',
						'link'        => '',
					),
				),
			),
			'args'         => array(
				'widget_id' => 'widget_pw_about_us_tmp',
			),
			'expectedHTML' => '<div class="widget widget-about-us"><div class="carousel slide" data-interval="42" data-ride="carousel" id="carousel-people-widget_pw_about_us_tmp"><div class="carousel-inner" role="listbox"><div class="item active"><a class="about-us__tag" href="http://primozcigler.net/projects/index.html">PhD</a><img alt="About us image" class="about-us__image" src="http://lorempixel.com/120/100/nature"/><h5 class="about-us__name">Primoz Cigler</h5><p class="about-us__description">I am developer since 3 months old</p><a class="read-more  about-us__link" href="http://primozcigler.net/projects/index.html">Read more</a></div><div class="item "><h5 class="about-us__name">Marko Capuder</h5><p class="about-us__description"/></div></div></div><div class="about-us__navigation"><a class="person__carousel  person__carousel--left about-us__navigation__left" data-slide="prev" href="#carousel-people-widget_pw_about_us_tmp"><i aria-hidden="true" class="fa  fa-chevron-left"/><span class="sr-only" role="button">Previous</span></a><a class="person__carousel  person__carousel--right about-us__navigation__right" data-slide="next" href="#carousel-people-widget_pw_about_us_tmp"><i aria-hidden="true" class="fa  fa-chevron-right"/><span class="sr-only" role="button">Next</span></a></div></div>',
		),
	);

	function setUp() {
		parent::setUp();
		$this->AboutUs = new PW_About_Us();

	}

	function test_class_is_available_and_instances() {
		$this->assertInstanceOf( 'PW_About_Us', $this->AboutUs );
		$this->assertInstanceOf( 'PW_Widget', $this->AboutUs );
		$this->assertInstanceOf( 'WP_Widget', $this->AboutUs );
	}

	function test_widget_different_outputs() {

		foreach ( $this->widget_params_array as $test_desc => $params ) {
			ob_start();
			the_widget( 'PW_About_Us', $params['instance'], $params['args'] );
			$output = ob_get_clean();

			$this->assertNotEmpty( $output, $test_desc );

			$expected = new DOMDocument;
			$expected->preserveWhiteSpace = false;
			$expected->loadXML( $params['expectedHTML'] );

			$actual = new DOMDocument;
			$actual->preserveWhiteSpace = false;
			$actual->loadXML( $output );

			$this->assertEquals( $expected, $actual, $test_desc );
		}

	}

}