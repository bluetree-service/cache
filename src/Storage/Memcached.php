<?php

declare(strict_types=1);

namespace BlueCache\Storage;

use Psr\Cache\CacheItemInterface;
use BlueCache\CacheException;
use Memcached as MemcachedOrigin;

class Memcached extends Common implements StorageInterface
{
    /**
     * @var array
     */
    protected array $params = [
        'storage_servers' => [['127.0.0.1', 11211]],
    ];

    /**
     * @var MemcachedOrigin
     */
    protected MemcachedOrigin $memcached;

    /**
     * @param array $params
     * @throws CacheException
     */
    public function __construct(array $params = [])
    {
        $this->params = \array_merge($this->params, $params);

        $this->memcached = new MemcachedOrigin();

        $this->memcached->addServers($this->params['storage_servers']);

        if (!$this->memcached->getVersion()) {
            throw new CacheException('Connection with server is not established');
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

        $this->memcached->set($key, $data);

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
            return $this->memcached->flush();
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
            if ($this->memcached->get($key) !== false) {
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
        return $this->memcached->get($key);
    }

    /**
     * @param string $key
     * @return bool
     */
    protected function delete(string $key): bool
    {
        unset($this->currentCache[$key]);
        return $this->memcached->delete($key);
    }
}
