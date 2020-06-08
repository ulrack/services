<?php

/**
 * Copyright (C) GrizzIT, Inc. All rights reserved.
 * See LICENSE for license details.
 */

namespace Ulrack\Services\Tests\Factory;

use PHPUnit\Framework\TestCase;
use Ulrack\Services\Factory\ServiceFactory;
use GrizzIt\Storage\Component\ObjectStorage;
use GrizzIt\ObjectFactory\Factory\ObjectFactory;
use Ulrack\Services\Tests\Mock\Hook\FactoryHook;
use Ulrack\Services\Common\ServiceCompilerInterface;
use Ulrack\Services\Exception\ServiceNotFoundException;
use Ulrack\Services\Factory\Extension\ParametersFactory;
use GrizzIt\ObjectFactory\Common\MethodReflectorInterface;
use GrizzIt\ObjectFactory\Component\Analyser\ClassAnalyser;
use Ulrack\Services\Common\ServiceFactoryExtensionInterface;

/**
 * @coversDefaultClass Ulrack\Services\Factory\ServiceFactory
 * @covers Ulrack\Services\Exception\ServiceNotFoundException
 */
class ServiceFactoryTest extends TestCase
{
    /**
     * @return void
     *
     * @covers ::__construct
     * @covers ::create
     * @covers ::addExtension
     * @covers ::addHook
     * @covers ::getHooks
     * @covers ::getExtension
     */
    public function testCreate(): void
    {
        $serviceCompiler = $this->createMock(ServiceCompilerInterface::class);
        $serviceCompiler->expects(static::once())
            ->method('compile')
            ->willReturn(
                ['parameters' => ['@{parameters.my-parameter}' => 'foo']]
            );

        $classAnalyser = new ClassAnalyser(new ObjectStorage());
        $subject = new ServiceFactory(
            $serviceCompiler,
            new ObjectFactory($classAnalyser),
            $classAnalyser,
            $this->createMock(MethodReflectorInterface::class)
        );

        $subject->addExtension('parameters', ParametersFactory::class, []);
        $subject->addHook('parameters', FactoryHook::class, 0, []);

        $this->assertEquals('foo', $subject->create('parameters.my-parameter'));

        $this->assertInstanceOf(
            ServiceFactoryExtensionInterface::class,
            $subject->getExtension('parameters')
        );

        $this->expectException(ServiceNotFoundException::class);
        $subject->create('not-parameters.my-parameter');
    }
}
