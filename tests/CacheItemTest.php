<?php

namespace BlueCache\Test;

use BlueCache\CacheItem;
use PHPUnit\Framework\TestCase;

class CacheItemTest extends TestCase
{
    public function testCreateCacheItem()
    {
        $item = new CacheItem('test');

        $this->assertEquals($item->getKey(), 'test');
        $this->assertFalse($item->isHit());

        $item->set('test data');

        $this->assertEquals($item->get(), 'test data');
    }

    /**
     * @expectedException \BlueCache\CacheException
     * @expectedExceptionMessage Invalid key. Should us only chars, numbers and _. Use: test+1234
     */
    public function testCreateCacheItemWithException()
    {
        new CacheItem('test+1234');
    }

    public function testCacheItemDefaultExpire()
    {
        $item = (new CacheItem('test', ['expire' => 1]))->set('test data');

        $this->assertTrue($item->isHit());

        sleep(2);

        $this->assertTrue($item->isHit());

        $item->expiresAfter();

        $this->assertTrue($item->isHit());

        sleep(2);

        $this->assertFalse($item->isHit());
        $this->assertNull($item->get());
    }

    public function testCacheItemExpireAt()
    {
        $item = (new CacheItem('test'))->set('test data');

        $this->assertTrue($item->isHit());
        $item->expiresAt(4);

        sleep(2);

        $this->assertTrue($item->isHit());

        sleep(2);

        $this->assertFalse($item->isHit());

        $item->set('test data');
        $this->assertTrue($item->isHit());

        $item->expiresAt(new \DateTime('+2 seconds'));

        sleep(3);

        $this->assertFalse($item->isHit());
    }

    public function testCacheItemExpireAfter()
    {
        $item = (new CacheItem('test'))->set('test data');

        $this->assertTrue($item->isHit());
        $item->expiresAfter(4);

        sleep(2);

        $this->assertTrue($item->isHit());

        sleep(2);

        $this->assertFalse($item->isHit());

        $item->set('test data');
        $this->assertTrue($item->isHit());

        $item->expiresAfter(new \DateInterval('P0Y0DT0H0M2S'));

        sleep(3);

        $this->assertFalse($item->isHit());
    }

    /**
     * @expectedException \BlueCache\CacheException
     * @expectedExceptionMessage Invalid expire type.
     */
    public function testCacheItemExpireWithError()
    {
        $item = (new CacheItem('test'))->set('test data');

        $this->assertTrue($item->isHit());
        $item->expiresAfter('foo');
    }
}
