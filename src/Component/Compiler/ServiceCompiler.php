<?php

/**
 * Copyright (C) GrizzIT, Inc. All rights reserved.
 * See LICENSE for license details.
 */

namespace Ulrack\Services\Component\Compiler;

use GrizzIt\Storage\Common\StorageInterface;
use GrizzIt\Validator\Common\ValidatorInterface;
use Ulrack\Services\Common\ServiceCompilerInterface;
use Ulrack\Services\Common\ServiceRegistryInterface;
use GrizzIt\ObjectFactory\Common\ObjectFactoryInterface;
use GrizzIt\Validator\Component\Logical\AlwaysValidator;
use Ulrack\Services\Common\AbstractServiceCompilerExtension;
use Ulrack\Services\Common\Hook\AbstractServiceCompilerHook;
use Ulrack\Services\Common\Hook\ServiceCompilerHookInterface;

class ServiceCompiler implements ServiceCompilerInterface
{
    /**
     * The key that is used in the storage to determine whether the storage
     * contains compiled service information.
     *
     * @var string
     */
    public const STORAGE_COMPILED_KEY = 'compiled';

    /**
     * The key that is used in the storage to store all compiled services.
     *
     * @var string
     */
    public const STORAGE_SERVICES_KEY = 'services';

    /**
     * Contains the service registry.
     *
     * @var ServiceRegistryInterface
     */
    private $registry;

    /**
     * Contains the compiled services.
     *
     * @var StorageInterface
     */
    private $serviceStorage;

    /**
     * Contains the object factory.
     *
     * @var ObjectFactoryInterface
     */
    private $objectFactory;

    /**
     * Contains the service compiler extensions.
     *
     * @var AbstractServiceCompilerExtension[][]
     */
    private $extensions = [];

    /**
     * Contains the service compiler hooks.
     *
     * @var AbstractServiceCompilerHook[][]
     */
    private $hooks = [];

    /**
     * Constructor.
     *
     * @param ServiceRegistryInterface $registry
     * @param StorageInterface $serviceStorage
     * @param ObjectFactoryInterface $objectFactory
     */
    public function __construct(
        ServiceRegistryInterface $registry,
        StorageInterface $serviceStorage,
        ObjectFactoryInterface $objectFactory
    ) {
        $this->registry = $registry;
        $this->serviceStorage = $serviceStorage;
        $this->objectFactory = $objectFactory;
    }

    /**
     * Adds an extension to the service compiler.
     *
     * @param string $key
     * @param string $class
     * @param int $sortOrder
     * @param ValidatorInterface $validator
     * @param array $parameters
     *
     * @return void
     */
    public function addExtension(
        string $key,
        string $class,
        int $sortOrder,
        ValidatorInterface $validator = null,
        array $parameters = []
    ): void {
        if (class_exists($class)) {
            $this->extensions[$sortOrder][] = $this->objectFactory
                ->create(
                    $class,
                    [
                        'registry' => $this->registry,
                        'key' => $key,
                        'validator' => $validator ?? new AlwaysValidator(true),
                        'parameters' => $parameters,
                        'getHooks' => [$this, 'getHooks']
                    ]
                );
        }
    }

    /**
     * Adds a hook to the key connected to an extension.
     *
     * @param string $key
     * @param string $class
     * @param int $sortOrder
     * @param array $parameters
     *
     * @return void
     */
    public function addHook(
        string $key,
        string $class,
        int $sortOrder,
        array $parameters = []
    ): void {
        if (class_exists($class)) {
            $this->hooks[$sortOrder][$key][] = $this->objectFactory
                ->create(
                    $class,
                    [
                        'registry' => $this->registry,
                        'key' => $key,
                        'parameters' => $parameters
                    ]
                );
        }
    }

    /**
     * Retrieve the hooks for a key.
     *
     * @param string $key
     *
     * @return ServiceCompilerHookInterface[]
     */
    public function getHooks(string $key): array
    {
        $hooks = [];

        foreach ($this->hooks as $hooksSet) {
            if (isset($hooksSet[$key])) {
                $hooks = array_merge($hooks, $hooksSet[$key]);
            }
        }

        return $hooks;
    }

    /**
     * Compiles the services and returns the compiled services.
     *
     * @return array
     */
    public function compile(): array
    {
        if ($this->serviceStorage->has(static::STORAGE_COMPILED_KEY)) {
            if (
                $this->serviceStorage->get(
                    static::STORAGE_COMPILED_KEY
                ) === true
            ) {
                return $this->serviceStorage->get(
                    static::STORAGE_SERVICES_KEY
                );
            }

            $this->serviceStorage->unset(static::STORAGE_SERVICES_KEY);
        }

        ksort($this->extensions);
        $services = [];
        foreach ($this->extensions as $extensionSet) {
            foreach ($extensionSet as $extension) {
                $services[$extension->getKey()] = $extension->fetch();
            }
        }

        foreach ($this->extensions as $extensionSet) {
            foreach ($extensionSet as $extension) {
                $services = $extension->compile($services);
            }
        }

        $this->serviceStorage->set(static::STORAGE_COMPILED_KEY, true);
        $this->serviceStorage->set(static::STORAGE_SERVICES_KEY, $services);

        return $services;
    }
}
