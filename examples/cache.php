<?php
require __DIR__ . '/share.php';
try{
	$a = Cache::getInstance($config['type'],$config['parameters'],$config['options']);
	$b = $a->set('aa',100);
	$c = $a->get('aa');
	$d = $a->set('dd',200);
	$f = $a->getMultiple(['aa','dd','cc'],'adf');
	$e = $a->setMultiple(['ff' => 300,'ee' => 400]);
	$m = $a->deleteMultiple(['ff','ee','mm']);
	$l = $a->has('aa');
	$o = $a->lpush('aaa',500);
	$p = $a->rpop('aaa');
	//$n = $a->clear();
	var_dump($b);
	var_dump($c);
	var_dump($d);
	var_dump($f);
	var_dump($e);
	var_dump($m);
	var_dump($l);
	var_dump($o);
	var_dump($p);
	//var_dump($n);
}catch(\Exception $e){
	var_dump($e);
}