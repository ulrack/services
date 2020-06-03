<?php

/**
 * Copyright (C) GrizzIT, Inc. All rights reserved.
 * See LICENSE for license details.
 */

namespace Ulrack\Services\Factory\Extension;

use Ulrack\Services\Exception\DefinitionNotFoundException;
use Ulrack\Services\Common\AbstractServiceFactoryExtension;

class PreferencesFactory extends AbstractServiceFactoryExtension
{
    /**
     * Contains the registered services.
     *
     * @var array
     */
    private $registeredServices = [];

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
        $this->registeredServices[$serviceKey] = $value;
    }

    /**
     * Retrieve the parameter from the services.
     *
     * @param string $serviceKey
     *
     * @return array
     *
     * @throws DefinitionNotFoundException When the requested preference can not be found.
     */
    public function create(string $serviceKey): array
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

        if (isset($this->registeredServices[$internalKey])) {
            return $this->postCreate(
                $serviceKey,
                $this->registeredServices[$internalKey],
                $this->getParameters()
            )['return'];
        }

        $preferences = $this->getServices()[$this->getKey()]['definitions'];

        if (isset($preferences[$internalKey])) {
            return $this->postCreate(
                $serviceKey,
                [
                    $preferences[$internalKey]['for'] => $preferences[$internalKey]['preference']
                ],
                $this->getParameters()
            )['return'];
        }

        throw new DefinitionNotFoundException($serviceKey);
    }
}
