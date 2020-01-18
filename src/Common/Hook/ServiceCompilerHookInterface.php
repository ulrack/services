<?php
/**
 * Copyright (C) Jyxon, Inc. All rights reserved.
 * See LICENSE for license details.
 */
namespace Ulrack\Services\Common\Hook;

interface ServiceCompilerHookInterface
{
    /**
     * Hooks in before compiling all services in the compiler.
     *
     * @param array $services
     * @param array $parameters
     *
     * @return array
     */
    public function preCompile(array $services, array $parameters = []): array;

    /**
     * Hooks in after compiling all services in the compiler.
     *
     * @param array $services
     * @param array $return
     * @param array $parameters
     *
     * @return array
     */
    public function postCompile(
        array $services,
        array $return,
        array $parameters = []
    ): array;

    /**
     * Hooks in after fetching all services in the compiler.
     *
     * @param array $return
     * @param array $parameters
     *
     * @return array
     */
    public function postFetch(
        array $return,
        array $parameters = []
    ): array;
}
