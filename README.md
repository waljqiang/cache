# Nova\Cache
## Features
    
* 支持常用缓存。
* 目前仅提供redis缓存方式，redis缓存使用predis/predis，github地址:https://github.com/nrk/predis,packagelist地址:https://packagist.org/packages/predis/predis。
* 可自行扩展使用自己的缓存类

## 使用
* redis作为缓存使用

    1.引入自动加载。
```
    require_once __DIR__ . '/../autoload.php';
    require_once __DIR__ . '/../vendor/autoload.php';
```
    
     2.缓存配置type配置成redis
```
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
```
    3.获取缓存实例
```
    $cache = Nova\Cache\Cache::getInstance($config['type'],$config['parameters'],$config['options']);
```

    4.按照predis/predis文档调用相关方法即可
```
    $cache->set('aa',100);
    $cache->get('aa');
    $cache->set('dd',200);
    $cache->getMultiple(['aa','dd','cc'],'adf');
    $cache->setMultiple(['ff' => 300,'ee' => 400]);
    $cache->deleteMultiple(['ff','ee','mm']);
    $cachea->has('aa');
    $cache->lpush('aaa',500);
    $cache->rpop('aaa');
    $cache->clear();
```
* 扩展自己缓存类

    1.必须继承Nova\Cache\Cache类。
    
    2.必须实现Nova\Cache\Driver\CacheInterface接口。


