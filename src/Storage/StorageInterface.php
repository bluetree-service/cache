<?php

namespace BlueCache\Storage;

use Psr\Cache\CacheItemInterface;

interface StorageInterface
{
    /**
     * StorageInterface constructor.
     *
     * @param array $params
     */
    public function __construct(array $params);

    /**
     * @param CacheItemInterface $item
     * @return bool
     * @throws \BlueCache\CacheException
     */
    public function store(CacheItemInterface $item);

    /**
     * @param array|string $name
     * @return array|CacheItemInterface
     */
    public function restore($name);

    /**
     * @param string $name
     * @return bool
     */
    public function exists($name);

    /**
     * @param string|null|array $name
     * @return bool
     * @throws \BlueCache\CacheException
     */
    public function clear($name = null);

    /**
     * @param array $list
     * @param bool $isKey
     * @return bool
     */
    public function clearMany(array $list, $isKey = true);
}
