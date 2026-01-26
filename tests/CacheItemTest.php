<?php

namespace BlueCache\Test;

use BlueCache\CacheItem;
use PHPUnit\Framework\TestCase;

class CacheItemTest extends TestCase
{
    public function testCreateCacheItem()
    {
        $item = new CacheItem('test');

        $this->assertEquals('test', $item->getKey());
        $this->assertFalse($item->isHit());

        $item->set('test data');

        $this->assertEquals('test data', $item->get());
    }

    public function testCreateCacheItemWithException()
    {
        $this->expectExceptionMessage("Invalid key. Should us only chars, numbers and _. Use: test+1234");
        $this->expectException(\BlueCache\CacheException::class);
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

    public function testCacheItemExpireWithError()
    {
        $this->expectException(\TypeError::class);
        $this->expectExceptionMessageMatches(
            '/Argument #1 .* must be of type .*DateInterval\|DateTimeInterface\|int\|null, string given/'
        );
        $item = (new CacheItem('test'))->set('test data');

        $this->assertTrue($item->isHit());
        $item->expiresAfter('foo');
    }

    public function testGetExpirationDate(): void
    {
        $item = new CacheItem('test');

        $this->assertNull($item->getExpirationTimeFormatted());
        $this->assertNull($item->getExpirationTime());

        $time = time();
        $item->expiresAfter(10);
        $this->assertTrue($item->getExpirationTime() - ($time + 10) <= 1);

        $date = \date('Y-m-d', $time);
        $this->assertMatchesRegularExpression(
            '/^' . $date . 'T[\d]{2}:[\d]{2}:[\d]{2}\+[\d]{2}:[\d]{2}$/',
            $item->getExpirationTimeFormatted()
        );
    }

    public function testReplaceStorageTime(): void
    {
        $item = new CacheItem('test');

        $time = time();
        $item->expiresAfter(10);

        $item->set('test data');

        $this->assertTrue($item->getExpirationTime() - ($time + 10) <= 1);

        sleep(3);

        $time = time();
        $item->expiresAfter(10);

        $item->set('new test data');

        $this->assertTrue($item->getExpirationTime() - ($time + 10) <= 1);
    }
}
