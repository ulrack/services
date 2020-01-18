<?php
/**
 * Copyright (C) Jyxon, Inc. All rights reserved.
 * See LICENSE for license details.
 */
namespace Ulrack\Services\Common\Hook;

interface ServiceFactoryHookInterface
{
    /**
     * Hooks in before the creation of a service.
     *
     * @param string $serviceKey
     * @param array $parameters
     *
     * @return array
     */
    public function preCreate(
        string $serviceKey,
        array $parameters = []
    ): array;

    /**
     * Hooks in after the creation of a service.
     *
     * @param string $serviceKey
     * @param mixed $return
     * @param array $parameters
     *
     * @return mixed
     */
    public function postCreate(
        string $serviceKey,
        $return,
        array $parameters = []
    ): array;
}
