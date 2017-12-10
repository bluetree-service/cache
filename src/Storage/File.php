<?php

namespace BlueCache\Storage;

use Psr\Cache\CacheItemInterface;
use BlueCache\CacheException;

class File implements StorageInterface
{
    const CACHE_EXTENSION = '.cache';

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
     * @return bool
     * @throws \BlueCache\CacheException
     */
    public function store(CacheItemInterface $item)
    {
        $data = serialize($item);
        $key = $item->getKey();

        $cacheFile = $this->getFilePath($key);
        $dir = $this->params['cache_path'];

        if (!file_exists($dir) && !is_dir($dir)) {
            throw new CacheException('Unable to create cache directory: ' . $this->params['cache_path']);
        }

        if (!@file_put_contents($cacheFile, $data)) {
            throw new CacheException('Unable to save log file: ' . $cacheFile);
        }

        return true;
    }

    /**
     * @param array|string $names
     * @return array|null|CacheItemInterface
     */
    public function restore($names)
    {
        if (\is_array($names)) {
            return $this->processNames($names);
        }

        return $this->getItem($names);
    }

    /**
     * @param array $names
     * @return array
     */
    protected function processNames(array $names)
    {
        $list = [];

        foreach ($names as $name) {
            $list[$name] = $this->getItem($name);
        }

        return $list;
    }

    /**
     * @param array|string|null $names
     * @return bool
     * @throws \BlueCache\CacheException
     */
    public function clear($names = null)
    {
        switch (true) {
            case \is_null($names):
                $cacheDir = $this->params['cache_path'] . DIRECTORY_SEPARATOR;
                $this->currentCache = [];

                return $this->clearMany(glob($cacheDir . '*.cache'), false);

            case \is_array($names):
                return $this->clearMany($names);

            case \is_string($names):
                return $this->delete($names);

            default:
                throw new CacheException('Invalid type: ' . $names);
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

        if (\is_null($item)) {
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
                return $this->getUnserializedCacheItem($key);
            }

            return null;
        }

        return $this->currentCache[$key];
    }

    /**
     * @param string $key
     * @return null|CacheItemInterface
     */
    protected function getUnserializedCacheItem($key)
    {
        /** @var CacheItemInterface $item */
        $item = unserialize($this->getCacheContent($key));

        if (!$item->isHit()) {
            $this->delete($key);
            return null;
        }

        $this->currentCache[$key] = $item;
        return $item;
    }

    /**
     * @param string $key
     * @return bool|string
     */
    protected function getCacheContent($key)
    {
        return file_get_contents($this->getFilePath($key));
    }

    /**
     * @param array $list
     * @param bool $isKey
     * @return bool
     */
    protected function clearMany(array $list, $isKey = true)
    {
        $flag = true;

        foreach ($list as $name) {
            $deleted = $this->delete($name, $isKey);

            if (!$deleted) {
                $flag = false;
            }
        }

        return $flag;
    }

    /**
     * @param string $key
     * @param bool $isKey
     * @return bool
     */
    protected function delete($key, $isKey = true)
    {
        if ($isKey) {
            unset($this->currentCache[$key]);
            $key = $this->getFilePath($key);
        }

        return unlink($key);
    }

    /**
     * @param string $key
     * @return string
     */
    protected function getFilePath($key)
    {
        return $this->params['cache_path'] . DIRECTORY_SEPARATOR . $key . self::CACHE_EXTENSION;
    }
}
