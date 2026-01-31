<?php

declare(strict_types=1);

namespace BlueCache\Storage;

use Psr\Cache\CacheItemInterface;
use BlueCache\CacheException;
use Redis as RedisOrigin;

class Redis extends Common implements StorageInterface
{
    /**
     * @var array
     */
    protected array $params = [
        'storage_servers' => ['127.0.0.1', 6379, 0],
    ];

    /**
     * @var RedisOrigin
     */
    protected RedisOrigin $redis;

    /**
     * @param array $params
     * @throws CacheException
     */
    public function __construct(array $params = [])
    {
        $this->params = \array_merge($this->params, $params);

        try {
            $this->redis = new RedisOrigin();
            $this->redis->connect($this->params['storage_servers'][0], $this->params['storage_servers'][1]);
            $this->redis->select($this->params['storage_servers'][2]);
        } catch (\Throwable $exception) {
            throw new CacheException('Redis exception: ' . $exception->getMessage());
        }
    }

    /**
     * @param CacheItemInterface $item
     * @return bool
     */
    public function store(CacheItemInterface $item): bool
    {
        $data = \serialize($item);
        $key = $item->getKey();

        if (isset($this->currentCache[$key])) {
            unset($this->currentCache[$key]);
        }

        $this->redis->set($key, $data);

        return true;
    }

    /**
     * @param string|null $name
     * @return bool
     */
    public function clear(string|null $name = null): bool
    {
        if (\is_null($name)) {
            $this->currentCache = [];
            return $this->redis->flushAll();
        }

        return $this->delete($name);
    }

    /**
     * @param string $key
     * @return CacheItemInterface|null
     */
    protected function getCacheItem(string $key): ? CacheItemInterface
    {
        if (!isset($this->currentCache[$key])) {
            if ($this->redis->get($key) !== false) {
                return $this->getUnserializedCacheItem($key);
            }

            return null;
        }

        return $this->currentCache[$key];
    }

    /**
     * @param string $key
     * @return bool|string
     */
    protected function getCacheContent(string $key): bool|string
    {
        return $this->redis->get($key);
    }

    /**
     * @param string $key
     * @return bool
     */
    protected function delete(string $key): bool
    {
        unset($this->currentCache[$key]);
        $val = $this->redis->del($key);

        return (\is_int($val) && $val > 0) || $val instanceof RedisOrigin;
    }
}
