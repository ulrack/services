<?php

/**
 * Copyright (C) GrizzIT, Inc. All rights reserved.
 * See LICENSE for license details.
 */

namespace Ulrack\Services\Tests\Component\Compiler\Extension;

use PHPUnit\Framework\TestCase;
use Ulrack\Services\Tests\Mock\Hook\CompilerHook;
use Ulrack\Services\Common\ServiceRegistryInterface;
use Ulrack\Services\Component\Registry\ServiceRegistry;
use GrizzIt\Validator\Component\Logical\AlwaysValidator;
use Ulrack\Services\Component\Compiler\Extension\ParametersCompiler;

/**
 * @coversDefaultClass Ulrack\Services\Component\Compiler\Extension\ParametersCompiler
 * @covers Ulrack\Services\Common\AbstractServiceCompilerExtension
 * @covers Ulrack\Services\Common\Hook\AbstractServiceCompilerHook
 */
class ParametersCompilerTest extends TestCase
{
    /**
     * Provides the get hooks method and tests the remaing methods in AbstractServiceCompilerHook.
     *
     * @return array
     */
    public function getHooks(): array
    {
        $compilerHook = new CompilerHook(
            new ServiceRegistry(),
            'parameters',
            ['foo' => 'bar']
        );

        $this->assertInstanceOf(
            ServiceRegistryInterface::class,
            $compilerHook->getRegistry()
        );

        $this->assertEquals(
            'parameters',
            $compilerHook->getKey()
        );

        $this->assertEquals(
            'bar',
            $compilerHook->getParameter('foo')
        );

        $this->assertEquals(
            ['foo' => 'bar'],
            $compilerHook->getParameters()
        );

        return [$compilerHook];
    }

    /**
     * @return void
     *
     * @covers ::fetch
     * @covers ::prepareParameters
     */
    public function testFetch(): void
    {
        $serviceRegistry = new ServiceRegistry();
        $serviceRegistry->add('parameters', 'my-parameter', 'foo');
        $subject = new ParametersCompiler(
            $serviceRegistry,
            'parameters',
            new AlwaysValidator(true),
            [],
            [$this, 'getHooks']
        );

        $this->assertEquals(['@{parameters.my-parameter}' => 'foo'], $subject->fetch());
    }

    /**
     * @return void
     *
     * @covers ::compile
     * @covers ::layerParameters
     */
    public function testCompile(): void
    {
        $serviceRegistry = new ServiceRegistry();
        $serviceRegistry->add('parameters', 'my-parameter', 'foo');
        $subject = new ParametersCompiler(
            $serviceRegistry,
            'parameters',
            new AlwaysValidator(true),
            [],
            [$this, 'getHooks']
        );

        $result = $subject->compile(
            [
                'parameters' => ['@{parameters.my-parameter}' => 'foo'],
                'not-parameters' => [
                    ['foo' => '@{parameters.my-parameter}']
                ]
            ]
        );

        $this->assertEquals(
            [
                'parameters' => ['@{parameters.my-parameter}' => 'foo'],
                'not-parameters' => [
                    ['foo' => 'foo']
                ]
            ],
            $result
        );
    }

    /**
     * @return void
     */
    public function testAbstract(): void
    {
        $serviceRegistry = new ServiceRegistry();
        $serviceRegistry->add('parameters', 'my-parameter', 'foo');
        $subject = new ParametersCompiler(
            $serviceRegistry,
            'parameters',
            new AlwaysValidator(false),
            ['foo' => 'bar'],
            [$this, 'getHooks']
        );

        $this->assertEquals([], $subject->getServices());
        $this->assertEquals($serviceRegistry, $subject->getRegistry());
        $this->assertEquals('bar', $subject->getParameter('foo'));
    }
}
