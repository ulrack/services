<?php

/**
 * Copyright (C) GrizzIT, Inc. All rights reserved.
 * See LICENSE for license details.
 */

namespace Ulrack\Services\Factory\Extension;

use Throwable;
use GrizzIt\ObjectFactory\Common\ClassAnalyserInterface;
use Ulrack\Services\Exception\InvalidArgumentException;
use Ulrack\Services\Exception\MissingPreferenceException;
use Ulrack\Services\Exception\DefinitionNotFoundException;
use Ulrack\Services\Common\AbstractServiceFactoryExtension;
use Ulrack\Services\Exception\NonInstantiableServiceException;
use GrizzIt\ObjectFactory\Exception\NonInstantiableClassException;

class ServicesFactory extends AbstractServiceFactoryExtension
{
    /**
     * Contains the objects which have been created previously.
     *
     * @var array
     */
    private $objects = [];

    /**
     * Register a value to a service key.
     *
     * @param string $serviceKey
     * @param mixed $value
     *
     * @return void
     */
    public function registerService(string $serviceKey, $value): void
    {
        $this->objects[$serviceKey] = $value;
    }

    /**
     * Retrieve the parameter from the services.
     *
     * @param string $serviceKey
     *
     * @return object
     *
     * @throws NonInstantiableServiceException When the service is defined as abstract.
     * @throws MissingPreferenceException When the class that is being instantiated is not instantiable.
     * @throws InvalidArgumentException When the arguments provided for the class are invalid.
     * @throws DefinitionNotFoundException When the definition can not be found.
     */
    public function create(string $serviceKey): object
    {
        $serviceKey = $this->preCreate(
            $serviceKey,
            $this->getParameters()
        )['serviceKey'];

        $internalKey = preg_replace(
            sprintf('/^%s\\./', preg_quote($this->getKey())),
            '',
            $serviceKey,
            1
        );

        if (isset($this->objects[$internalKey])) {
            return $this->postCreate(
                $serviceKey,
                $this->objects[$internalKey],
                $this->getParameters()
            )['return'];
        }

        $services = $this->getServices()[$this->getKey()];

        if (isset($services[$internalKey])) {
            $service = $services[$internalKey];
            if (isset($service['abstract']) && $service['abstract'] === true) {
                throw new NonInstantiableServiceException($serviceKey);
            }

            $parameters = [];
            /** @var ClassAnalyserInterface $classAnalyser */
            $classAnalyser = $this->getInternalService('class-analyser');

            try {
                $parametersAnalysis = $classAnalyser->analyse(
                    $service['class']
                );
            } catch (NonInstantiableClassException $exception) {
                throw new MissingPreferenceException(
                    $service['class'],
                    $exception
                );
            }

            foreach ($parametersAnalysis as $parameterName => $parameterAnalysis) {
                $parameterValue = $parameterAnalysis['default'];

                if (isset($service['parameters'][$parameterName])) {
                    $newValue = $this->resolveReferences(
                        $service['parameters'][$parameterName]
                    );

                    while ($newValue !== $parameterValue) {
                        $parameterValue = $newValue;
                        $newValue = $this->resolveReferences($parameterValue);
                    }
                }

                $parameters[$parameterName] = $parameterValue;
            }

            /** @var ObjectFactoryInterface $objectFactory */
            $objectFactory = $this->getInternalService('object-factory');

            try {
                $this->objects[$internalKey] = $objectFactory->create(
                    $service['class'],
                    $parameters
                );

                return $this->postCreate(
                    $serviceKey,
                    $this->objects[$internalKey],
                    $this->getParameters()
                )['return'];
            } catch (Throwable $exception) {
                throw new InvalidArgumentException(
                    $parameters,
                    $service['class'],
                    $exception
                );
            }
        }

        throw new DefinitionNotFoundException($serviceKey);
    }
}
