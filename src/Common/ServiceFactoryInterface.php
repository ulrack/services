<?php

/**
 * Copyright (C) GrizzIT, Inc. All rights reserved.
 * See LICENSE for license details.
 */

namespace Ulrack\Services\Common;

interface ServiceFactoryInterface
{
    /**
     * Retrieve the interpreted value of a service.
     *
     * @param string $key
     * @param array $parameters
     *
     * @return mixed
     */
    public function create(string $key, array $parameters = []);

    /**
     * Adds an extension to the service factory.
     *
     * @param string $key
     * @param string $class
     * @param array $parameters
     *
     * @return void
     */
    public function addExtension(
        string $key,
        string $class,
        array $parameters = []
    ): void;

    /**
     * Adds a hook to the key connected to an extension.
     *
     * @param string $key
     * @param string $class
     * @param int $sortOrder
     * @param array $parameters
     *
     * @return void
     */
    public function addHook(
        string $key,
        string $class,
        int $sortOrder,
        array $parameters = []
    ): void;
}
