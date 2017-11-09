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
     * @param null|int $ttl
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

    /**
     * @param string $key
     * @return $this
     * @throws \BlueCache\CacheException
     */
    public function delete($key)
    {
        $this->storage->clear($key);

        return $this;
    }

    /**
     * @return $this
     * @throws \BlueCache\CacheException
     */
    public function clear()
    {
        $this->storage->clear();

        return $this;
    }

    /**
     * @param iterable $keys
     * @param null|mixed $default
     * @return array
     */
    public function getMultiple($keys, $default = null)
    {
        $list = [];

        foreach ($keys as $key) {
            $list[$key] = $this->get($key, $default);
        }

        return $list;
    }

    /**
     * @param iterable $values
     * @param null|int $ttl
     * @return $this
     * @throws \BlueCache\CacheException
     */
    public function setMultiple($values, $ttl = null)
    {
        foreach ($values as $key => $data) {
            $this->set($key, $data, $ttl);
        }

        return $this;
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
