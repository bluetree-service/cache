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

        $this->assertTrue($cache->has('test1'));
        $this->assertTrue($cache->has('test2'));

        return $cache;
    }

    public function testCreateMultipleSimpleCacheItemWithError()
    {
        $cache = new SimpleCache([
            'storage_directory' => $this->cachePath
        ]);

        $data1 = 'test data';
        $data2 = 'test data 2';

        $this->assertFalse($cache->has('test1'));
        $this->assertFalse($cache->has('test2'));

        chmod($this->cachePath, 0555);

        $bool = $cache->setMultiple([
            'test1' => $data1,
            'test2' => $data2,
        ]);

        chmod($this->cachePath, 0777);

        $this->assertFalse($bool);
        $this->assertArrayHasKey('test1', $cache->getMultipleSetExceptions());
        $this->assertArrayHasKey('test2', $cache->getMultipleSetExceptions());

        $this->assertRegExp(
            '#Unable to save log file: .*tests\/var\/cache\/test1\.cache#',
            $cache->getMultipleSetExceptions()['test1']
        );
        $this->assertRegExp(
            '#Unable to save log file: .*tests\/var\/cache\/test2\.cache#',
            $cache->getMultipleSetExceptions()['test2']
        );
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

    public function testGetDefaultDataFromCache()
    {
        $cache = $this->createSimpleCacheItem();

        $this->assertFalse($cache->get('test_default', false));

        $this->assertEquals(['test_default' => false], $cache->getMultiple(['test_default'], false));
    }

    public function testGetMultipleDataFromCache()
    {
        $cache = $this->createMultipleSimpleCacheItem();

        $cachedData = $cache->getMultiple(['test1', 'test2']);

        $this->assertEquals('test data', $cachedData['test1']);
        $this->assertEquals('test data 2', $cachedData['test2']);
    }

    public function testClearCacheData()
    {
        $cache = $this->createMultipleSimpleCacheItem();

        $cache->clear();

        $this->assertFalse($cache->has('test1'));
        $this->assertFalse($cache->has('test2'));
    }

    public function testDeleteItems()
    {
        $cache = $this->createMultipleSimpleCacheItem();

        $cache->delete('test1');

        $this->assertFalse($cache->has('test1'));
        $this->assertTrue($cache->has('test2'));
    }

    public function testDeleteMultipleItems()
    {
        $cache = $this->createMultipleSimpleCacheItem();

        $cache->deleteMultiple(['test1']);

        $this->assertFalse($cache->has('test1'));
        $this->assertTrue($cache->has('test2'));
    }

    public function testSetDataWithExpirationTime()
    {
        $cache = new SimpleCache([
            'storage_directory' => $this->cachePath
        ]);

        $data = 'test data';

        $this->assertFalse($cache->has('test'));

        $cache->set('test', $data, 4);
        sleep(2);

        $this->assertTrue($cache->has('test'));

        sleep(2);

        $this->assertFalse($cache->has('test'));
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
