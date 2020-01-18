<?php
/**
 * Copyright (C) Jyxon, Inc. All rights reserved.
 * See LICENSE for license details.
 */
namespace Ulrack\Services\Tests\Component\Compiler\Extension;

use PHPUnit\Framework\TestCase;
use Ulrack\Services\Tests\Mock\Hook\CompilerHook;
use Ulrack\Services\Component\Registry\ServiceRegistry;
use Ulrack\Validator\Component\Logical\AlwaysValidator;
use Ulrack\Services\Component\Compiler\Extension\ServicesCompiler;

/**
 * @coversDefaultClass \Ulrack\Services\Component\Compiler\Extension\ServicesCompiler
 */
class ServicesCompilerTest extends TestCase
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
                'services',
                []
            )
        ];
    }

    /**
     * @return void
     *
     * @covers ::compile
     */
    public function testCompile(): void
    {
        $subject = new ServicesCompiler(
            new ServiceRegistry(),
            'services',
            new AlwaysValidator(true),
            [],
            [$this, 'getHooks']
        );

        $this->assertEquals(
            [
                'services' => [
                    'bar' => [
                        'class' => AlwaysValidator::class,
                        'parameters' => [
                            'alwaysBool' => true
                        ]
                    ],
                    'foo' => [
                        'class' => AlwaysValidator::class,
                        'abstract' => true
                    ]
                ]
            ],
            $subject->compile(
                [
                    'services' => [
                        'bar' => [
                            'parent' => 'foo',
                            'parameters' => [
                                'alwaysBool' => true
                            ]
                        ],
                        'foo' => [
                            'class' => AlwaysValidator::class,
                            'abstract' => true
                        ]
                    ]
                ]
            )
        );
    }
}
