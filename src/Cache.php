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

use Exception;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Cache\CacheItemInterface;

class Cache implements CacheItemPoolInterface
{
    protected $config = [
        'expire_time' => 86400,
        'cache_config_time' => 1,
        'storage_class' => 'file',
        'storage_directory' => './var/cache',
    ];

    /**
     * check that cache directory exist and create it if not
     */
    public function __construct(array $config = [])
    {
        $this->config = array_merge($this->config, $config);
        
        $this->storage = 'register new storage ??';
    }

    public function getItem($name)
    {
        if ($this->hasItem()) {
            return $this->storage->get($name);
        }

        return null;
    }

    public function getItems(array $names = [])
    {
        $list = [];

        foreach ($names as $name) {
            $list[$name] = $this->getItem($name);
        }

        return $list;
    }

    public function hasItem($name)
    {
        $this->storage->has($name);
    }

    public function clear()
    {
        $this->storage->clear();

        return $this;
    }

    public function deleteItem($name)
    {
        $this->storage->delete($name);

        return $this;
    }

    public function deleteItems(array $names)
    {
        $this->storage->deleteItems($names);

        return $this;
    }

    public function save(CacheItemInterface $item)
    {
        
    }

    public function saveDeferred(CacheItemInterface $item)
    {
        
    }

    public function commit()
    {
        
    }

    /**
     * get cached configuration if it exists
     * 
     * @param string $cacheCode
     * @return bool|mixed
     */
    public function getCache($cacheCode)
    {
        $file = CORE_CACHE . $cacheCode . '.cache';
        if (!file_exists($file)) {
            return false;
        }

        if ($this->checkCachedTimes($file)) {
            return file_get_contents($file);
        }

        return false;
    }

    /**
     * add data to cache file, or create it
     * 
     * @param string $cacheCode
     * @param mixed $data
     * @return bool
     */
    public function setCache($cacheCode, $data)
    {
        $file = CORE_CACHE . $cacheCode . '.cache';
        return (bool)file_put_contents($file, $data);
    }

    /**
     * check cached file time
     * 
     * @param string $file
     * @return bool
     */
    protected function checkCachedTimes($file)
    {
        $coreConfig = Loader::getConfiguration();
        $currentTime = time();
        $fileTime = filemtime($file);

        if ($coreConfig) {
            $validTime = $coreConfig->getCore()->getCacheTime();
        } else {
            $validTime = self::CACHE_CONFIG_TIME;
        }

        $expireTime = ($validTime * self::CACHE_BASE_TIME) + $fileTime;

        if ($expireTime > $currentTime) {
            return true;
        }

        return false;
    }
}
