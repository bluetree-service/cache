# File Storage

Allow to store cache element as single files on local file system. Remember
about key names allowed by filesystem and possible conflicts with file names.

## Basic Usage

Just create storage object and optionally set storage directory as constructor parameter.

```php
$storage = new File (['cache_path' => 'some/path']);
```

By default all files are stored in `./var/cache` directory.

## Exceptions

In some cases `File` object can throw some exceptions. all of them are type
of `BlueCache\CacheException`.  
Possible exception can be caused by:

* Unable to create cache directory
* Unable to create log file
* Invalid type of data given into `clear` method


## List of public methods

* **__construct** - Create storage object, get array of parameters. For now only one is supported `cache_path` that point into cache storage directory.
* **clear** - Clear one cache elements. Parameters is *string* key name to remove single or `null` to remove all.
* **clearMany** - Allow to remove list of given cache elements.
* **exists** - Return boolean information that cache element exists, as parameter takes key name of cache element.
* **restore** - Restore single or many cache elements. As parameter takes key name or *array* of keys.
* **store** - Save in cache directory single cache element. As parameter takes `CacheItemInterface` instance.

**const CACHE_EXTENSION** - store extension for cache file (`.cache`)
