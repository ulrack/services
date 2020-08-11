<?php

/**
 * Copyright (C) GrizzIT, Inc. All rights reserved.
 * See LICENSE for license details.
 */

namespace Ulrack\Services\Exception;

use Exception;
use Throwable;

class ServiceNotFoundException extends Exception
{
    /**
     * Constructor.
     *
     * @param string $serviceKey
     * @param Throwable $previous
     */
    public function __construct(
        string $serviceKey,
        Throwable $previous = null
    ) {
        parent::__construct(
            sprintf(
                'Tried to create service %s, no handler found.',
                $serviceKey
            ),
            0,
            $previous
        );
    }
}
