<?php

/**
 * Copyright (C) GrizzIT, Inc. All rights reserved.
 * See LICENSE for license details.
 */

namespace Ulrack\Services\Common;

use Ulrack\Services\Common\ServiceFactoryInterface;

abstract class AbstractServiceFactoryExtension implements ServiceFactoryExtensionInterface
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
     * Contains the callable to retrieve the hooks for a key.
     *
     * @var callable
     */
    private $getHooks;

    /**
     * Contains the main service factory.
     *
     * @var ServiceFactoryInterface
     */
    private $serviceFactory;

    /**
     * Constructor
     *
     * @param ServiceFactoryInterface $serviceFactory
     * @param string $key
     * @param array $parameters
     * @param array $services
     * @param callable $getHooks
     * @param array $internalServices
     */
    public function __construct(
        ServiceFactoryInterface $serviceFactory,
        string $key,
        array $parameters,
        array $services,
        callable $getHooks,
        array $internalServices = []
    ) {
        $this->serviceFactory   = $serviceFactory;
        $this->key              = $key;
        $this->parameters       = $parameters;
        $this->services         = $services;
        $this->internalServices = $internalServices;
        $this->getHooks         = $getHooks;
    }

    /**
     * Invokes the defined connected hooks.
     *
     * @param string $method
     * @param array $input
     *
     * @return array
     */
    private function invokeOnHooks(string $method, array $input): array
    {
        foreach (($this->getHooks)($this->getKey()) as $hook) {
            $input = array_merge(
                $input,
                call_user_func_array([$hook, $method], $input)
            );
        }

        return $input;
    }

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
    ): array {
        return $this->invokeOnHooks(
            'preCreate',
            ['serviceKey' => $serviceKey, 'parameters' => $parameters]
        );
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
        return $this->invokeOnHooks(
            'postCreate',
            [
                'serviceKey' => $serviceKey,
                'return' => $return,
                'parameters' => $parameters
            ]
        );
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

    /**
     * Determines whether a string is a reference to another registration.
     *
     * @param string $input
     *
     * @return bool
     */
    public function isReference(string $input): bool
    {
        return preg_match('/^@{.+}$/', $input) === 1;
    }

    /**
     * Calls the main service factory to retrieve a value.
     *
     * @param string $service
     *
     * @return mixed
     */
    public function superCreate(string $service)
    {
        return $this->serviceFactory->create($service);
    }

    /**
     * Resolves references in an object to other services if applicable.
     *
     * @param mixed $value
     *
     * @return mixed
     */
    public function resolveReferences($value)
    {
        if (is_string($value) && $this->isReference($value)) {
            $value = $this->superCreate(trim($value, '@{}'));
        }

        if (is_array($value)) {
            foreach ($value as $key => $item) {
                $value[$key] = $this->resolveReferences($item);
            }
        }

        return $value;
    }
}
