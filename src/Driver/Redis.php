<?php
namespace Nova\Cache\Driver;

use Nova\Cache\Cache;
use Nova\Cache\Driver\CacheInterface;
use Nova\Cache\Exceptions\CacheException;
use Predis\Client;

class Redis extends Cache implements CacheInterface{

	public function __construct($parameters = [],$options = []){
		try{
			$this->handler = new Client($parameters,$options);
		}catch(\Exception $e){
			throw new CacheException($e->getMessage(),CacheException::CACHERROR);
		}
	}

	/**
	 * Fetches avalue from the cache.
	 *
	 * @param  string $key     The unique key of this item in the cache.
	 * @param  mixed $default Default value to return if the key does not exist.
	 * @return mixed          The value of the item from the cache,or $default in case of cache miss.
	 * @throws \Nova\Cache\Exceptions\CacheException MUST be throw if the $key string is not a legal value.
	 */
	public function get($key,$default = null){
		try{
			return $this->handler->get($key) ? unserialize($this->handler->get($key)) : $default;
		}catch(\Exception $e){
			throw new CacheException($e->getMessage(),CacheException::CACHERROR);
		}
	}

	/**
	 * Persists data in the cache, uniquely referenced by a key with an optional expiration TTL time.
	 *
	 * @param string $key   The key of the item to store.
	 * @param mixed $value The value of the item to store, must be serializable.
	 * @param null|int|\DateInterval $ttl   Optional. The TTL value of this item. If no value is sent and
     *                                      the driver supports TTL then the library may set a default value
     *                                      for it or let the driver take care of that.
     * @return  bool True on success and false on failure.
     * @throws \Nova\Cache\Exceptions\CacheException MUST be throw if the $key string is not a legal value.
	 */
	public function set($key,$value,$ttl = null){
		try{
			$value = serialize($value);
			$result = $ttl ? $this->handler->setex($key,$ttl,$value) : $this->handler->set($key,$value);

			return 'OK' == $result->getPayload() ? true : false;
		}catch(\Exception $e){
			throw new CacheException($e->getMessage(),CacheException::CACHERROR);
		}
	}

	/**
	 * Delete an item from the cache by its unique key.
	 *
	 * @param  string $key The key of the item to store.
	 * @return bool True on success and false on failure.
	 * @throws \Nova\Cache\Exceptions\CacheException MUST be throw if the $key string is not a legal value.
	 */
	public function delete($key){
		try{
			return (bool) $this->handler->del($key);
		}catch(\Exception $e){
			throw new CacheException($e->getMessage(),CacheException::CACHERROR);
		}
	}

	/**
     * Obtains multiple cache items by their unique keys.
     *
     * @param iterable $keys    A list of keys that can obtained in a single operation.
     * @param mixed    $default Default value to return for keys that do not exist.
     *
     * @return iterable A list of key => value pairs. Cache keys that do not exist or are stale will have $default as value.
     *
     * @throws \Psr\SimpleCache\InvalidArgumentException
     *   MUST be thrown if $keys is neither an array nor a Traversable,
     *   or if any of the $keys are not a legal value.
     */
    public function getMultiple($keys, $default = null){
    	try{
    		return array_map(function($value) use ($default){
    			return $value ? unserialize($value) : $default;
    		},$this->handler->pipeline(function($pipe) use ($keys){
    			foreach ($keys as $key) {
    				$pipe->get($key);
    			}
    		}));
    	}catch(\Exception $e){
			throw new CacheException($e->getMessage(),CacheException::CACHERROR);
		}
    }

    /**
     * Persists a set of key => value pairs in the cache, with an optional TTL.
     *
     * @param iterable               $values A list of key => value pairs for a multiple-set operation.
     * @param null|int|\DateInterval $ttl    Optional. The TTL value of this item. If no value is sent and
     *                                       the driver supports TTL then the library may set a default value
     *                                       for it or let the driver take care of that.
     *
     * @return bool True on success and false on failure.
     *
     * @throws \Psr\SimpleCache\InvalidArgumentException
     *   MUST be thrown if $values is neither an array nor a Traversable,
     *   or if any of the $values are not a legal value.
     */
    public function setMultiple($values, $ttl = null){
    	try{
    		$rs = true;
    		$results = $this->handler->pipeline(function($pipe) use ($values,$ttl){
    			foreach ($values as $key => $value) {
    				$ttl ? $pipe->setex($key,$ttl,$value) : $pipe->set($key,$value);
    			}
    		});

    		//某个设置失败回滚
    		foreach ($results as $result) {
    			if('OK' != $result->getPayload()){
    				$this->deleteMultiple(array_keys($values));
    				$rs = false;
    				break;
    			}
    		}
    		return $rs;
    	}catch(\Exception $e){
			throw new CacheException($e->getMessage(),CacheException::CACHERROR);
		}
    }
    
    /**
     * Deletes multiple cache items in a single operation.
     *
     * @param iterable $keys A list of string-based keys to be deleted.
     *
     * @return bool True if the items were successfully removed. False if there was an error.
     *
     * @throws \Psr\SimpleCache\InvalidArgumentException
     *   MUST be thrown if $keys is neither an array nor a Traversable,
     *   or if any of the $keys are not a legal value.
     */
    public function deleteMultiple($keys){
    	try{
    		$this->handler->pipeline(function($pipe) use ($keys){
    			foreach ($keys as $key) {
    				$pipe->del($key);
    			}
    		});
    		return true;
    	}catch(\Exception $e){
			throw new CacheException($e->getMessage(),CacheException::CACHERROR);
		}
    }


	/**
	 * Wipes clean the entire cache's keys.
	 *
	 * @return bool True on success and false on failure.
	 */
	public function clear(){
		try{
			$result = $this->handler->flushdb();
			return 'OK' == $result->getPayload() ? true : false;
		}catch(\Exception $e){
			throw new CacheException($e->getMessage(),CacheException::CACHERROR);
		}
	}

	/**
	 * Determines whether an item is present in the cache.
	 *
	 * @param string $key The key of the item to store.
	 * @return boolean
	 * @throws \Nova\Cache\Exceptions\CacheException MUST be throw if the $key string is not a legal value.
	 */
	public function has($key){
		try{
			return (bool) $this->handler->exists($key);
		}catch(\Exception $e){
			throw new CacheException($e->getMessage(),CacheException::CACHERROR);
		}
	}

	public function __call($method, $args){
		if($this->handler->getProfile()->supportsCommand($method)){
			return call_user_func_array(array($this->handler, $method), $args);
		}
	}
}