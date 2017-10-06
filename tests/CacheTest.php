<?php

namespace BlueCache\Test;

use BlueCache\Cache;
use BlueCache\CacheItem;
use PHPUnit\Framework\TestCase;

class CacheTest extends TestCase
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

    public function testCreateCache()
    {
        $items = (new Cache)->getItems();

        $this->assertEmpty($items);
    }

    public function testCreateCacheWithConfig()
    {
        $items = (new Cache([
            [
                'storage_class' => \BlueCache\Storage\File::class,
                'storage_directory' => './var/cache',
            ]
        ]))->getItems();

        $this->assertEmpty($items);
    }

    public function testCreateCacheWithDefinedStorage()
    {
        $storage = new \BlueCache\Storage\File;

        $items = (new Cache([
            'storage_class' => $storage,
        ]))->getItems();

        $this->assertEmpty($items);
    }

    /**
     * @expectedException \BlueCache\CacheException
     * @expectedExceptionMessage Incorrect storage type: BlueCache\Cache
     */
    public function testCreateCacheWithIncorrectStorage()
    {
        new Cache(['storage_class' => Cache::class]);
    }

    /**
     * @expectedException \BlueCache\CacheException
     */
    public function testCreateCacheWithIncorrectStorageType()
    {
        new Cache(['storage_class' => 123123]);
    }

    public function testAddItemToCache()
    {
        $this->createCacheItem();
    }

    public function testGetItemFromCache()
    {
        $cache = $this->createCacheItem();

        $item = $cache->getItem('test');

        $this->assertInstanceOf(CacheItem::class, $item);
        $this->assertEquals('test data', $item->get());

        /** @var CacheItem $item */
        $item = $cache->getItems(['test'])['test'];

        $this->assertInstanceOf(CacheItem::class, $item);
        $this->assertEquals('test data', $item->get());
    }

    public function testClearItems()
    {
        $cache = $this->createCacheItem();

        $cache->clear();

        $this->assertFalse($cache->hasItem('test'));
    }

    public function testDeleteItems()
    {
        $cache = $this->createCacheItem();
        
        $cache->deleteItems(['test']);

        $this->assertFalse($cache->hasItem('test'));

        $cache = $this->createCacheItem();
        
        $cache->deleteItem('test');

        $this->assertFalse($cache->hasItem('test'));
    }

    public function testSaveDeferred()
    {
        $cache = new Cache([
            'storage_directory' => $this->cachePath
        ]);
        $item = (new CacheItem('test'))->set('test data');

        $this->assertFalse($cache->hasItem('test'));

        $cache->saveDeferred($item);

        $this->assertFalse($cache->hasItem('test'));

        $cache->commit();

        $this->assertTrue($cache->hasItem('test'));
    }

    public function testSaveDeferredWithDestruct()
    {
        $cache = new Cache([
            'storage_directory' => $this->cachePath
        ]);
        $item = (new CacheItem('test'))->set('test data');

        $this->assertFalse($cache->hasItem('test'));

        $cache->saveDeferred($item);

        $this->assertFalse($cache->hasItem('test'));

        unset($cache);

        $cache = new Cache([
            'storage_directory' => $this->cachePath
        ]);

        $this->assertTrue($cache->hasItem('test'));
    }

    protected function createCacheItem()
    {
        $cache = new Cache([
            'storage_directory' => $this->cachePath
        ]);
        $item = (new CacheItem('test'))->set('test data');

        $this->assertFalse($cache->hasItem('test'));

        $cache->save($item);

        $this->assertTrue($cache->hasItem('test'));

        return $cache;
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
