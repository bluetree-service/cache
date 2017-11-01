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
    protected $fullTestFilePath1;
    protected $fullTestFilePath2;

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

    protected function createMultipleSimpleCacheItem()
    {
        $cache = new SimpleCache([
            'storage_directory' => $this->cachePath
        ]);

        $data1 = 'test data';
        $data2 = 'test data 2';

        $this->assertFalse($cache->has('test1'));
        $this->assertFalse($cache->has('test2'));

        $cache->setMultiple([
            'test1' => $data1,
            'test2' => $data2,
        ]);

        $this->assertTrue($cache->has('test'));
        $this->assertTrue($cache->has('test2'));

        return $cache;
    }

    public function testAddMultipleDataToSimpleCache()
    {
        $this->createMultipleSimpleCacheItem();
    }

    public function testGetDataFromCache()
    {
        $cache = $this->createSimpleCacheItem();

        $this->assertEquals('test data', $cache->get('test'));

        $this->assertEquals('test data', $cache->getMultiple(['test'])['test']);
    }

    public function testGetMultipleDataFromCache()
    {
        $cache = $this->createMultipleSimpleCacheItem();
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
        $this->fullTestFilePath1 = $this->cachePath . '/test1.cache';
        $this->fullTestFilePath2 = $this->cachePath . '/test2.cache';

        $this->tearDown();
    }

    /**
     * actions launched after test was finished
     */
    protected function tearDown()
    {
        $this->unlinkFile($this->fullTestFilePath)
            ->unlinkFile($this->fullTestFilePath1)
            ->unlinkFile($this->fullTestFilePath2);
    }

    /**
     * @param string $path
     * @return $this
     */
    protected function unlinkFile($path)
    {
        if (file_exists($path)) {
            unlink($path);
        }

        return $this;
    }
}
