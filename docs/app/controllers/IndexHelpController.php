<?php

namespace help\app\controllers;

class IndexHelpController
{
	private $app;
	protected $time;

	public function __construct($app, $time)
	{
		$this->app = $app;
		$this->time = $time;
	}

	public function getPage($page)
	{
		$this->app->render('header', array('t' => $this->time), 'header_content');
		$this->app->render('footer', array('t' => $this->time), 'footer_content');
		$this->app->render($page);
	}

}

?>