<?php

namespace BlueCache;

use Psr\SimpleCache\CacheInterface;

class SimpleCache implements CacheInterface
{
    use Common;

    /**
     * @param string $key
     * @param mixed $default
     * @return array|null|\Psr\Cache\CacheItemInterface
     */
    public function get($key, $default = null)
    {
        $cacheItem = $this->storage->restore($key);

        if (is_null($cacheItem)) {
            return $default;
        }

        return $cacheItem->get();
    }

    /**
     * @param string $key
     * @param mixed $value
     * @param null $ttl
     * @return $this
     * @throws \BlueCache\CacheException
     */
    public function set($key, $value, $ttl = null)
    {
        $config = [];

        if (!is_null($ttl)) {
            $config['expire'] = $ttl;
        }

        $item = (new CacheItem($key, $config))->set($value);

        $this->storage->store($item);

        return $this;
    }

    public function delete($key)
    {

    }

    public function clear()
    {

    }

    public function getMultiple($keys, $default = null)
    {

    }

    public function setMultiple($values, $ttl = null)
    {

    }

    public function deleteMultiple($keys)
    {

    }

    /**
     * @param string $key
     * @return bool
     */
    public function has($key)
    {
        return $this->storage->exists($key);
    }
}
