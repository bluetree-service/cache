# PSR 16 cache usage

Allow to use cache with [PSR-16](http://www.php-fig.org/psr/psr-16/) standard.

## Create Simple Cache instance

To store Cache Items we need to create `SimpleCache` instance. To create instance with
default config just use:

```php
$cache = new SimpleCache;
```

Cache object constructor can accept one variable that is configuration array.
That configuration can set default storage path and some different storage object.

### Configuration

* **storage_class** - Value can be string with full namespace of class or some existing object. All of them must be instances of `StorageInterface`. By default its `File::class`
* **storage_directory** - Optional (for file system storage) path to storage directory. By default its: `./var/cache`

```php
$cache = new SimpleCache([
    'storage_class' => '\Some\Class'
    'storage_directory' => 'some/path'
]);
```

### Public methods

* **get** - Return value of stored by given key data. As second optional parameter store value that will be returned if key was nod founded (by default its `null`).
* **set** - Set data in Cache by given key. Require key name as first parameter and data as second. Third parameter is expiration time (\DateTimeInterface|\DateInterval|null|int). Return `true` if everything was ok
* **delete** - Return boolean information that Cache Item exists. Require string with cache item key name. Return `true` if everything was ok
* **clear** - Remove all Cache items. Return `true` if everything was ok
* **getMultiple** - Return values for given as first parameter keys (array of keys). Second optional parameter (default value) works the same as default in `get` method
* **setMultiple** - Allow to set array of pair key => value. Second parameter is expiration time. Return `true` if everything was ok
* **deleteMultiple** - Remove all cache elements given in array keys. Return `true` if everything was ok
* **has** - Return boolean information that cache element exists
* **getMultipleSetExceptions** - Return list of exceptions that was occurred during `setMultiple`

## Exceptions

All throwed exceptions are type of `CacheExceptionInterface`.

Possible exception can be caused by:

* Incorrect storage type, must be instance of `StorageInterface`
* Invalid expire type for Cache Item
* Invalid key name for Cache Item. Should us only chars, numbers and _.

IMPORTANT

All data stored by Simple Cache is converted into `CacheItem` used by PSR-6. That means all data will
be serialized and unserialized.
