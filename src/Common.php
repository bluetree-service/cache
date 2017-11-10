<?php
/**
 * Created by PhpStorm.
 * User: michal
 * Date: 10/10/2017
 * Time: 14:27
 */

namespace BlueCache;

use \BlueCache\Storage\File;
use \BlueCache\Storage\StorageInterface;

trait Common
{
    /**
     * @var StorageInterface
     */
    protected $storage;

    /**
     * @var array
     */
    protected $config = [
        'storage_class' => File::class,
        'storage_directory' => './var/cache',
    ];

    /**
     * check that cache directory exist and create it if not
     *
     * @param array $config
     * @throws \BlueCache\CacheException
     */
    public function __construct(array $config = [])
    {
        $this->config = array_merge($this->config, $config);

        $this->registerStorage();
    }

    /**
     * @return $this
     * @throws \BlueCache\CacheException
     */
    protected function registerStorage()
    {
        if (!$this->storage) {
            switch (true) {
                case $this->config['storage_class'] instanceof StorageInterface:
                    $this->storage = $this->config['storage_class'];
                    break;

                case $this->config['storage_class'] === File::class:
                    $config = ['cache_path' => $this->config['storage_directory']];
                    $this->storage = new $this->config['storage_class']($config);
                    break;

                case is_string($this->config['storage_class']):
                    $config = ['cache_path' => $this->config['storage_directory']];
                    $this->storage = new $this->config['storage_class']($config);

                    if (!($this->storage instanceof StorageInterface)) {
                        throw new CacheException('Incorrect storage type: ' . $this->config['storage_class']);
                    }
                    break;

                default:
                    throw new CacheException('Incorrect storage type: ' . get_class($this->storage));
                    break;
            }
        }

        return $this;
    }
}
