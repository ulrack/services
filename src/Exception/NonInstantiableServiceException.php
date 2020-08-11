<?php

/**
 * Copyright (C) GrizzIT, Inc. All rights reserved.
 * See LICENSE for license details.
 */

namespace Ulrack\Services\Exception;

use Exception;
use Throwable;

class NonInstantiableServiceException extends Exception
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
                'Tried to instantiate non-instantiable service %s.',
                $serviceKey
            ),
            0,
            $previous
        );
    }
}
