<?php

/**
 * Copyright (C) GrizzIT, Inc. All rights reserved.
 * See LICENSE for license details.
 */

namespace Ulrack\Services\Common;

interface ServiceRegistryInterface
{
    /**
     * Adds a declaration to a key.
     *
     * @param string $key
     * @param string $name
     * @param array $definition
     *
     * @return void
     */
    public function add(string $key, string $name, array $definition): void;

    /**
     * Retrieves all declaration for a key.
     *
     * @return array
     */
    public function get(string $key): array;

    /**
     * Retrieve all keys for the service registry.
     *
     * @return array
     */
    public function getKeys(): array;
}
