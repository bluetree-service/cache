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
* **getKey** - Return cache item key value
* **isHit** - Return boolean information that cache item is still exists
* **set** - Store in cache item given as parameter mixed data

**const ALLOWED_KEY_CHARS** - regular expression to check that key is correct

## Create Cache instance

To store Cache Items we need to create `Cache` instance. To create instance with
default config just use:

```php
$cache = new Cache;
```

Cache object constructor can accept one variable that is configuration array.
That configuration can set default storage path and some different storage object.

### Configuration

* **storage_class** - Value can be string with full namespace of class or some existing object. All of them must be instances of `StorageInterface`. By default its `File::class`
* **storage_directory** - Optional (for file system storage) path to storage directory. By default its: `./var/cache`

```php
$cache = new Cache([
    'storage_class' => '\Some\Class'
    'storage_directory' => 'some/path'
]);
```

### Public methods

* **getItem** - Return `CacheItem` or `null`. Require string with cache item key name
* **getItems** - Return array of`CacheItem`s or empty array. Require array with cache item key names. If one of items don't exists, will return `null` for its key name
* **hasItem** - Return boolean information that Cache Item exists. Require string with cache item key name
* **clear** - Remove all Cache items, return `true` if item was removed
* **deleteItem** - Remove Cache Item by given key name, return `true` if item was removed
* **deleteItems** - Allow to remove list of Cache Items. Accept array of key names, return `true` if items was removed
* **save** - Save Cache Item into selected storage engine. Accept instance of `CacheItemInterface`, return `true` if item was saved
* **saveDeferred** - Add Cache Item into que, and save in storage engine only if `Cache` object was destructed or manually method `commit` was executed.
* **commit** - Save all stored in `Cache` Cache Items added by `saveDeferred` in storage engine, return `true` if items was saved

## Exceptions

All throwed exceptions are type of `CacheExceptionInterface`.

Possible exception can be caused by:

* Invalid key name for Cache Item. Should us only chars, numbers and _.
* Invalid expire type for Cache Item
* Incorrect storage type, must be instance of `StorageInterface`
* Error on saving cache items after `commit` execution
* Some of cache items was not saved during object destruction or when `commit` method was executed
