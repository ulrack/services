<?php
/**
 * Copyright (C) Jyxon, Inc. All rights reserved.
 * See LICENSE for license details.
 */
namespace Ulrack\Services\Component\Compiler\Extension;

use Ulrack\Services\Helper\ServiceCompilerHelper;
use Ulrack\Services\Common\AbstractServiceCompilerExtension;

class PreferencesCompiler extends AbstractServiceCompilerExtension
{
    const SERVICES_PARAMETER_KEY = 'service_keys';

    /**
     * Retrieve the service definitions.
     *
     * @return array
     */
    public function fetch(): array
    {
        $services = parent::fetch();

        return $this->postFetch(
            array_merge(
                ['definitions' => $services],
                $this->aggregatePreferences($services)
            ),
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

        $inputServices = $services;

        foreach ($this->getParameter(
            self::SERVICES_PARAMETER_KEY
        ) as $serviceName => $classNode) {
            foreach ($services[$serviceName] as $serviceKey => &$service) {
                if (isset($service[$classNode])) {
                    if (class_exists($service[$classNode])
                    || interface_exists($service[$classNode])) {
                        $scopes = ServiceCompilerHelper::getScopesFromServiceKey(
                            $serviceKey
                        );
    
                        $service[$classNode] = $this->resolveClassFromPreferences(
                            $service[$classNode],
                            $services[$this->getKey()],
                            $scopes
                        );
                    }
                }
            }
        }

        return $this->postCompile(
            $inputServices,
            $services,
            $this->getParameters()
        )['return'];
    }

    /**
     * Aggregates the preferences.
     * Groups them by scope and overwrites duplicates.
     *
     * @param array $preferences
     *
     * @return array
     */
    private function aggregatePreferences(array $preferences): array
    {
        $aggregatedPreferences = [];
        foreach ($preferences as $serviceKey => $preference) {
            if (class_exists($preference['preference'])
            || interface_exists($preference['preference'])) {
                if (class_exists($preference['for'])
                || interface_exists($preference['for'])) {
                    $scopes = ServiceCompilerHelper::getScopesFromServiceKey(
                        $serviceKey
                    );
                    
                    $aggregatedPreferences = ServiceCompilerHelper::mergeIntoArrayPath(
                        $aggregatedPreferences,
                        $scopes,
                        [
                            'preferences' => [
                                $preference['for'] => $preference['preference']
                            ]
                        ]
                    );
                }
            }
        }

        return $aggregatedPreferences;
    }

    /**
     * Resolves the preferences (if there are any) for the provided class.
     *
     * @param string $class
     * @param array $preferences
     * @param string[] $scopes
     *
     * @return string
     */
    private function resolveClassFromPreferences(
        string $class,
        array $preferences,
        array $scopes
    ): string {
        while (count($scopes) > 0) {
            $preference = $preferences;
            foreach ($scopes as $key => $scope) {
                if (!isset($preference[$scope])) {
                    for ($i = count($scopes); $i > $key; $i--) {
                        unset($scopes[$key]);
                    }
                }

                $preference = &$preference[$scope];
            }

            if (isset($preference['preferences'][$class])) {
                $class = $preference['preferences'][$class];

                continue;
            }

            array_pop($scopes);
        }

        return $class;
    }
}
