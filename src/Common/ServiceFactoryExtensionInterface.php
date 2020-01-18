<?php
/**
 * Copyright (C) Jyxon, Inc. All rights reserved.
 * See LICENSE for license details.
 */
namespace Ulrack\Services\Common;

interface ServiceFactoryExtensionInterface
{
    /**
     * Retrieve the value of the service key.
     *
     * @return mixed
     */
    public function create(string $serviceKey);
}
