<?php

declare(strict_types=1);

namespace BlueCache\Storage;

use Psr\Cache\CacheItemInterface;

abstract class Common
{
    /**
     * @var array
     */
    protected array $currentCache = [];

    /**
     * @param array|string $names
     * @return array|null|CacheItemInterface
     */
    public function restore(array|string $names): array|null|CacheItemInterface
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
    protected function processNames(array $names): array
    {
        $list = [];

        foreach ($names as $name) {
            $list[$name] = $this->getItem($name);
        }

        return $list;
    }

    /**
     * @param string $key
     * @return bool
     */
    public function exists(string $key): bool
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
    protected function getItem(string $key): ? CacheItemInterface
    {
        if ($this->exists($key)) {
            return $this->getCacheItem($key);
        }

        return null;
    }

    /**
     * @param string $key
     * @return null|CacheItemInterface
     */
    protected function getUnserializedCacheItem(string $key): ? CacheItemInterface
    {
        /** @var CacheItemInterface $item */
        $item = \unserialize($this->getCacheContent($key), ['allowed_classes' => true]);

        if (!$item->isHit()) {
            $this->delete($key);
            return null;
        }

        $this->currentCache[$key] = $item;
        return $item;
    }

    /**
     * @param iterable $list
     * @return bool
     */
    public function clearMany(iterable $list): bool
    {
        $flag = true;

        foreach ($list as $name) {
            $deleted = $this->delete($name);

            if (!$deleted) {
                $flag = false;
            }
        }

        return $flag;
    }
}
