<?php

namespace BlueCache\Test;

use BlueCache\Storage\File;
use BlueCache\CacheItem;
use PHPUnit\Framework\TestCase;

class FileTest extends TestCase
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

    public function testCreateCacheFile()
    {
        $this->assertFileNotExists($this->fullTestFilePath);

        (new File($this->fileConfig))->store(new CacheItem($this->testCache));

        $this->assertFileExists($this->fullTestFilePath);

        $content = file_get_contents($this->fullTestFilePath);

        $this->assertInstanceOf(CacheItem::class, unserialize($content));
    }

    /**
     * @expectedException \BlueCache\CacheException
     * @expectedExceptionMessage Unable to create cache directory:
     */
    public function testCreateCacheFileForIncorrectDir()
    {
        $conf = [
            'cache_path' => $this->cachePathNoAccess . '/dir'
        ];

        (new File($conf))->store(new CacheItem($this->testCache));
    }

    public function testAddMessageForExistingLog()
    {
        $storage = new File($this->fileConfig);
        $item = new CacheItem($this->testCache);

        $this->assertFileNotExists($this->fullTestFilePath);

        $storage->store($item->set($this->testMessage[0]));

        $this->assertFileExists($this->fullTestFilePath);

        $content = file_get_contents($this->fullTestFilePath);
        $this->assertInstanceOf(CacheItem::class, unserialize($content));
        $this->assertEquals($this->testMessage[0], unserialize($content)->get());

        $storage->store($item->set($this->testMessage[1]));

        $this->assertFileExists($this->fullTestFilePath);

        $content = file_get_contents($this->fullTestFilePath);
        $this->assertInstanceOf(CacheItem::class, unserialize($content));
        $this->assertEquals($this->testMessage[1], unserialize($content)->get());
    }

    /**
     * @expectedException \BlueCache\CacheException
     */
    public function testWriteForIncorrectCacheFile()
    {
        $file = new File($this->fileConfig);
        $file->store(new CacheItem($this->testCache));

        chmod($this->fullTestFilePath, 0555);

        $file->store(new CacheItem($this->testCache));
    }

    public function testCacheExists()
    {
        $storage = new File($this->fileConfig);
        $item = (new CacheItem($this->testCache))->set('data');

        $this->assertFalse($storage->exists($this->testCache));

        $storage->store($item);

        $this->assertTrue($storage->exists($this->testCache));
    }

    public function testRestoreCache()
    {
        $this->assertFileNotExists($this->fullTestFilePath);
        $item = new CacheItem($this->testCache);

        (new File($this->fileConfig))->store($item->set($this->testMessage[0]));

        $this->assertFileExists($this->fullTestFilePath);

        $content = (new File($this->fileConfig))->restore($this->testCache);

        $this->assertInstanceOf(CacheItem::class, $content);
        $this->assertEquals($this->testMessage[0], $content->get());
    }

    public function testRestoreCacheAfterExpiration()
    {
        $this->assertFileNotExists($this->fullTestFilePath);
        $item = new CacheItem($this->testCache);
        $item->expiresAfter(1);

        $storage = new File($this->fileConfig);
        $storage->store($item);

        $this->assertFileExists($this->fullTestFilePath);

        sleep(1);

        $content = $storage->exists($this->testCache);

        $this->assertFalse($content);
    }

    public function testRestoreManyCache()
    {
        $contents = $this->createCacheFiles();

        $this->assertInstanceOf(CacheItem::class, $contents['test_cache']);
        $this->assertEquals($this->testMessage[0], $contents['test_cache']->get());

        $this->assertInstanceOf(CacheItem::class, $contents['test_cache_2']);
        $this->assertEquals($this->testMessage[1], $contents['test_cache_2']->get());
    }

    public function testRestoreNotExisting()
    {
        $content = (new File($this->fileConfig))->restore($this->testCache);

        $this->assertNull($content);
    }

    protected function createCacheFiles()
    {
        $this->assertFileNotExists($this->fullTestFilePath);
        $this->assertFileNotExists($this->fullTestFilePath2);

        $storage = new File($this->fileConfig);
        $item1 = new CacheItem($this->testCache);
        $item2 = new CacheItem($this->testCache2);

        $storage->store($item1->set($this->testMessage[0]));
        $storage->store($item2->set($this->testMessage[1]));

        $this->assertFileExists($this->fullTestFilePath);
        $this->assertFileExists($this->fullTestFilePath2);

        return (new File($this->fileConfig))->restore([
            $this->testCache,
            $this->testCache2,
        ]);
    }

    public function testClearCache()
    {
        $this->createCacheFiles();
        $storage = new File($this->fileConfig);

        $storage->clear($this->testCache);
        $storage->clear($this->testCache2);

        $this->createCacheFiles();
        $storage->clear();

        $this->assertFileNotExists($this->fullTestFilePath);
        $this->assertFileNotExists($this->fullTestFilePath2);
    }

    /**
     * @expectedException \BlueCache\CacheException
     * @expectedExceptionMessage Invalid type: 2134
     */
    public function testClearCacheInvalidType()
    {
        $this->createCacheFiles();
        $storage = new File($this->fileConfig);

        $storage->clear(2134);
    }

    public function testClearMany()
    {
        $this->createCacheFiles();
        $storage = new File($this->fileConfig);

        $isDeleted = $storage->clearMany([$this->testCache, $this->testCache2]);

        $this->assertTrue($isDeleted);
        $this->assertFileNotExists($this->fullTestFilePath);
        $this->assertFileNotExists($this->fullTestFilePath2);
    }

    public function testClearManyWithError()
    {
        $this->createCacheFiles();
        $storage = new File($this->fileConfig);

        $isDeleted = $storage->clearMany(['non_existing_key', $this->testCache]);

        $this->assertFalse($isDeleted);
        $this->assertFileNotExists($this->fullTestFilePath);
    }

    /**
     * actions launched before test starts
     */
    protected function setUp()
    {
        $this->cachePath = dirname(__DIR__) . '/var/cache';
        $this->cachePathNoAccess = $this->cachePath . 'NoAccess';
        $this->fileConfig = ['cache_path' => $this->cachePath];
        $this->fullTestFilePath = $this->cachePath . '/' . $this->testCache . '.cache';
        $this->fullTestFilePath2 = $this->cachePath . '/' . $this->testCache2 . '.cache';

        if (!is_dir($this->cachePathNoAccess)) {
            mkdir($this->cachePathNoAccess);
        }
        chmod($this->cachePathNoAccess, 0555);

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

        if (file_exists($this->fullTestFilePath2)) {
            unlink($this->fullTestFilePath2);
        }
    }
}
