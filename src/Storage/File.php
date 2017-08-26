<?php

namespace BlueCache\Storage;

class File implements StorageInterface
{
    /**
     * @var array
     */
    protected $params = [];

    /**
     * @param array $params
     */
    public function __construct(array $params)
    {
        $this->params = $params;
    }

    /**
     * @param string $name
     * @param mixed $data
     * @param string|int $expire
     * @return $this
     */
    public function store($name, $data, $expire = '')
    {
        //convert expire to timestamp

        $cacheFile = $this->params['cache_path'] . DIRECTORY_SEPARATOR . $name . '_' . $expire . '.cache';

        if (!file_exists($this->params['cache_path'])) {
            $bool = mkdir($this->params['cache_path']);

            if (!$bool) {
                throw new CacheException('Unable to create cache directory: ' . $this->params['cache_path']);
            }
        }

        $bool = file_put_contents($cacheFile, serialize($data));

        if (!$bool) {
            throw new CacheException('Unable to save log file: ' . $cacheFile);
        }

        return $this;
    }

    public function restore($name, $type = 'string')
    {
        
    }

    public function clear($name = false)
    {
        
    }

    protected function exists($name)
    {
        //find with regexp
        return ;
    }

    protected function expire($path)
    {
        
    }
}
