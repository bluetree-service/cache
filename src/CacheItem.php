<?php

namespace BlueCache;

use Psr\Cache\CacheItemInterface;

class CacheItem implements CacheItemInterface
{
    const ALLOWED_KEY_CHARS = '#^[\w_-]+$#';

    /**
     * @var string
     */
    protected $key;

    /**
     * @var mixed
     */
    protected $data;

    /**
     * @var int
     */
    protected $expire;

    /**
     * @var array
     */
    protected $config = [
        'expire' => 86400,
    ];

    /**
     * CacheItem constructor.
     *
     * @param string $key
     * @param array $config
     * @throws \BlueCache\CacheException
     */
    public function __construct($key, array $config = [])
    {
        if ($this->isKeyInvalid($key)) {
            throw new CacheException('Invalid key. Should us only chars, numbers and _. Use: ' . $key);
        }

        $this->key = $key;
        $this->config = array_merge($this->config, $config);
    }

    /**
     * @param string $key
     * @return bool
     */
    protected function isKeyInvalid($key)
    {
        return !preg_match(self::ALLOWED_KEY_CHARS, $key);
    }

    /**
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * @return mixed|null
     */
    public function get()
    {
        return $this->isHit() ? $this->data : null;
    }

    /**
     * check that cashed data exists
     *
     * @return bool
     */
    public function isHit()
    {
        if (is_null($this->data)) {
            return false;
        }

        if (is_null($this->expire)) {
            return true;
        }

        return !($this->expire <= time());
    }

    /**
     * @param mixed $value
     * @return $this
     */
    public function set($value)
    {
        $this->data = $value;
        $this->expire = null;

        return $this;
    }

    /**
     * @param \DateTimeInterface|null $expiration
     * @return $this
     * @throws \BlueCache\CacheException
     */
    public function expiresAt($expiration = null)
    {
        return $this->setExpiration($expiration);
    }

    /**
     * @param \DateTimeInterface|null|int $expire
     * @return $this
     * @throws \BlueCache\CacheException
     */
    protected function setExpiration($expire)
    {
        switch (true) {
            case is_null($expire):
                $this->expire = $this->config['expire'] + time();
                break;

            case is_int($expire):
                $this->expire = time() + $expire;
                break;

            case $expire instanceof \DateTimeInterface:
                $this->expire = $expire->getTimestamp();
                break;

            case $expire instanceof \DateInterval:
                $this->expire = (new \DateTime)
                    ->add($expire)
                    ->getTimestamp();
                break;

            default:
                throw new CacheException('Invalid expire type.');
        }

        return $this;
    }

    /**
     * @param \DateInterval|int|null $time
     * @return $this
     * @throws \BlueCache\CacheException
     */
    public function expiresAfter($time = null)
    {
        return $this->setExpiration($time);
    }
}
