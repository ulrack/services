<?php

/**
 * Copyright (C) GrizzIT, Inc. All rights reserved.
 * See LICENSE for license details.
 */

namespace Ulrack\Services\Factory;

use Ulrack\Services\Common\ServiceFactoryInterface;
use Ulrack\Services\Common\ServiceCompilerInterface;
use Ulrack\Services\Common\AbstractServiceFactoryHook;
use Ulrack\Services\Exception\ServiceNotFoundException;
use GrizzIt\ObjectFactory\Common\ClassAnalyserInterface;
use GrizzIt\ObjectFactory\Common\ObjectFactoryInterface;
use Ulrack\Services\Common\AbstractServiceFactoryExtension;
use Ulrack\Services\Common\Hook\ServiceFactoryHookInterface;
use Ulrack\Services\Common\ServiceFactoryExtensionInterface;

class ServiceFactory implements ServiceFactoryInterface
{
    /**
     * Contains the service compiler.
     *
     * @var ServiceCompilerInterface
     */
    private $serviceCompiler;

    /**
     * Contains the object factory.
     *
     * @var ObjectFactoryInterface
     */
    private $objectFactory;

    /**
     * Contains the extensions.
     *
     * @var AbstractServiceFactoryExtension[]
     */
    private $extensions;

    /**
     * Contains the class analyser.
     *
     * @var ClassAnalyserInterface
     */
    private $classAnalyser;

    /**
     * Contains the compiled services.
     *
     * @var array
     */
    private $services;

    /**
     * Contains the service factory hooks.
     *
     * @var AbstractServiceFactoryHook[][]
     */
    private $hooks = [];

    /**
     * Constructor.
     *
     * @param ServiceCompilerInterface $serviceCompiler
     * @param ObjectFactoryInterface   $objectFactory
     * @param ClassAnalyserInterface   $classAnalyser
     */
    public function __construct(
        ServiceCompilerInterface $serviceCompiler,
        ObjectFactoryInterface $objectFactory,
        ClassAnalyserInterface $classAnalyser
    ) {
        $this->serviceCompiler = $serviceCompiler;
        $this->objectFactory = $objectFactory;
        $this->classAnalyser = $classAnalyser;
        $this->services = $this->serviceCompiler->compile();
    }

    /**
     * Adds an extension to the service factory.
     *
     * @param string $key
     * @param string $class
     * @param array $parameters
     *
     * @return void
     */
    public function addExtension(
        string $key,
        string $class,
        array $parameters = []
    ): void {
        if (class_exists($class)) {
            $this->extensions[$key] = $this->objectFactory
                ->create(
                    $class,
                    [
                        'serviceFactory' => $this,
                        'key' => $key,
                        'parameters' => $parameters,
                        'services' => $this->services,
                        'internalServices' => [
                            'object-factory' => $this->objectFactory,
                            'class-analyser' => $this->classAnalyser
                        ],
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
     * @param integer $sortOrder
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
                        'key' => $key,
                        'parameters' => $parameters,
                        'services' => $this->services,
                        'internalServices' => [
                            'object-factory' => $this->objectFactory,
                            'class-analyser' => $this->classAnalyser
                        ]
                    ]
                );
        }
    }

    /**
     * Retrieve the hooks for a key.
     *
     * @param string $key
     *
     * @return ServiceFactoryHookInterface[]
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
     * Retrieve the extension for a key.
     *
     * @param string $key
     *
     * @return ServiceFactoryExtensionInterface
     */
    public function getExtension(string $key): ServiceFactoryExtensionInterface
    {
        return $this->extensions[$key];
    }

    /**
     * Retrieve the interpreted value of a service.
     *
     * @param string $key
     *
     * @return mixed
     */
    public function create(string $key)
    {
        $firstDot = strpos($key, '.');
        $serviceKey = $firstDot !== false ? substr(
            $key,
            0,
            $firstDot
        ) : $key;

        if (isset($this->extensions[$serviceKey])) {
            return $this->extensions[$serviceKey]->create($key);
        }

        throw new ServiceNotFoundException($key);
    }
}
