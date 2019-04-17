<?php
namespace Nova\Cache\Exceptions;
class CacheException extends \Exception{
	const NODRIVER = 400100;
	const CACHERROR = 400101;
}