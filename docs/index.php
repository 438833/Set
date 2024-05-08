<?php

use help\app\controllers\IndexHelpController;

$ds = DIRECTORY_SEPARATOR;

$app = Set::app(
	array(
		realpath(__DIR__.$ds.'..'.$ds.'.set')
		, realpath(__DIR__.$ds)
	)
);

$app->set('set.template.path', realpath(__DIR__.$ds).$ds.'app'.$ds.'html');

$t = time();

$controller = new IndexHelpController($app, $t);
	
$app->route(
	'/(index(/*|.php)|about)'
	, function() use($controller)
	{
		$controller->getPage('index');
	}
);
	
$app->route(
	'/@page'
	, function($page) use($controller)
	{
		$controller->getPage($page);
	}
);

?>