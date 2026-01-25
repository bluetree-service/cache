# Memcached Storage

Allow to store cache element in Memcached server. Remember about key names allowed by Memcached.  
Memcached keys can be up to 250 characters long and must not contain control characters (such as spaces, newlines, tabs)
or the null byte. Only printable ASCII characters are recommended (letters, digits, underscores, hyphens, dots).
Avoid special characters like spaces, slashes, colons, or equals signs.

## Basic Usage

Just create storage object and optionally set storage directory as constructor parameter.

```php
$storage = new Memcached (['storage_servers' => [['host', 'port']]]);
```

## Exceptions

In some cases `Memcached` object can throw some exceptions. All of them are type of `BlueCache\CacheException`.  


## List of public methods

* **clear** - Clear one cache elements. Parameters is *string* key name to remove single or `null` to remove all.
* **clearMany** - Allow to remove list of given cache elements.
* **exists** - Return boolean information that cache element exists, as parameter takes key name of cache element.
* **restore** - Restore single or many cache elements. As parameter takes key name or *array* of keys.
* **store** - Save in cache server single cache element. As parameter takes `CacheItemInterface` instance.
