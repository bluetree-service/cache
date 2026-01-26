<?php

declare(strict_types=1);

/**
 * Allows to manage cache
 * cache structure (module_code/cache_code.cache)
 *
 * @package     Blue
 * @subpackage  Cache
 * @author      chajr <chajr@bluetree.pl>
 */

namespace BlueCache;

use Psr\Cache\CacheItemPoolInterface;
use Psr\Cache\CacheItemInterface;

class Cache implements CacheItemPoolInterface
{
    use Common;

    /**
     * @var array
     */
    protected array $deferred = [];

    /**
     * @var array
     */
    protected array $cacheCommitExceptions = [];

    /**
     * @param string $key
     * @return null|CacheItemInterface
     */
    public function getItem($key): ?CacheItemInterface
    {
        if ($this->hasItem($key)) {
            return $this->storage->restore($key);
        }

        return null;
    }

    /**
     * @param array $keys
     * @return array
     */
    public function getItems(array $keys = []): array
    {
        $list = [];

        foreach ($keys as $name) {
            $list[$name] = $this->getItem($name);
        }

        return $list;
    }

    /**
     * @param string $key
     * @return bool
     */
    public function hasItem($key): bool
    {
        return $this->storage->exists($key);
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
     * @param string $key
     * @return bool
     * @throws CacheException
     */
    public function deleteItem($key): bool
    {
        return $this->storage->clear($key);
    }

    /**
     * @param array $keys
     * @return bool
     */
    public function deleteItems(array $keys): bool
    {
        return $this->storage->clearMany($keys);
    }

    /**
     * @param CacheItemInterface $item
     * @return bool
     * @throws CacheException
     */
    public function save(CacheItemInterface $item): bool
    {
        return $this->storage->store($item);
    }

    /**
     * Keep cache items to store it later
     *
     * @param CacheItemInterface $item
     * @return bool
     */
    public function saveDeferred(CacheItemInterface $item): bool
    {
        $this->deferred[] = $item;
        return true;
    }

    /**
     * Store all cache items added by saveDeferred
     *
     * @return bool
     * @throws CacheException
     */
    public function commit(): bool
    {
        $this->cacheCommitExceptions = [];
        $flag = true;

        foreach ($this->deferred as $item) {
            if (!$this->commitSave($item)) {
                $flag = false;
            }
        }

        if (!empty($this->cacheCommitExceptions)) {
            throw new CacheException(
                'Error on saving cache items: ' . \implode('; ', $this->cacheCommitExceptions)
            );
        }

        $this->deferred = [];

        return $flag;
    }

    /**
     * @param CacheItemInterface $item
     * @return bool
     */
    protected function commitSave(CacheItemInterface $item): bool
    {
        try {
            return $this->save($item);
        } catch (CacheException $exception) {
            $this->cacheCommitExceptions[] = $exception->getMessage();

            return false;
        }
    }

    /**
     * @throws CacheException
     */
    public function __destruct()
    {
        $this->commit();
    }
}
