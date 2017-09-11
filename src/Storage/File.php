<?php

namespace BlueCache\Storage;

use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheException;

class File implements StorageInterface
{
    /**
     * @var array
     */
    protected $params = [
        'cache_path' => './var/cache',
    ];

    /**
     * @var array
     */
    protected $currentCache = [];

    /**
     * @param array $params
     */
    public function __construct(array $params = [])
    {
        $this->params = array_merge($this->params, $params);
    }

    /**
     * @param CacheItemInterface $item
     * @return $this
     */
    public function store(CacheItemInterface $item)
    {
        $data = serialize($item);
        $key = $item->getKey();

        $cacheFile = $this->params['cache_path'] . DIRECTORY_SEPARATOR . $key . '.cache';
        $dir = $this->params['cache_path'];

        if (!file_exists($dir) && !mkdir($dir) && !is_dir($dir)) {
            throw new CacheException('Unable to create cache directory: ' . $this->params['cache_path']);
        }

        if (!file_put_contents($cacheFile, $data)) {
            throw new CacheException('Unable to save log file: ' . $cacheFile);
        }

        return $this;
    }

    /**
     * @param array|string $names
     * @return array|CacheItemInterface
     */
    public function restore($names)
    {
        $list = [];

        if (is_array($names)) {
            foreach ($names as $name) {
                $list[$name] = $this->getItem($name);
            }
        } else {
            return $this->getItem($names);
        }

        return $list;
    }

    /**
     * @param array|string|null $names
     * @return $this
     */
    public function clear($names = null)
    {
        switch (true) {
            case is_null($names):
                $cacheDir = $this->params['cache_path'] . DIRECTORY_SEPARATOR;

                return $this->clearMany(glob($cacheDir . '*.cache'), true);

            case is_array($names):
                return $this->clearMany($names);

            case is_string($names):
                return $this->delete($names);

            default:
                throw new CacheException('Invalid expire type.');
                break;
        }
    }

    /**
     * @param string $key
     * @return bool
     */
    public function exists($key)
    {
        /** @var CacheItemInterface|null $item */
        $item = $this->getCacheItem($key);

        if (is_null($item)) {
            return false;
        }

        return $item->isHit();
    }

    /**
     * @param string $key
     * @return CacheItemInterface|null
     */
    protected function getItem($key)
    {
        if ($this->exists($key)) {
            return $this->currentCache[$key];
        }

        return null;
    }

    /**
     * @param string $key
     * @return CacheItemInterface|null
     */
    protected function getCacheItem($key)
    {
        if (!isset($this->currentCache[$key])) {
            if (file_exists($this->getFilePath($key))) {
                /** @var CacheItemInterface $item */
                $item = unserialize(file_get_contents($this->getFilePath($key)));

                if (!$item->isHit()) {
                    $this->delete($key);
                    return null;
                }

                $this->currentCache[$key] = $item;
                return $item;
            }

            return null;
        }

        return $this->currentCache[$key];
    }

    /**
     * @param array $list
     * @return $this
     */
    protected function clearMany(array $list, $removeExtension = false)
    {
        foreach ($list as $name) {
            if ($removeExtension) {
//                $name = rtrim($name, '\.cache');
            }

            $this->delete($name);
        }

        return $this;
    }

    /**
     * @param string $key
     * @return $this
     */
    protected function delete($key)
    {
        unlink($this->getFilePath($key));

        return $this;
    }

    protected function getFilePath($key)
    {
        return $this->params['cache_path'] . DIRECTORY_SEPARATOR . $key . '.cache';
    }
}
