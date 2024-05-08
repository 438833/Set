<?php

use set\components\data\PdoWrapper;

$dsn_preferences = "mysql:host={$config['database']['preferences']['host']};dbname={$config['database']['preferences']['dbname']};charset={$config['database']['preferences']['charset']}";

$app->register(
	'db_preferences'
	, PdoWrapper::class
	, array(
		$dsn_preferences
		, $config['database']['preferences']['user']
		, $config['database']['preferences']['password']
	)
);

?>