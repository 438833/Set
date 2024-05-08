<?php

$ds = DIRECTORY_SEPARATOR;

$app = Set::app(
	array(
		realpath(__DIR__.$ds.'..')
		, realpath(__DIR__.$ds.'..'.$ds.'..'.$ds.'.set')
	)
);

if(file_exists(__DIR__.$ds.'config.php') === false)
{
	Set::halt(500, '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" /><pre style="background: #222; color: #98fb98; padding:20px; font-size: 16px;">Файл конфигурации не найден. Для начала создайте файл config.php в каталоге .get/config</pre>');
}

$config = require 'config.php';

$app->set('config', $config);

require 'services.php';
require 'routes.php';

$app->before(
	'start'
	, function()
	{
		
	}
);

?>