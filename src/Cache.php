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
    /**
     * @var \BlueCache\Storage\StorageInterface
     */
    protected $storage;

    /**
     * @var array
     */
    protected $config = [
        'expire_time' => 86400,
        'cache_config_time' => 1,
        'storage_class' => '\BlueCache\Storage\File',
        'storage_directory' => './var/cache',
    ];

    /**
     * check that cache directory exist and create it if not
     *
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        $this->config = array_merge($this->config, $config);

        $this->registerStorage();
    }

    public function getItem($name)
    {
        if ($this->hasItem($name)) {
            return $this->storage->restore($name);
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
        $this->storage->exists($name);
    }

    public function clear()
    {
        $this->storage->clear();

        return $this;
    }

    public function deleteItem($name)
    {
        $this->storage->clear($name);

        return $this;
    }

    public function deleteItems(array $names)
    {
        $this->storage->clear($names);

        return $this;
    }

    public function save(CacheItemInterface $item)
    {
        $this->storage->store(
            $item->getKey(),
            $item->get()
        );
    }

    public function saveDeferred(CacheItemInterface $item)
    {
        
    }

    public function commit()
    {
        
    }

    /**
     * @return $this
     */
    private function registerStorage()
    {
        if (!$this->storage) {
            if ($this->config['storage_class']
                && $this->config['storage_class'] instanceof \BlueCache\Storage\StorageInterface
            ) {
                $this->storage = $this->config['storage_class'];
            } else {
                $this->storage = new $this->config['storage_class'];
            }
        }

        return $this;
    }
}
