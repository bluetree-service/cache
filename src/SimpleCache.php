<?php

declare(strict_types=1);

namespace BlueCache;

use Psr\SimpleCache\CacheInterface;

class SimpleCache implements CacheInterface
{
    use Common;

    /**
     * @var array
     */
    protected array $multipleSetExceptions = [];

    /**
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function get($key, $default = null): mixed
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
     * @throws CacheException
     */
    public function set($key, $value, $ttl = null): bool
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
     * @throws CacheException
     */
    public function delete($key): bool
    {
        return $this->storage->clear($key);
    }

    /**
     * @return bool
     * @throws CacheException
     */
    public function clear(): bool
    {
        return $this->storage->clear();
    }

    /**
     * @param iterable $keys
     * @param null|mixed $default
     * @return array
     */
    public function getMultiple($keys, $default = null): array
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
     */
    public function setMultiple($values, $ttl = null): bool
    {
        $flag = true;

        foreach ($values as $key => $data) {
            try {
                $this->set($key, $data, $ttl);
            } catch (CacheException $exception) {
                $flag = false;
                $this->multipleSetExceptions[$key] = $exception->getMessage();
            }
        }

        return $flag;
    }

    /**
     * @param iterable $keys
     * @return bool
     */
    public function deleteMultiple($keys): bool
    {
        return $this->storage->clearMany($keys);
    }

    /**
     * @param string $key
     * @return bool
     */
    public function has($key): bool
    {
        return $this->storage->exists($key);
    }

    /**
     * @return array
     */
    public function getMultipleSetExceptions(): array
    {
        return $this->multipleSetExceptions;
    }
}
