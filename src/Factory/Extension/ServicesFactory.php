<?php
/**
 * Copyright (C) GrizzIT, Inc. All rights reserved.
 * See LICENSE for license details.
 */
namespace Ulrack\Services\Factory\Extension;

use Throwable;
use Ulrack\ObjectFactory\Common\ClassAnalyserInterface;
use Ulrack\Services\Exception\InvalidArgumentException;
use Ulrack\Services\Exception\MissingPreferenceException;
use Ulrack\Services\Exception\DefinitionNotFoundException;
use Ulrack\Services\Common\AbstractServiceFactoryExtension;
use Ulrack\Services\Exception\NonInstantiableServiceException;
use Ulrack\ObjectFactory\Exception\NonInstantiableClassException;

class ServicesFactory extends AbstractServiceFactoryExtension
{
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

        $services = $this->getServices()[$this->getKey()];

        $internalKey = preg_replace(
            sprintf('/^%s\\./', preg_quote($this->getKey())),
            '',
            $serviceKey,
            1
        );

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
                    $parameterValue = $this->resolveReference(
                        $service['parameters'][$parameterName]
                    );

                    if (is_array($parameterValue)) {
                        foreach ($parameterValue as &$value) {
                            $value = $this->resolveReference($value);
                        }
                    }
                }

                $parameters[$parameterName] = $parameterValue;
            }

            /** @var ObjectFactoryInterface $objectFactory */
            $objectFactory = $this->getInternalService('object-factory');

            try {
                return $this->postCreate(
                    $serviceKey,
                    $objectFactory->create($service['class'], $parameters),
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

    /**
     * Resolves a reference to another service if applicable.
     *
     * @param mixed $value
     *
     * @return mixed
     */
    private function resolveReference($value)
    {
        if (is_string($value) && preg_match(
            '/^\\@\\{/',
            $value
        ) === 1) {
            $value = $this->create(trim($value, '@{}'));
        }

        return $value;
    }
}
