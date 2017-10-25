<?php

namespace BlueCache\Test;

use BlueCache\SimpleCache;
use PHPUnit\Framework\TestCase;

class SimpleCacheTest extends TestCase
{
    /**
     * store generated cache file path
     *
     * @var string
     */
    protected $cachePath;

    /**
     * @var string
     */
    protected $fullTestFilePath;

    public function testSimpleCacheObject()
    {
        $this->assertInstanceOf(SimpleCache::class, new SimpleCache);
    }

    public function testCreateSimpleCacheWithConfig()
    {
        $cache = new SimpleCache(
            [
                'storage_class' => \BlueCache\Storage\File::class,
                'storage_directory' => './var/cache',
            ]
        );

        $this->assertInstanceOf(SimpleCache::class, $cache);
    }

    public function testCreateSimpleCacheWithDefinedStorage()
    {
        $storage = new \BlueCache\Storage\File;

        $cache = new SimpleCache([
            'storage_class' => $storage,
        ]);

        $this->assertInstanceOf(SimpleCache::class, $cache);
    }

    /**
     * @expectedException \BlueCache\CacheException
     * @expectedExceptionMessage Incorrect storage type: BlueCache\SimpleCache
     */
    public function testCreateSimpleCacheWithIncorrectStorage()
    {
        new SimpleCache(['storage_class' => SimpleCache::class]);
    }

    /**
     * @expectedException \BlueCache\CacheException
     */
    public function testCreateSimpleCacheWithIncorrectStorageType()
    {
        new SimpleCache(['storage_class' => 123123]);
    }

    public function testAddDataToSimpleCache()
    {
        $this->createSimpleCacheItem();
    }

    protected function createSimpleCacheItem()
    {
        $cache = new SimpleCache([
            'storage_directory' => $this->cachePath
        ]);

        $data = 'test data';

        $this->assertFalse($cache->has('test'));

        $cache->set('test', $data);

        $this->assertTrue($cache->has('test'));

        return $cache;
    }

    protected function createSimpleCacheMultipleItem()
    {
        
    }

    public function testGetDataFromCache()
    {
        $cache = $this->createSimpleCacheItem();

        $this->assertEquals('test data', $cache->get('test'));

        $this->assertEquals('test data', $cache->getMultiple(['test'])['test']);
    }

    public function testClearCacheData()
    {
        
    }

    public function testDeleteItems()
    {
        
    }

    /**
     * actions launched before test starts
     */
    protected function setUp()
    {
        $this->cachePath = dirname(__DIR__) . '/tests/var/cache';
        $this->fullTestFilePath = $this->cachePath . '/test.cache';

        $this->tearDown();
    }

    /**
     * actions launched after test was finished
     */
    protected function tearDown()
    {
        if (file_exists($this->fullTestFilePath)) {
            unlink($this->fullTestFilePath);
        }
    }
}
