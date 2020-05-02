<?php

/**
 * Copyright (C) GrizzIT, Inc. All rights reserved.
 * See LICENSE for license details.
 */

namespace Ulrack\Services\Tests\Component\Compiler;

use PHPUnit\Framework\TestCase;
use GrizzIt\Storage\Component\ObjectStorage;
use GrizzIt\ObjectFactory\Factory\ObjectFactory;
use Ulrack\Services\Tests\Mock\Hook\CompilerHook;
use Ulrack\Services\Component\Compiler\ServiceCompiler;
use Ulrack\Services\Component\Registry\ServiceRegistry;
use GrizzIt\ObjectFactory\Component\Analyser\ClassAnalyser;
use Ulrack\Services\Component\Compiler\Extension\ParametersCompiler;

/**
 * @coversDefaultClass Ulrack\Services\Component\Compiler\ServiceCompiler
 */
class ServiceCompilerTest extends TestCase
{
    /**
     * @return void
     *
     * @covers ::addExtension
     * @covers ::addHook
     * @covers ::compile
     * @covers ::getHooks
     * @covers ::__construct
     */
    public function testCompile(): void
    {
        $storage = new ObjectStorage();
        $subject = new ServiceCompiler(
            new ServiceRegistry(),
            $storage,
            new ObjectFactory(
                new ClassAnalyser(
                    new ObjectStorage()
                )
            )
        );

        $subject->addExtension('foo', ParametersCompiler::class, 0);
        $subject->addHook('foo', CompilerHook::class, 0, []);

        $result = $subject->compile();
        $this->assertIsArray($result);
        // Check that it retrieves a cached version.
        $this->assertSame($result, $subject->compile());

        $storage->set(ServiceCompiler::STORAGE_COMPILED_KEY, false);

        // Check that it removes the compiled services and start recompilation.
        $this->assertSame($result, $subject->compile());
    }
}
