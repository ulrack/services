<?php
/**
 * Copyright (C) Jyxon, Inc. All rights reserved.
 * See LICENSE for license details.
 */
namespace Ulrack\Services\Common\Hook;

use Ulrack\Services\Common\ServiceRegistryInterface;

abstract class AbstractServiceCompilerHook implements ServiceCompilerHookInterface
{
    /**
     * Contains the service registry.
     *
     * @var ServiceRegistryInterface
     */
    private $registry;

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
     * Constructor.
     *
     * @param ServiceRegistryInterface $registry
     * @param string $key
     * @param array $parameters
     */
    public function __construct(
        ServiceRegistryInterface $registry,
        string $key,
        array $parameters
    ) {
        $this->registry = $registry;
        $this->key = $key;
        $this->parameters = $parameters;
    }

    /**
     * Hooks in before compiling all services in the compiler.
     *
     * @param array $services
     * @param array $parameters
     *
     * @return array
     */
    public function preCompile(array $services, array $parameters = []): array
    {
        return ['services' => $services, 'parameters' => $parameters];
    }

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
    ): array {
        return [
            'services' => $services,
            'return' => $return,
            'parameters' => $parameters
        ];
    }

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
    ): array {
        return ['return' => $return, 'parameters' => $parameters];
    }

    /**
     * Retrieves the registry.
     *
     * @return ServiceRegistryInterface
     */
    public function getRegistry(): ServiceRegistryInterface
    {
        return $this->registry;
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
