<?php

declare(strict_types=1);

/**
 * Created by PhpStorm.
 * User: michal
 * Date: 10/10/2017
 * Time: 14:27
 */

namespace BlueCache;

use \BlueCache\Storage\{
    File,
    Memcached,
};
use BlueCache\Storage\StorageInterface;

trait Common
{
    /**
     * @var StorageInterface
     */
    protected StorageInterface $storage;

    /**
     * @var array
     */
    protected array $config = [
        'storage_class' => File::class,
        'storage_directory' => './var/cache',
        'storage_servers' => [['127.0.0.1', 11211]],
    ];

    /**
     * check that cache directory exist and create it if not
     *
     * @param array $config
     * @throws CacheException
     */
    public function __construct(array $config = [])
    {
        $this->config = \array_merge($this->config, $config);

        $this->registerStorage();
    }

    /**
     * @return Common|Cache|SimpleCache
     * @throws CacheException
     */
    protected function registerStorage(): self
    {
            switch (true) {
                case $this->config['storage_class'] instanceof StorageInterface:
                    $this->storage = $this->config['storage_class'];
                    break;
    
                case $this->config['storage_class'] === File::class:
                case $this->config['storage_class'] === Memcached::class:
                case \is_string($this->config['storage_class']):
                    $this->factoryStorage();
                    break;
    
                default:
                    throw new CacheException('Incorrect storage type: ' . $this->config['storage_class']);
            }

        return $this;
    }

    /**
     * @return Common|Cache|SimpleCache
     */
    protected function factoryStorage(): self
    {
        $config = [
            'cache_path' => $this->config['storage_directory'],
            'storage_servers' => $this->config['storage_servers'],
        ];
        $this->storage = new $this->config['storage_class']($config);

        return $this;
    }
}
