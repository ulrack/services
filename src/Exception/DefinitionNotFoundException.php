<?php

/**
 * Copyright (C) GrizzIT, Inc. All rights reserved.
 * See LICENSE for license details.
 */

namespace Ulrack\Services\Exception;

use Throwable;
use RuntimeException;

class DefinitionNotFoundException extends RuntimeException
{
    /**
     * Constructor.
     *
     * @param string $key
     * @param Throwable $previous
     */
    public function __construct(
        string $key,
        Throwable $previous = null
    ) {
        parent::__construct(
            sprintf('Could not find definition for %s.', $key),
            0,
            $previous
        );
    }
}
