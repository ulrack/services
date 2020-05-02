<?php

/**
 * Copyright (C) GrizzIT, Inc. All rights reserved.
 * See LICENSE for license details.
 */

namespace Ulrack\Services\Component\Compiler\Extension;

use Ulrack\Services\Common\AbstractServiceCompilerExtension;

class ServicesCompiler extends AbstractServiceCompilerExtension
{
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

        $inputServices = $services;

        $newServices = [];
        $resolve = $services[$this->getKey()];
        while (count($resolve) > 0) {
            foreach ($resolve as $serviceKey => $service) {
                if (isset($service['class'])) {
                    $newServices[$serviceKey] = $service;
                } elseif (array_key_exists($service['parent'], $newServices)) {
                    $newService = $newServices[$service['parent']];
                    unset($service['parent']);

                    if (isset($newService['abstract'])) {
                        unset($newService['abstract']);
                    }

                    $newServices[$serviceKey] = array_replace_recursive(
                        $newService,
                        $service
                    );
                } elseif (array_key_exists($service['parent'], $resolve)) {
                    // Step over the unset, because something else needs to be resolved first.
                    continue;
                }

                unset($resolve[$serviceKey]);
            }
        }

        $services[$this->getKey()] = $newServices;

        return $this->postCompile(
            $inputServices,
            $services,
            $this->getParameters()
        )['return'];
    }
}
