<?php
namespace Support;

use Phalcon\Cache\Frontend\Data as FrontData;

class CacheAdapter
{
    private $Adapter;

    private $Cache;

    public function __construct()
    {
        $config             = \Phalcon\DI::getDefault()['config'];
        $this->Adapter      = $config->get("application")->get("cacheAdapter");
        $this->Provision    = \Phalcon\DI::getDefault()->getShared("provision");

        $this->configCache();
    }

    private function configCache()
    {
        switch ($this->Adapter) {
          case 'file':
                $this->configFileAdapter();
            break;

          case 'memcached':
            // TODO: implement MemcachedAdapter();
            break;

          case 'apc':
            // TODO: implement APCAdapter();
            break;

          case 'redis':
              // TODO: implement RedisAdapter();
              break;
        }
    }

    private function configFileAdapter()
    {
        $frontCache = new FrontData(
              [
                  'lifetime' => 120,
              ]
          );

        $this->Cache = new \Phalcon\Cache\Backend\File(
              $frontCache,
              [
                  'cacheDir' => '../app/cache/',
              ]
          );
    }

    private function configMemcachedAdapter()
    {
    }

    private function configApcAdapter()
    {
    }

    private function configRedisAdapter()
    {
    }

    public function get($key = false, $lifetime = false)
    {
        return $this->Cache->get($key);
    }

    public function save($key, $data)
    {
        $lifeTime = ($this->Provision->getConfig("cacheTime") !== null)? $this->Provision->getConfig("cacheTime") : DEFAULT_CACHE_TIME;
        return $this->Cache->save($key, $data, $lifeTime);
    }
}
