<?php

/**
 * Copyright (C) GrizzIT, Inc. All rights reserved.
 * See LICENSE for license details.
 */

namespace Ulrack\Services\Tests\Component\Compiler\Extension;

use PHPUnit\Framework\TestCase;
use Ulrack\Services\Tests\Mock\Hook\CompilerHook;
use Ulrack\Services\Component\Registry\ServiceRegistry;
use GrizzIt\Validator\Component\Logical\AlwaysValidator;
use Ulrack\Services\Common\ServiceCompilerExtensionInterface;
use Ulrack\Services\Component\Compiler\Extension\PreferencesCompiler;

/**
 * @coversDefaultClass Ulrack\Services\Component\Compiler\Extension\PreferencesCompiler
 */
class PreferencesCompilerTest extends TestCase
{
    /**
     * Provides the get hooks method.
     *
     * @return array
     */
    public function getHooks(): array
    {
        return [
            new CompilerHook(
                new ServiceRegistry(),
                'preferences',
                []
            )
        ];
    }

    /**
     * @return void
     *
     * @covers ::fetch
     * @covers ::aggregatePreferences
     */
    public function testFetch(): void
    {
        $serviceRegistry = new ServiceRegistry();
        $serviceRegistry->add('preferences', 'foo', [
            'preference' => PreferencesCompiler::class,
            'for' => ServiceCompilerExtensionInterface::class
        ]);

        $subject = new PreferencesCompiler(
            $serviceRegistry,
            'preferences',
            new AlwaysValidator(true),
            [],
            [$this, 'getHooks']
        );

        $this->assertEquals([
            'definitions' => [
                'foo' => [
                    'preference' => PreferencesCompiler::class,
                    'for' => ServiceCompilerExtensionInterface::class
                ]
            ],
            '*' => [
                'preferences' => [
                    ServiceCompilerExtensionInterface::class => PreferencesCompiler::class
                ]
            ]
        ], $subject->fetch());
    }

    /**
     * @return void
     *
     * @covers ::compile
     * @covers ::resolveClassFromPreferences
     */
    public function testCompile(): void
    {
        $serviceRegistry = new ServiceRegistry();
        $serviceRegistry->add('preferences', 'foo', [
            'preference' => PreferencesCompiler::class,
            'for' => ServiceCompilerExtensionInterface::class
        ]);

        $subject = new PreferencesCompiler(
            $serviceRegistry,
            'preferences',
            new AlwaysValidator(true),
            ['service_keys' => ['services' => 'class']],
            [$this, 'getHooks']
        );

        $this->assertIsArray($subject->compile(
            [
                'preferences' => [
                    'definitions' => [
                        'foo.qux' => [
                            'preference' => PreferencesCompiler::class,
                            'for' => ServiceCompilerExtensionInterface::class
                        ]
                    ],
                    '*' => [
                        'foo' => [
                            'preferences' => [
                                ServiceCompilerExtensionInterface::class => PreferencesCompiler::class
                            ]
                        ]
                    ]
                ],
                'services' => [
                    'foo.bar.baz.qux' => [
                        'class' => ServiceCompilerExtensionInterface::class
                    ]
                ]
            ]
        ));
    }
}
