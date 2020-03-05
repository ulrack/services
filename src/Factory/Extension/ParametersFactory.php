<?php
/**
 * Copyright (C) GrizzIT, Inc. All rights reserved.
 * See LICENSE for license details.
 */
namespace Ulrack\Services\Factory\Extension;

use Ulrack\Services\Exception\DefinitionNotFoundException;
use Ulrack\Services\Common\AbstractServiceFactoryExtension;

class ParametersFactory extends AbstractServiceFactoryExtension
{
    /**
     * Retrieve the parameter from the services.
     *
     * @param string $serviceKey
     *
     * @return mixed
     *
     * @throws DefinitionNotFoundException When the parameter can not be found.
     */
    public function create(string $serviceKey)
    {
        $serviceKey = $this->preCreate(
            $serviceKey,
            $this->getParameters()
        )['serviceKey'];

        $originalServiceKey = $serviceKey;
        $parameters = $this->getServices()[$this->getKey()];
        $serviceKey = sprintf('${%s}', $serviceKey);

        if (isset($parameters[$serviceKey])) {
            return $this->postCreate(
                $serviceKey,
                $parameters[$serviceKey],
                $this->getParameters()
            )['return'];
        }

        throw new DefinitionNotFoundException($originalServiceKey);
    }
}
