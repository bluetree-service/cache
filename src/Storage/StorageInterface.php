<?php

namespace BlueCache\Storage;

interface StorageInterface
{
    /**
     * StorageInterface constructor.
     *
     * @param array $params
     */
    public function __construct(array $params);

    /**
     * @param string $name
     * @param mixed $data
     * @param string $type
     * @return StorageInterface
     */
    public function store($name, $data, $type = 'string');

    /**
     * @param string $name
     * @param string $type
     * @return mixed
     */
    public function restore($name, $type = 'string');
}
