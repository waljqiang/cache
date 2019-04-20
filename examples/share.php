<?php
require_once __DIR__ . '/../autoload.php';
require_once __DIR__ . '/../vendor/autoload.php';

$config = [
	'type' => 'redis',
	'parameters' => [
		'scheme' => 'tcp',
		'host' => '127.0.0.1',
		'port' => 6379,
		'database' => 1,
		'password' => '1f494c4e0df9b837dbcc82eebed35ca3f2ed3fc5f6428d75bb542583fda2170f'
	],
	'options' => [
		'profile' => '2.8',
		'prefix' => 'yuncore:'
	]
];