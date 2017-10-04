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
        $cache = new Cache([
            'storage_directory' => $this->cachePath
        ]);
        $item = (new CacheItem('test'))->set('test data');

        $this->assertFalse($cache->hasItem('test'));

        $cache->save($item);

        $this->assertTrue($cache->hasItem('test'));
    }

    public function testGetItemFromCache()
    {
        $cache = new Cache([
            'storage_directory' => $this->cachePath
        ]);
        $item = (new CacheItem('test'))->set('test data');

        $this->assertFalse($cache->hasItem('test'));

        $cache->save($item);

        $this->assertTrue($cache->hasItem('test'));

        $item = $cache->getItem('test');

        $this->assertInstanceOf(CacheItem::class, $item);
        $this->assertEquals('test data', $item->get());

        $items = $cache->getItems(['test']);

        $this->assertInstanceOf(CacheItem::class, $items['test']);
        $this->assertEquals('test data', $items['test']->get());
    }

    public function testClearItems()
    {
        $cache = new Cache([
            'storage_directory' => $this->cachePath
        ]);
        $item = (new CacheItem('test'))->set('test data');

        $this->assertFalse($cache->hasItem('test'));

        $cache->save($item);

        $this->assertTrue($cache->hasItem('test'));

        $cache->clear();

        $this->assertFalse($cache->hasItem('test'));
    }

    public function testDeleteItems()
    {
        
    }

    public function testSaveDeferred()
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
