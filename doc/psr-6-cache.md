# PSR 6 cache usage

Allow to use cache with [PSR-6](http://www.php-fig.org/psr/psr-6/) standard.

## Create Cache Item

All cache data need to be stored as `CacheItem` object. So first we need to
create such object with data to be cached.

```php
$item = new CacheItem('cache_key');
```

`CacheItem` constructor require one parameter, that is string name of cached data key
and second optionally parameter, array of item configuration. For now its
have only one key wit cache data expiration. That value can be also set up
with some special methods.

```php
$item = new CacheItem('cache_key', ['expire' => 1234]);
```

Default expiration time is `86400` seconds.

### Public methods

* **__construct** - Require first parameter as string with cache key name, and second optional with expiration time (['expire' => 1234])
* **expiresAfter** - Allow to set expiration after some given time. Accept `DateInterval` ,`int` or `null`. If its `null` then will use expiration from configuration, `int` value will be added into current time.
* **expiresAt** - Allow to set cache item expiration on specified date. Accept `DateTimeInterface` or `null`. If is `null` then will use expiration from configuration.
* **get** - Get stored in cache item data, or `null` if there is no data, or cache item was expired
* **getKey** - Return cache item cache key value
* **isHit** - Return boolean information that cache item is still exists
* **set** - Store in cache item given as parameter mixed data

**const ALLOWED_KEY_CHARS** - regular expression to check that key is correct

## Create Cache instance

To store Cache Items we need to create `Cache` instance.

### Configuration

'storage_class' => File::class,
'storage_directory' => './var/cache',

### Public methods

## Exceptions

