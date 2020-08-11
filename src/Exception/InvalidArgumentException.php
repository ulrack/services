<?php

/**
 * Copyright (C) GrizzIT, Inc. All rights reserved.
 * See LICENSE for license details.
 */

namespace Ulrack\Services\Exception;

use Exception;
use Throwable;
use InvalidArgumentException as PhpInvalidArgumentException;

class InvalidArgumentException extends PhpInvalidArgumentException
{
    /**
     * Constructor.
     *
     * @param array $parameters
     * @param string $class
     * @param Throwable $previous
     */
    public function __construct(
        array $parameters,
        string $class,
        Throwable $previous = null
    ) {
        parent::__construct(
            sprintf(
                'Tried to create object of instance %s, with parameters: %s',
                $class,
                print_r(
                    $parameters,
                    true
                )
            ),
            0,
            $previous
        );
    }
}
