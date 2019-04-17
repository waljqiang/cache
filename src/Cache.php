<?php
namespace Nova\Cache;

use Nova\Cache\Exceptions\CacheException;

class Cache{

	/**
	 * 操作句柄
	 * @var string
	 */
	protected $handler;

	/**
	 * 缓存类型
	 * @var string
	 */
	protected $type;

	/**
	 * 缓存连接参数
	 * @var [type]
	 */
	protected $parameters;

	/**
	 * 缓存控制参数
	 * @var array
	 */
	protected $options;

	/**
	 * 缓存实例
	 * @var array
	 */
	private static $_instance;

	public static function getInstance($type,$parameters = [],$options = []){
		$guid = $type . self::to_guid_string($options);
		if(!isset(self::$_instance[$guid])){
			$cache = new Cache();
			$cache->type = $type;
			$cache->parameters = $parameters;
			$cache->options = $options;
			self::$_instance[$guid] = $cache->init();
		}
		return self::$_instance[$guid];
	}

	private function init(){
		$class = strpos($this->type,'\\') ? $this->type : 'Nova\\Cache\\Driver\\' . ucwords(strtolower($this->type));
		if(!class_exists($class))
			throw new CacheException("No Driver",CacheException::NODRIVER);	
		$cache = new $class($this->parameters,$this->options);
		return $cache;
	}

	private static function to_guid_string($mix){
	    if (is_object($mix)) {
	        return spl_object_hash($mix);
	    } elseif (is_resource($mix)) {
	        $mix = get_resource_type($mix) . strval($mix);
	    } else {
	        $mix = serialize($mix);
	    }
	    return md5($mix);
	}
}