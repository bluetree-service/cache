<?php
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
    protected $deferred = [];

    /**
     * @var array
     */
    protected $cacheCommitExceptions = [];

    /**
     * @param string $name
     * @return null|CacheItemInterface
     */
    public function getItem($name)
    {
        if ($this->hasItem($name)) {
            return $this->storage->restore($name);
        }

        return null;
    }

    /**
     * @param array $names
     * @return array
     */
    public function getItems(array $names = [])
    {
        $list = [];

        foreach ($names as $name) {
            $list[$name] = $this->getItem($name);
        }

        return $list;
    }

    /**
     * @param string $name
     * @return bool
     */
    public function hasItem($name)
    {
        return $this->storage->exists($name);
    }

    /**
     * @return bool
     * @throws CacheException
     */
    public function clear()
    {
        return $this->storage->clear();
    }

    /**
     * @param string $name
     * @return bool
     * @throws CacheException
     */
    public function deleteItem($name)
    {
        return $this->storage->clear($name);
    }

    /**
     * @param array $names
     * @return bool
     */
    public function deleteItems(array $names)
    {
        return $this->storage->clearMany($names);
    }

    /**
     * @param CacheItemInterface $item
     * @return bool
     * @throws CacheException
     */
    public function save(CacheItemInterface $item)
    {
        return $this->storage->store($item);
    }

    /**
     * Keep cache items to store it later
     *
     * @param CacheItemInterface $item
     * @return bool
     */
    public function saveDeferred(CacheItemInterface $item)
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
    public function commit()
    {
        $cacheExceptions = [];
        $this->cacheCommitExceptions = [];
        $flag = true;

        foreach ($this->deferred as $item) {
            if (!$this->commitSave($item)) {
                $flag = false;
            }
        }

        if (!empty($this->cacheCommitExceptions)) {
            throw new CacheException('Error on saving cache items: ' . implode('; ', $cacheExceptions));
        }

        $this->deferred = [];

        return $flag;
    }

    /**
     * @param CacheItemInterface $item
     * @return bool
     */
    protected function commitSave(CacheItemInterface $item)
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
