<?php
/**
 * Copyright (C) GrizzIT, Inc. All rights reserved.
 * See LICENSE for license details.
 */
namespace Ulrack\Services\Common;

interface ServiceCompilerExtensionInterface
{
    /**
     * Retrieve the service definitions.
     *
     * @return array
     */
    public function fetch(): array;

    /**
     * Compile the services.
     *
     * @param array $services
     *
     * @return array
     */
    public function compile(array $services): array;
}
