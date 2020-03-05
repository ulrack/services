<?php
/**
 * Copyright (C) GrizzIT, Inc. All rights reserved.
 * See LICENSE for license details.
 */
namespace Ulrack\Services\Exception;

use Exception;
use RuntimeException;

class MissingPreferenceException extends RuntimeException
{
    /**
     * Constructor.
     *
     * @param string $class
     * @param Exception $previous
     */
    public function __construct(
        string $class,
        Exception $previous = null
    ) {
        parent::__construct(
            sprintf('Missing preference for %s', $class),
            0,
            $previous
        );
    }
}
