<?php

/**
 * Copyright (C) GrizzIT, Inc. All rights reserved.
 * See LICENSE for license details.
 */

namespace Ulrack\Services\Tests\Component\Compiler\Extension;

use PHPUnit\Framework\TestCase;
use Ulrack\Services\Common\ServiceRegistryInterface;
use GrizzIt\Validator\Component\Logical\AlwaysValidator;
use Ulrack\Services\Component\Compiler\Extension\PassThroughCompiler;

/**
 * @coversDefaultClass \Ulrack\Services\Component\Compiler\Extension\PassThroughCompiler
 */
class PassThroughCompilerTest extends TestCase
{
    /**
     * @return void
     *
     * @covers ::compile
     */
    public function testCompiler(): void
    {
        $services = [
            'my-service' => [
                'foo' => [
                    'my-default' => true
                ]
            ]
        ];

        $subject = new PassThroughCompiler(
            $this->createMock(ServiceRegistryInterface::class),
            'my-service',
            new AlwaysValidator(true),
            [],
            [$this, 'getHooks']
        );

        $this->assertEquals($services, $subject->compile($services));
    }

    /**
     * Required method.
     *
     * @return array
     */
    public function getHooks(): array
    {
        return [];
    }
}
