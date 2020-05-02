<?php

/**
 * Copyright (C) GrizzIT, Inc. All rights reserved.
 * See LICENSE for license details.
 */

namespace Ulrack\Services\Component\Compiler\Extension;

use Ulrack\Services\Common\AbstractServiceCompilerExtension;

class ParametersCompiler extends AbstractServiceCompilerExtension
{
    /**
     * Retrieve the service definitions.
     *
     * @return array
     */
    public function fetch(): array
    {
        return $this->postFetch(
            $this->prepareParameters(parent::fetch()),
            $this->getParameters()
        )['return'];
    }

    /**
     * Compile the services.
     *
     * @param array $services
     *
     * @return array
     */
    public function compile(array $services): array
    {
        $services = $this->preCompile(
            $services,
            $this->getParameters()
        )['services'];

        $serviceKey = $this->getKey();
        $parameters = $services[$serviceKey];
        $newServices = $services;
        unset($newServices['parameters']);

        $return = array_merge(
            $this->layerParameters(
                $newServices,
                $parameters
            ),
            [$serviceKey => $parameters]
        );

        return $this->postCompile(
            $services,
            $return,
            $this->getParameters()
        )['return'];
    }

    /**
     * Prepares the parameters so they can be easily replaced.
     *
     * @param array $parameters
     *
     * @return array
     */
    private function prepareParameters(array $parameters): array
    {
        $prepared = [];
        foreach ($parameters as $key => $value) {
            $prepared[sprintf('${parameters.%s}', $key)] = $value;
        }

        return $prepared;
    }

    /**
     * Layers the parameters over the service.
     *
     * @param array $services
     * @param array $parameters
     *
     * @return array
     */
    private function layerParameters(array $services, array $parameters): array
    {
        foreach ($services as &$service) {
            if (is_string($service)) {
                foreach ($parameters as $key => $parameter) {
                    if ($service === $key) {
                        $service = $parameter;
                    }
                }
            } elseif (is_array($service)) {
                $service = $this->layerParameters($service, $parameters);
            }
        }

        return $services;
    }
}
