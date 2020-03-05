<?php
/**
 * Copyright (C) GrizzIT, Inc. All rights reserved.
 * See LICENSE for license details.
 */
namespace Ulrack\Services\Tests\Factory\Extension;

use PHPUnit\Framework\TestCase;
use Ulrack\Storage\Component\ObjectStorage;
use Ulrack\ObjectFactory\Factory\ObjectFactory;
use Ulrack\Validator\Common\ValidatorInterface;
use Ulrack\Services\Tests\Mock\Hook\FactoryHook;
use Ulrack\Validator\Component\Chain\AndValidator;
use Ulrack\Validator\Component\Logical\NotValidator;
use Ulrack\Services\Factory\Extension\ServicesFactory;
use Ulrack\Validator\Component\Logical\AlwaysValidator;
use Ulrack\Services\Exception\MissingPreferenceException;
use Ulrack\ObjectFactory\Component\Analyser\ClassAnalyser;
use Ulrack\Services\Exception\DefinitionNotFoundException;
use Ulrack\Services\Exception\NonInstantiableServiceException;
use Ulrack\Services\Exception\InvalidArgumentException;

/**
 * @coversDefaultClass Ulrack\Services\Factory\Extension\ServicesFactory
 * @covers Ulrack\Services\Exception\NonInstantiableServiceException
 * @covers Ulrack\Services\Exception\MissingPreferenceException
 * @covers Ulrack\Services\Exception\InvalidArgumentException
 */
class ServicesFactoryTest extends TestCase
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
            'services',
            [],
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
            [
                'object-factory' => new ObjectFactory($classAnalyser),
                'class-analyser' => $classAnalyser
            ]
        )];
    }

    /**
     * @param array $services
     *
     * @return void
     *
     * @covers ::create
     * @covers ::resolveReference
     *
     * @dataProvider servicesProvider
     */
    public function testCreate(array $services): void
    {
        $classAnalyser = new ClassAnalyser(new ObjectStorage());
        $subject = new ServicesFactory(
            'services',
            [],
            $services,
            [$this, 'getHooks'],
            [
                'object-factory' => new ObjectFactory($classAnalyser),
                'class-analyser' => $classAnalyser
            ]
        );

        $this->assertInstanceOf(NotValidator::class, $subject->create('services.not-validator'));

        $this->expectException(DefinitionNotFoundException::class);

        $subject->create('services.foo');
    }

    /**
     * @param array $services
     *
     * @return void
     *
     * @covers ::create
     *
     * @dataProvider servicesProvider
     */
    public function testCreateAbstract(array $services): void
    {
        $classAnalyser = new ClassAnalyser(new ObjectStorage());
        $subject = new ServicesFactory(
            'services',
            [],
            $services,
            [$this, 'getHooks'],
            [
                'object-factory' => new ObjectFactory($classAnalyser),
                'class-analyser' => $classAnalyser
            ]
        );

        $this->expectException(NonInstantiableServiceException::class);

        $subject->create('services.validator.always-validator-true');
    }

    /**
     * @param array $services
     *
     * @return void
     *
     * @covers ::create
     *
     * @dataProvider servicesProvider
     */
    public function testCreateNotInstantiable(array $services): void
    {
        $classAnalyser = new ClassAnalyser(new ObjectStorage());
        $subject = new ServicesFactory(
            'services',
            [],
            $services,
            [$this, 'getHooks'],
            [
                'object-factory' => new ObjectFactory($classAnalyser),
                'class-analyser' => $classAnalyser
            ]
        );

        $this->expectException(MissingPreferenceException::class);

        $subject->create('validator.unresolved');
    }

    /**
     * @param array $services
     *
     * @return void
     *
     * @covers ::create
     *
     * @dataProvider servicesProvider
     */
    public function testCreateInvalidArguments(array $services): void
    {
        $classAnalyser = new ClassAnalyser(new ObjectStorage());
        $subject = new ServicesFactory(
            'services',
            [],
            $services,
            [$this, 'getHooks'],
            [
                'object-factory' => new ObjectFactory($classAnalyser),
                'class-analyser' => $classAnalyser
            ]
        );

        $this->expectException(InvalidArgumentException::class);

        $subject->create('validator.invalid');
    }

    /**
     * @return array
     */
    public function servicesProvider(): array
    {
        return [
            [
                [
                    'services' => [
                        'validator.always-validator-true'=> [
                            'class'=> AlwaysValidator::class,
                            'abstract' => true,
                            'parameters'=> [
                                'alwaysBool'=> true
                            ]
                        ],
                        'validator.always-validator-false'=> [
                            'class'=> AlwaysValidator::class,
                            'parameters'=> [
                                'alwaysBool'=> false
                            ]
                        ],
                        'validator.invalid'=> [
                            'class'=> AlwaysValidator::class,
                            'parameters'=> [
                                'alwaysValidate'=> false
                            ]
                        ],
                        'validator.unresolved'=> [
                            'class'=> ValidatorInterface::class
                        ],
                        'chain-validator'=> [
                            'class'=> AndValidator::class,
                            'parameters'=> [
                                'validators'=> [
                                    '@{validator.always-validator-false}'
                                ]
                            ]
                        ],
                        'not-validator'=> [
                            'class'=> NotValidator::class,
                            'parameters'=> [
                                'validator'=> '@{chain-validator}'
                            ]
                        ]
                    ]
                ]
            ]
        ];
    }
}
