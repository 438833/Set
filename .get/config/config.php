<?php

date_default_timezone_set('UTC');

if(function_exists('mb_internal_encoding') === true)
{
	mb_internal_encoding('UTF-8');
}
if(function_exists('mb_regex_encoding') === true)
{
	mb_regex_encoding('UTF-8');
}
if(function_exists('mb_http_output') === true)
{
	mb_http_output('UTF-8');
}
if(function_exists('mb_language') === true)
{
	mb_language('uni');
}

if(function_exists('setlocale') === true)
{
	setlocale(LC_ALL, 'ru_RU.UTF-8', 'Russian_Russia.65001');
}

if(empty($app))
{
	$app = Set::app();
}

$app->set('set.log_errors', true);
$app->set('set.template.path', realpath(__DIR__.DIRECTORY_SEPARATOR.'..').DIRECTORY_SEPARATOR.'template');

return array(
	'database' => array(
		'preferences' => array(
			'host' => 'localhost'
			, 'dbname' => 'todom_preferences_db'
			, 'user' => 'todom_user'
			, 'password' => 'q8!mcVUr3XsRksQd'
			, 'charset' => 'utf8mb4'
		)
	)
);

?>