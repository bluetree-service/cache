<?php

declare(strict_types=1);

namespace BlueCache;

use Exception;
use Psr\Cache\CacheException as CacheExceptionInterface;

/**
 * Exception interface for all exceptions thrown by an Implementing Library.
 */
class CacheException extends Exception implements CacheExceptionInterface
{
}
