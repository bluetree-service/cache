<?php

declare(strict_types=1);

namespace BlueCache;

use Psr\Cache\CacheItemInterface;

class CacheItem implements CacheItemInterface
{
    public const ALLOWED_KEY_CHARS = '#^[\w_-]+$#';

    /**
     * @var string
     */
    protected string $key;

    /**
     * @var mixed
     */
    protected mixed $data = null;

    /**
     * @var int|null
     */
    protected int|null $expire = null;

    /**
     * @var int|null
     */
    protected int|null $expireAfter = null;

    /**
     * @var array
     */
    protected array $config = [
        'expire' => 86400,
    ];

    /**
     * CacheItem constructor.
     *
     * @param string $key
     * @param array $config
     * @throws CacheException
     */
    public function __construct(string $key, array $config = [])
    {
        if ($this->isKeyInvalid($key)) {
            throw new CacheException('Invalid key. Should us only chars, numbers and _. Use: ' . $key);
        }

        $this->key = $key;
        $this->config = \array_merge($this->config, $config);
    }

    /**
     * @param string $key
     * @return bool
     */
    protected function isKeyInvalid(string $key): bool
    {
        return !\preg_match(self::ALLOWED_KEY_CHARS, $key);
    }

    /**
     * @return string
     */
    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * @return mixed|null
     */
    public function get(): mixed
    {
        return $this->isHit() ? $this->data : null;
    }

    /**
     * check that cashed data exists
     *
     * @return bool
     */
    public function isHit(): bool
    {
        if (\is_null($this->data)) {
            return false;
        }

        if (\is_null($this->expire)) {
            return true;
        }

        return $this->expire > \time();
    }

    /**
     * @param mixed $value
     * @return $this
     */
    public function set(mixed $value): self
    {
        $this->data = $value;
        if (!\is_null($this->expireAfter)) {
            $this->expiresAfter($this->expireAfter);
        }

        return $this;
    }

    /**
     * @param \DateTimeInterface|null $expiration
     * @return $this
     */
    public function expiresAt($expiration = null): self
    {
        return $this->setExpiration($expiration);
    }

    /**
     * @param \DateTimeInterface|\DateInterval|null|int $expire
     * @return $this
     */
    protected function setExpiration(\DateInterval|\DateTimeInterface|null|int $expire): self
    {
        switch (true) {
            case \is_null($expire):
                $this->expire = $this->config['expire'] + \time();
                $this->expireAfter = $this->config['expire'];
                break;

            case \is_int($expire):
                $this->expire = \time() + $expire;
                $this->expireAfter = $expire;
                break;

            case $expire instanceof \DateTimeInterface:
                $this->expire = $expire->getTimestamp();
                $this->expireAfter = $this->expire - time();
                break;

            case $expire instanceof \DateInterval:
                $this->expire = (new \DateTime())
                    ->add($expire)
                    ->getTimestamp();
                $this->expireAfter = $this->expire - time();
                break;
        }

        return $this;
    }

    /**
     * @param \DateTimeInterface|\DateInterval|int|null $time
     * @return $this
     */
    public function expiresAfter($time = null): self
    {
        return $this->setExpiration($time);
    }

    /**
     * @return int|null
     */
    public function getExpirationTime(): ?int
    {
        return $this->expire;
    }

    /**
    * @return string|null
    */
    public function getExpirationTimeFormatted(): ?string
    {
        return $this->expire ? date('c', $this->expire) : null;
    }
}
