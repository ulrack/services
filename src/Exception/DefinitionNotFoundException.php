<?php
/**
 * Copyright (C) Jyxon, Inc. All rights reserved.
 * See LICENSE for license details.
 */
namespace Ulrack\Services\Exception;

use RuntimeException;

class DefinitionNotFoundException extends RuntimeException
{
    /**
     * Constructor.
     *
     * @param string $key
     * @param Exception $previous
     */
    public function __construct(
        string $key,
        Exception $previous = null
    ) {
        parent::__construct(
            sprintf('Could not find definition for %s.', $key),
            0,
            $previous
        );
    }
}
