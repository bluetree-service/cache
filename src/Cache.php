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

class Cache
{
    protected $config = [
        'cache_base_time' => 86400,
        'cache_config_time' => 1,
        'storage' => 'file',
        'storage_directory' => 'file',
    ];

    /**
     * check that cache directory exist and create it if not
     */
    public function __construct(array $config)
    {
        $this->config = array_merge($this->config, $config);
        
        try{
            if (!file_exists(CORE_CACHE)) {
                mkdir(CORE_CACHE);
                chmod(CORE_CACHE, 0777);
            }
        } catch (Exception $e) {
            Loader::exceptions($e);
        }
    }

    /**
     * get cached configuration if it exists
     * 
     * @param string $cacheCode
     * @return bool|mixed
     */
    public function getCache($cacheCode)
    {
        $file = CORE_CACHE . $cacheCode . '.cache';
        if (!file_exists($file)) {
            return false;
        }

        if ($this->checkCachedTimes($file)) {
            return file_get_contents($file);
        }

        return false;
    }

    /**
     * add data to cache file, or create it
     * 
     * @param string $cacheCode
     * @param mixed $data
     * @return bool
     */
    public function setCache($cacheCode, $data)
    {
        $file = CORE_CACHE . $cacheCode . '.cache';
        return (bool)file_put_contents($file, $data);
    }

    /**
     * check cached file time
     * 
     * @param string $file
     * @return bool
     */
    protected function checkCachedTimes($file)
    {
        $coreConfig = Loader::getConfiguration();
        $currentTime = time();
        $fileTime = filemtime($file);

        if ($coreConfig) {
            $validTime = $coreConfig->getCore()->getCacheTime();
        } else {
            $validTime = self::CACHE_CONFIG_TIME;
        }

        $expireTime = ($validTime * self::CACHE_BASE_TIME) + $fileTime;

        if ($expireTime > $currentTime) {
            return true;
        }

        return false;
    }
}
