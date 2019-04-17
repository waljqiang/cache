<?php
require_once __DIR__ . '/../autoload.php';
require_once __DIR__ . '/../vendor/autoload.php';

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