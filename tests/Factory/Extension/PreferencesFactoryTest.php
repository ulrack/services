<?php
/**
 * Copyright (C) Jyxon, Inc. All rights reserved.
 * See LICENSE for license details.
 */
namespace Ulrack\Services\Tests\Factory\Extension;

use PHPUnit\Framework\TestCase;
use Ulrack\Storage\Component\ObjectStorage;
use Ulrack\ObjectFactory\Factory\ObjectFactory;
use Ulrack\Validator\Common\ValidatorInterface;
use Ulrack\Services\Tests\Mock\Hook\FactoryHook;
use Ulrack\Validator\Component\Logical\AlwaysValidator;
use Ulrack\Services\Factory\Extension\PreferencesFactory;
use Ulrack\ObjectFactory\Component\Analyser\ClassAnalyser;
use Ulrack\Services\Exception\DefinitionNotFoundException;

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
     */
    public function testCreate(): void
    {
        $classAnalyser = new ClassAnalyser(new ObjectStorage());
        $subject = new PreferencesFactory(
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
