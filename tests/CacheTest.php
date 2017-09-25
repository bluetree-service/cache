<?php

namespace BlueCache\Test;

use BlueCache\Cache;
use PHPUnit\Framework\TestCase;

class CacheTest extends TestCase
{
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

    public function testCreateCacheWithIncorrectStorage()
    {
        $items = (new Cache([
            [
                'storage_class' => '\noExists',
            ]
        ]))->getItems();

        $this->assertEmpty($items);
    }
}
