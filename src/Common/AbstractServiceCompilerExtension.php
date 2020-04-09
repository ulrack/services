<?php
/**
 * Copyright (C) GrizzIT, Inc. All rights reserved.
 * See LICENSE for license details.
 */
namespace Ulrack\Services\Common;

use GrizzIt\Validator\Common\ValidatorInterface;

abstract class AbstractServiceCompilerExtension implements ServiceCompilerExtensionInterface
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
     * Contains the schema validator.
     *
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * Contains the parameters for the extension.
     *
     * @var array
     */
    private $parameters;

    /**
     * Contains the callable to retrieve the hooks for a key.
     *
     * @var callable
     */
    private $getHooks;

    /**
     * Constructor.
     *
     * @param ServiceRegistryInterface $registry
     * @param string $key
     * @param ValidatorInterface $validator
     * @param array $parameters
     * @param callable $getHooks
     */
    public function __construct(
        ServiceRegistryInterface $registry,
        string $key,
        ValidatorInterface $validator,
        array $parameters,
        callable $getHooks
    ) {
        $this->registry = $registry;
        $this->key = $key;
        $this->validator = $validator;
        $this->parameters = $parameters;
        $this->getHooks = $getHooks;
    }

    /**
     * Retrieves the registry.
     *
     * @return array
     */
    public function getServices(): array
    {
        $registry = $this->registry->get($this->key);
        foreach ($registry as $key => $entry) {
            if (!$this->validator->__invoke(
                json_decode(
                    json_encode($entry)
                )
            )) {
                unset($registry[$key]);
            }
        }

        return $registry;
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
     * Hooks in before compiling all services in the compiler.
     *
     * @param array $services
     * @param array $parameters
     *
     * @return array
     */
    public function preCompile(array $services, array $parameters = []): array
    {
        return $this->invokeOnHooks(
            'preCompile',
            ['services' => $services, 'parameters' => $parameters]
        );
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
        return $this->invokeOnHooks(
            'postCompile',
            [
                'services' => $services,
                'return' => $return,
                'parameters' => $parameters
            ]
        );
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
        return $this->invokeOnHooks(
            'postFetch',
            ['return' => $return, 'parameters' => $parameters]
        );
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
     * Retrieve the service definitions.
     *
     * @return array
     */
    public function fetch(): array
    {
        return $this->postFetch(
            $this->getServices(),
            $this->getParameters()
        )['return'];
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
