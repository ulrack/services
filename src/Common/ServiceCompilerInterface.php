<?php

/**
 * Copyright (C) GrizzIT, Inc. All rights reserved.
 * See LICENSE for license details.
 */

namespace Ulrack\Services\Common;

use GrizzIt\Validator\Common\ValidatorInterface;

interface ServiceCompilerInterface
{
    /**
     * Compiles the services and returns the compiled services.
     *
     * @return array
     */
    public function compile(): array;

    /**
     * Adds an extension to the service compiler.
     *
     * @param string $key
     * @param string $class
     * @param int $sortOrder
     * @param ValidatorInterface $schema
     * @param array $parameters
     *
     * @return void
     */
    public function addExtension(
        string $key,
        string $class,
        int $sortOrder,
        ValidatorInterface $schema = null,
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
