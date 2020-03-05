<?php
/**
 * Copyright (C) GrizzIT, Inc. All rights reserved.
 * See LICENSE for license details.
 */
namespace Ulrack\Services\Component\Registry;

use Ulrack\Services\Common\ServiceRegistryInterface;

class ServiceRegistry implements ServiceRegistryInterface
{
    /**
     * Contains all services.
     *
     * @var array
     */
    private $services = [];

    /**
     * Adds a declaration to a key.
     *
     * @param string $key
     * @param string $name
     * @param mixed  $definition
     *
     * @return void
     */
    public function add(string $key, string $name, $definition): void
    {
        $this->services[$key][$name] = $definition;
    }

    /**
     * Retrieves all declaration for a key.
     *
     * @param string $key
     *
     * @return array
     */
    public function get(string $key): array
    {
        if (isset($this->services[$key])) {
            return $this->services[$key];
        }

        return [];
    }

    /**
     * Retrieve all keys for the service registry.
     *
     * @return array
     */
    public function getKeys(): array
    {
        return array_keys($this->services);
    }
}
