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
     * @param string $name
     * @return CacheItemInterface|null
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
     * @return $this
     * @throws CacheException
     */
    public function clear()
    {
        $this->storage->clear();

        return $this;
    }

    /**
     * @param string $name
     * @return $this
     * @throws CacheException
     */
    public function deleteItem($name)
    {
        $this->storage->clear($name);

        return $this;
    }

    /**
     * @param array $names
     * @return $this
     * @throws CacheException
     */
    public function deleteItems(array $names)
    {
        $this->storage->clear($names);

        return $this;
    }

    /**
     * @param CacheItemInterface $item
     * @return $this
     * @throws CacheException
     */
    public function save(CacheItemInterface $item)
    {
        $this->storage->store($item);

        return $this;
    }

    /**
     * Keep cache items to store it later
     *
     * @param CacheItemInterface $item
     * @return $this
     */
    public function saveDeferred(CacheItemInterface $item)
    {
        $this->deferred[] = $item;
        return $this;
    }

    /**
     * Store all cache items added by saveDeferred
     *
     * @return $this
     * @throws CacheException
     */
    public function commit()
    {
        $cacheExceptions = [];

        foreach ($this->deferred as $item) {
            try {
                $this->save($item);
            } catch (CacheException $exception) {
                $cacheExceptions[] = $exception->getMessage();
            }
        }

        if (!empty($cacheExceptions)) {
            throw new CacheException('Error on saving cache items: ' . implode('; ', $cacheExceptions));
        }

        $this->deferred = [];

        return $this;
    }

    /**
     * @throws CacheException
     */
    public function __destruct()
    {
        $this->commit();
    }
}
