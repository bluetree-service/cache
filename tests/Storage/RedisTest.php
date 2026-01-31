<?php

namespace BlueCache\Test;

use BlueCache\Storage\Redis;
use BlueCache\CacheItem;
use PHPUnit\Framework\TestCase;

class RedisTest extends TestCase
{
    /**
     * store generated cache file path
     *
     * @var string
     */
    protected $cachePath;

    /**
     * store generated cache file path
     *
     * @var string
     */
    protected $cachePathNoAccess;

    /**
     * @var array
     */
    protected $fileConfig = [];

    /**
     * @var string
     */
    protected $testCache = 'test_cache';
    protected $testCache2 = 'test_cache_2';

    /**
     * @var string
     */
    protected $fullTestFilePath;
    protected $fullTestFilePath2;

    /**
     * @var array
     */
    protected $testMessage = [
        'Some log message',
        'Some another log message',
    ];

    public function testCreateCacheRedis()
    {
        $item = new CacheItem($this->testCache);
        $cache = new Redis($this->fileConfig);
        $cache->store($item->set($this->testMessage[0]));

        $data = $cache->restore($this->testCache);

        $this->assertInstanceOf(CacheItem::class, $data);
        $this->assertEquals($this->testMessage[0], $data->get());
    }

    public function testCreateCacheWithoutConnection()
    {
        $warning = null;
        set_error_handler(function($errno, $errstr) use (&$warning) {
            $warning = $errstr;
            return true;
        });
        
        try {
            $this->expectExceptionMessage(
                "Redis exception: php_network_getaddresses: getaddrinfo for redis_none_existing failed: Name does not resolve"
            );
            $this->expectException(\BlueCache\CacheException::class);
            $conf = [
                'storage_servers' => ['redis_none_existing', 11]
            ];

            (new Redis($conf))->store(new CacheItem($this->testCache));
        } finally {
            restore_error_handler();
        }

        $this->assertStringContainsString(
            "Redis::connect(): php_network_getaddresses: getaddrinfo for redis_none_existing failed: Name does not resolve",
            $warning
        );
    }

    public function testAddMessageForExistingLog()
    {
        $storage = new Redis($this->fileConfig);
        $item = new CacheItem($this->testCache);

        $storage->store($item->set($this->testMessage[0]));

        $data = $storage->restore($this->testCache);
        $this->assertInstanceOf(CacheItem::class, $data);
        $this->assertEquals($this->testMessage[0], $data->get());

        $storage->store($item->set($this->testMessage[1]));

        $data = $storage->restore($this->testCache);
        $this->assertInstanceOf(CacheItem::class, $data);
        $this->assertEquals($this->testMessage[1], $data->get());
    }

    public function testWriteForIncorrectCacheKey()
    {
        $this->expectException(\BlueCache\CacheException::class);
        $this->expectExceptionMessage("Invalid key. Should us only chars, numbers and _. Use: bad key");

        $key = "bad key\n";
        $storage = new Redis($this->fileConfig);
        $storage->store(new CacheItem($key));
    }

    public function testCacheExists()
    {
        $storage = new Redis($this->fileConfig);
        $item = (new CacheItem($this->testCache))->set('data');

        $this->assertFalse($storage->exists($this->testCache));

        $storage->store($item);

        $this->assertTrue($storage->exists($this->testCache));
    }

    public function testRestoreCache()
    {
        $item = new CacheItem($this->testCache);
        $storage = new Redis($this->fileConfig);

        $data = $storage->restore($this->testCache);
        $this->assertNull($data);

        $storage->store($item->set($this->testMessage[0]));

        $data = $storage->restore($this->testCache);
        $this->assertInstanceOf(CacheItem::class, $data);
        $this->assertEquals($this->testMessage[0], $data->get());

        $content = (new Redis($this->fileConfig))->restore($this->testCache);

        $this->assertInstanceOf(CacheItem::class, $content);
        $this->assertEquals($this->testMessage[0], $content->get());
    }

    public function testRestoreCacheAfterExpiration()
    {
        $item = new CacheItem($this->testCache);
        $item->expiresAfter(1);
        $item->set($this->testMessage[0]);

        $storage = new Redis($this->fileConfig);

        $data = $storage->restore($this->testCache);
        $this->assertNull($data);
        
        $storage->store($item);

        $this->assertTrue($storage->exists($this->testCache));
        $item = $storage->restore($this->testCache);
        $this->assertEquals($this->testMessage[0], $item->get());

        sleep(2);

        $item = $storage->restore($this->testCache);

        $this->assertNull($item);
        $this->assertFalse($storage->exists($this->testCache));
    }

    public function testRestoreManyCache()
    {
        $contents = $this->createCache();

        $this->assertInstanceOf(CacheItem::class, $contents['test_cache']);
        $this->assertEquals($this->testMessage[0], $contents['test_cache']->get());

        $this->assertInstanceOf(CacheItem::class, $contents['test_cache_2']);
        $this->assertEquals($this->testMessage[1], $contents['test_cache_2']->get());
    }

    public function testRestoreNotExisting()
    {
        $content = (new Redis($this->fileConfig))->restore($this->testCache);

        $this->assertNull($content);
    }

    public function testRestoreEmptyItem()
    {
        $cache = new Redis($this->fileConfig);
        $cache->store(new CacheItem($this->testCache));
        $content = $cache->restore($this->testCache);

        $this->assertNull($content);
    }

    protected function createCache()
    {
        $storage = new Redis($this->fileConfig);
        $item1 = new CacheItem($this->testCache);
        $item2 = new CacheItem($this->testCache2);

        $storage->store($item1->set($this->testMessage[0]));
        $storage->store($item2->set($this->testMessage[1]));

        $this->assertTrue($storage->exists($this->testCache));
        $this->assertTrue($storage->exists($this->testCache2));

        return (new Redis($this->fileConfig))->restore([
            $this->testCache,
            $this->testCache2,
        ]);
    }

    public function testClearCache()
    {
        $this->createCache();
        $storage = new Redis($this->fileConfig);

        $storage->clear($this->testCache);
        $storage->clear($this->testCache2);

        $this->createCache();
        $storage->clear();

        $this->assertFalse($storage->exists($this->testCache));
        $this->assertFalse($storage->exists($this->testCache2));
    }

    public function testClearMany()
    {
        $this->createCache();
        $storage = new Redis($this->fileConfig);

        $isDeleted = $storage->clearMany([$this->testCache, $this->testCache2]);

        $this->assertTrue($isDeleted);
        $this->assertFalse($storage->exists($this->testCache));
        $this->assertFalse($storage->exists($this->testCache2));
    }

    public function testClearManyWithError()
    {
        $this->createCache();
        $storage = new Redis($this->fileConfig);

        $isDeleted = $storage->clearMany(['non_existing_key', $this->testCache]);

        $this->assertFalse($isDeleted);
        $this->assertFalse($storage->exists($this->testCache));
    }

    /**
     * actions launched before test starts
     */
    protected function setUp(): void
    {
        $this->fileConfig = ['storage_servers' => [getenv('REDIS_SERVERS') ?: 'redis', 6379, 0]];

        $this->tearDown();
    }

    /**
     * actions launched after test was finished
     */
    protected function tearDown(): void
    {
        $storage = new Redis($this->fileConfig);
        $storage->clear();
    }
}
