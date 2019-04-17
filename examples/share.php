<?php
require_once __DIR__ . '/../src/Cache.php';
use Nova\Cache\Cache;

$config = [
	'type' => 'redis',
	'parameters' => [
		'scheme' => 'tcp',
		'host' => '192.168.111.188',
		'port' => 6379,
		'database' => 1
	],
	'options' => [
		'profile' => '2.8',
		'prefix' => 'yuncore:'
	]
];