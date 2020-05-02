<?php

/**
 * Copyright (C) GrizzIT, Inc. All rights reserved.
 * See LICENSE for license details.
 */

namespace Ulrack\Services\Common\Hook;

abstract class AbstractServiceFactoryHook implements ServiceFactoryHookInterface
{
    /**
     * Contains the key of the service extension.
     *
     * @var string
     */
    private $key;

    /**
     * Contains the parameters for the extension.
     *
     * @var array
     */
    private $parameters;

    /**
     * Contains the compiled services.
     *
     * @var array
     */
    private $services;

    /**
     * Constructor
     *
     * @param string $key
     * @param array $parameters
     * @param array $services
     * @param array $internalServices
     */
    public function __construct(
        string $key,
        array $parameters,
        array $services,
        array $internalServices = []
    ) {
        $this->key              = $key;
        $this->parameters       = $parameters;
        $this->services         = $services;
        $this->internalServices = $internalServices;
    }

    /**
     * Hooks in before the creation of a service.
     *
     * @param string $serviceKey
     * @param array $parameters
     *
     * @return string
     */
    public function preCreate(
        string $serviceKey,
        array $parameters = []
    ): array {
        return ['serviceKey' => $serviceKey, 'parameters' => $parameters];
    }

    /**
     * Hooks in after the creation of a service.
     *
     * @param string $serviceKey
     * @param mixed $return
     * @param array $parameters
     *
     * @return array
     */
    public function postCreate(
        string $serviceKey,
        $return,
        array $parameters = []
    ): array {
        return [
            'serviceKey' => $serviceKey,
            'return' => $return,
            'parameters' => $parameters
        ];
    }

    /**
     * Retrieves the internal service.
     *
     * @param string $serviceKey
     *
     * @return object
     */
    public function getInternalService(string $serviceKey): object
    {
        return $this->internalServices[$serviceKey];
    }

    /**
     * Retrieves the registry.
     *
     * @return array
     */
    public function getServices(): array
    {
        return $this->services;
    }

    /**
     * Retrieves the key of the service.
     *
     * @return string
     */
    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * Retrieves the requested extension parameter.
     *
     * @param string $key
     *
     * @return mixed
     */
    public function getParameter(string $key)
    {
        return $this->parameters[$key];
    }

    /**
     * Retrieves the parameters for the extension.
     *
     * @return array
     */
    public function getParameters(): array
    {
        return $this->parameters;
    }
}
