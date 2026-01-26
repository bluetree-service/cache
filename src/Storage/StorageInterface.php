<?php

declare(strict_types=1);

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
    public function store(CacheItemInterface $item): bool;

    /**
     * @param array|string $name
     * @return array|null|CacheItemInterface
     */
    public function restore(array|string $name): array|null|CacheItemInterface;

    /**
     * @param string $name
     * @return bool
     */
    public function exists(string $name): bool;

    /**
     * @param string|null $name
     * @return bool
     * @throws \BlueCache\CacheException
     */
    public function clear(string|null $name = null): bool;

    /**
     * @param iterable $list
     * @return bool
     */
    public function clearMany(iterable $list): bool;
}
