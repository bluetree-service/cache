Bluetree PSR-6 & PSR-16 cache
=============================

[![Latest Stable Version](https://poser.pugx.org/bluetree-service/cache/v/stable.svg?style=flat-square)](https://packagist.org/packages/bluetree-service/cache)
[![Total Downloads](https://poser.pugx.org/bluetree-service/cache/downloads.svg?style=flat-square)](https://packagist.org/packages/bluetree-service/cache)
[![License](https://poser.pugx.org/bluetree-service/cache/license.svg?style=flat-square)](https://packagist.org/packages/bluetree-service/cache)

##### Builds
| Travis | Scrutinizer |
|:---:|:---:|
| [![Build Status](https://travis-ci.org/bluetree-service/cache.svg?style=flat-square)](https://travis-ci.org/bluetree-service/cache) | [![Build Status](https://scrutinizer-ci.com/g/bluetree-service/cache/badges/build.png?style=flat-square&b=master)](https://scrutinizer-ci.com/g/bluetree-service/cache/build-status/master) |

##### Coverage
| Coveralls | Scrutinizer |
|:---:|:---:|
| [![Coverage Status](https://coveralls.io/repos/github/bluetree-service/cache/badge.svg?style=flat-square&branch=master)](https://coveralls.io/github/bluetree-service/cache?branch=master) | [![Code Coverage](https://scrutinizer-ci.com/g/bluetree-service/cache/badges/coverage.png?style=flat-square&b=master)](https://scrutinizer-ci.com/g/bluetree-service/cache/?branch=master) |

##### Quality
| Code Climate | Scrutinizer | Sensio Labs |
|:---:|:---:|:---:|
| [![Code Climate](https://codeclimate.com/github/bluetree-service/cache/badges/gpa.svg?style=flat-square)](https://codeclimate.com/github/bluetree-service/cache) | [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/bluetree-service/cache/badges/quality-score.png?style=flat-square&b=master)](https://scrutinizer-ci.com/g/bluetree-service/cache/?branch=master) | [![SensioLabsInsight](https://insight.sensiolabs.com/projects/e9b6307b-4407-4fbe-8e40-233a3ec7f352/mini.png?style=flat-square)](https://insight.sensiolabs.com/projects/e9b6307b-4407-4fbe-8e40-233a3ec7f352) |
|  | [![Code Intelligence Status](https://scrutinizer-ci.com/g/bluetree-service/cache/badges/code-intelligence.svg?style=flat-square&b=master)](https://scrutinizer-ci.com/code-intelligence) |  |

PSR-6 and/or PSR-16 cache system, based on file storage.

### Included libraries
* **BlueCache\Cache** - Basic class for PSR-6 cache handling
* **BlueCache\SimpleCache** - Basic class for PSR-16 cache handling
* **BlueCache\CacheItem** - Cache item element for PSR-6 (used also not explicitly by SimpleCache)
* **BlueCache\CacheException** - Exception class for all PSR-6/16 exceptions
* **BlueCache\Common** - Common with PSR-6 and 16 methods
* **BlueCache\Storage\File** - Allow storage cache as files on local filesystem
* **BlueCache\Storage\StorageInterface** - Storage interface for future usage by other storage systems

Documentation
--------------
* [PSR-6 cache](https://github.com/bluetree-service/cache/blob/develop/doc/psr-6-cache.md "PSR-6 cache")
* [PSR-16 cache](https://github.com/bluetree-service/cache/blob/develop/doc/psr-16-cache.md "PSR-16 cache")
* [File storage](https://github.com/bluetree-service/cache/blob/develop/doc/FileStorage.md "File storage")


Install via Composer
--------------
To use packages you can just download package and pace it in your code. But recommended
way to use _BlueCache_ is install it via Composer. To include _BlueCache_
libraries paste into composer json:

```json
{
    "require": {
        "bluetree-service/cache": "version_number"
    }
}
```

Project description
--------------

### Used conventions

* **Namespaces** - each library use namespaces
* **PSR-2** - [PSR-2](http://www.php-fig.org/psr/psr-2/) coding standard
* **PSR-4** - [PSR-4](http://www.php-fig.org/psr/psr-4/) auto loading standard
* **PSR-6** - [PSR-6](http://www.php-fig.org/psr/psr-6/) cache standard
* **PSR-16** - [PSR-16](http://www.php-fig.org/psr/psr-16/) cache standard
* **Composer** - [Composer](https://getcomposer.org/)

### Requirements

* PHP 5.6 or higher

Change log
--------------
All release version changes:  
[Change log](https://github.com/bluetree-service/cache/blob/develop/doc/changelog.md "Change log")

License
--------------
This bundle is released under the Apache license.  
[Apache license](https://github.com/bluetree-service/cache/LICENSE "Apache license")

Travis Information
--------------
[Travis CI Build Info](https://travis-ci.org/bluetree-service/cache)
