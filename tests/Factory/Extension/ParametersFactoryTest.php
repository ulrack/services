<?php
/**
 * Copyright (C) Jyxon, Inc. All rights reserved.
 * See LICENSE for license details.
 */
namespace Ulrack\Services\Tests\Factory\Extension;

use PHPUnit\Framework\TestCase;
use Ulrack\Storage\Component\ObjectStorage;
use Ulrack\ObjectFactory\Factory\ObjectFactory;
use Ulrack\Services\Tests\Mock\Hook\FactoryHook;
use Ulrack\Services\Factory\Extension\ParametersFactory;
use Ulrack\ObjectFactory\Component\Analyser\ClassAnalyser;
use Ulrack\Services\Exception\DefinitionNotFoundException;

/**
 * @coversDefaultClass Ulrack\Services\Factory\Extension\ParametersFactory
 * @covers Ulrack\Services\Common\AbstractServiceFactoryExtension
 * @covers Ulrack\Services\Common\Hook\AbstractServiceFactoryHook
 * @covers Ulrack\Services\Exception\DefinitionNotFoundException
 */
class ParametersFactoryTest extends TestCase
{
    /**
     * Provides the getHooks method and tests the remaining methods in the AbstractServiceFactoryHook.
     *
     * @return array
     */
    public function getHooks(): array
    {
        $classAnalyser = new ClassAnalyser(new ObjectStorage());
        $services = [
            'parameters' => [
                '${parameters.my-parameter}' => 'foo'
            ]
        ];

        $factoryHook = new FactoryHook(
            'parameters',
            ['foo' => 'bar'],
            $services,
            [
                'object-factory' => new ObjectFactory($classAnalyser),
                'class-analyser' => $classAnalyser
            ]
        );

        $this->assertEquals($classAnalyser, $factoryHook->getInternalService('class-analyser'));
        $this->assertEquals('bar', $factoryHook->getParameter('foo'));
        $this->assertEquals(['foo' => 'bar'], $factoryHook->getParameters());
        $this->assertEquals('parameters', $factoryHook->getKey());
        $this->assertEquals($services, $factoryHook->getServices());

        return [$factoryHook];
    }

    /**
     * @return void
     *
     * @covers ::create
     */
    public function testCreate(): void
    {
        $classAnalyser = new ClassAnalyser(new ObjectStorage());
        $subject = new ParametersFactory(
            'parameters',
            ['foo' => 'bar'],
            [
                'parameters' => [
                    '${parameters.my-parameter}' => 'foo'
                ]
            ],
            [$this, 'getHooks'],
            [
                'object-factory' => new ObjectFactory($classAnalyser),
                'class-analyser' => $classAnalyser
            ]
        );

        $this->assertEquals('foo', $subject->create('parameters.my-parameter'));

        $this->assertEquals($classAnalyser, $subject->getInternalService('class-analyser'));
        $this->assertEquals('bar', $subject->getParameter('foo'));

        $this->expectException(DefinitionNotFoundException::class);

        $subject->create('not-parameters.my-parameter');
    }
}
