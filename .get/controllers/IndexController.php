<?php

class IndexController
{

	protected $language = 'ru';

	protected $app;

	public function __construct($app)
	{
		$this->app = $app;
	}
}

?>