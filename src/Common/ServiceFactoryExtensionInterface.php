<?php

/**
 * Copyright (C) GrizzIT, Inc. All rights reserved.
 * See LICENSE for license details.
 */

namespace Ulrack\Services\Common;

interface ServiceFactoryExtensionInterface
{
    /**
     * Register a value to a service key.
     *
     * @param string $serviceKey
     * @param mixed $value
     *
     * @return void
     */
    public function registerService(string $serviceKey, $value): void;

    /**
     * Retrieve the value of the service key.
     *
     * @return mixed
     */
    public function create(string $serviceKey);
}
