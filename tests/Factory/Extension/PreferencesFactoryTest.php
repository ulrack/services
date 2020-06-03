<?php

/**
 * Copyright (C) GrizzIT, Inc. All rights reserved.
 * See LICENSE for license details.
 */

namespace Ulrack\Services\Tests\Factory\Extension;

use PHPUnit\Framework\TestCase;
use GrizzIt\Storage\Component\ObjectStorage;
use GrizzIt\ObjectFactory\Factory\ObjectFactory;
use GrizzIt\Validator\Common\ValidatorInterface;
use Ulrack\Services\Tests\Mock\Hook\FactoryHook;
use Ulrack\Services\Common\ServiceFactoryInterface;
use GrizzIt\Validator\Component\Logical\NotValidator;
use GrizzIt\Validator\Component\Logical\AlwaysValidator;
use Ulrack\Services\Factory\Extension\PreferencesFactory;
use Ulrack\Services\Exception\DefinitionNotFoundException;
use GrizzIt\ObjectFactory\Component\Analyser\ClassAnalyser;

/**
 * @coversDefaultClass Ulrack\Services\Factory\Extension\PreferencesFactory
 */
class PreferencesFactoryTest extends TestCase
{
    /**
     * Provides the getHooks method.
     *
     * @return array
     */
    public function getHooks(): array
    {
        $classAnalyser = new ClassAnalyser(new ObjectStorage());
        return [new FactoryHook(
            'preferences',
            [],
            [
                'preferences' => [
                    '*' => [
                        'preferences' => [
                            ValidatorInterface::class => AlwaysValidator::class
                        ]
                    ]
                ]
            ],
            [
                'object-factory' => new ObjectFactory($classAnalyser),
                'class-analyser' => $classAnalyser
            ]
        )];
    }

    /**
     * @return void
     *
     * @covers ::create
     * @covers ::registerService
     */
    public function testPreloadedService(): void
    {
        $classAnalyser = new ClassAnalyser(new ObjectStorage());
        $subject = new PreferencesFactory(
            $this->createMock(ServiceFactoryInterface::class),
            'preferences',
            [],
            [
                'preferences' => [
                    'definitions' => [
                        'foo' => [
                            'preference' => AlwaysValidator::class,
                            'for' => ValidatorInterface::class
                        ]
                    ]
                ]
            ],
            [$this, 'getHooks'],
            [
                'object-factory' => new ObjectFactory($classAnalyser),
                'class-analyser' => $classAnalyser
            ]
        );

        $result = [ValidatorInterface::class => NotValidator::class];
        $subject->registerService('bar', $result);

        $this->assertSame($result, $subject->create('preferences.bar'));
    }

    /**
     * @return void
     *
     * @covers ::create
     */
    public function testCreate(): void
    {
        $classAnalyser = new ClassAnalyser(new ObjectStorage());
        $subject = new PreferencesFactory(
            $this->createMock(ServiceFactoryInterface::class),
            'preferences',
            [],
            [
                'preferences' => [
                    'definitions' => [
                        'foo' => [
                            'preference' => AlwaysValidator::class,
                            'for' => ValidatorInterface::class
                        ]
                    ]
                ]
            ],
            [$this, 'getHooks'],
            [
                'object-factory' => new ObjectFactory($classAnalyser),
                'class-analyser' => $classAnalyser
            ]
        );

        $this->assertEquals(
            [ValidatorInterface::class => AlwaysValidator::class],
            $subject->create('preferences.foo')
        );

        $this->expectException(DefinitionNotFoundException::class);

        $subject->create('preferences.bar');
    }
}
