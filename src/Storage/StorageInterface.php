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
     * @return $this
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
     * @param string|null $name
     * @return $this
     * @throws \BlueCache\CacheException
     */
    public function clear($name = null);
}
