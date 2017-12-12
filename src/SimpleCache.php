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

        if (\is_null($cacheItem)) {
            return $default;
        }

        return $cacheItem->get();
    }

    /**
     * @param string $key
     * @param mixed $value
     * @param \DateTimeInterface|\DateInterval|null|int $ttl
     * @return bool
     * @throws \BlueCache\CacheException
     */
    public function set($key, $value, $ttl = null)
    {
        $item = (new CacheItem($key))->set($value);

        if (!\is_null($ttl)) {
            $item->expiresAfter($ttl);
        }

        return $this->storage->store($item);
    }

    /**
     * @param string $key
     * @return bool
     * @throws \BlueCache\CacheException
     */
    public function delete($key)
    {
        return $this->storage->clear($key);
    }

    /**
     * @return bool
     * @throws \BlueCache\CacheException
     */
    public function clear()
    {
        return $this->storage->clear();
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
     * @return bool
     * @throws \BlueCache\CacheException
     */
    public function setMultiple($values, $ttl = null)
    {
        $flag = true;

        foreach ($values as $key => $data) {
            $isSet = $this->set($key, $data, $ttl);

            if (!$isSet) {
                $flag = false;
            }
        }

        return $flag;
    }

    /**
     * @param iterable $keys
     * @return bool
     */
    public function deleteMultiple($keys)
    {
        return $this->storage->clearMany($keys);
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
