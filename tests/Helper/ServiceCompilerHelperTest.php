<?php

/**
 * Copyright (C) GrizzIT, Inc. All rights reserved.
 * See LICENSE for license details.
 */

namespace Ulrack\Services\Tests\Helper;

use PHPUnit\Framework\TestCase;
use Ulrack\Services\Helper\ServiceCompilerHelper;

/**
 * @coversDefaultClass Ulrack\Services\Helper\ServiceCompilerHelper
 */
class ServiceCompilerHelperTest extends TestCase
{
    /**
     * @return void
     *
     * @covers ::getScopesFromServiceKey
     *
     * @dataProvider scopesProvider
     */
    public function testGetScopesFromServiceKey(string $scope, array $expected): void
    {
        $this->assertEquals(
            $expected,
            ServiceCompilerHelper::getScopesFromServiceKey($scope)
        );
    }

    /**
     * @return void
     *
     * @covers ::mergeIntoArrayPath
     *
     * @dataProvider mergeProvider
     */
    public function testMergeIntoArrayPath(array $path, array $expected): void
    {
        $this->assertEquals(
            $expected,
            ServiceCompilerHelper::mergeIntoArrayPath([], $path, ['foo' => 'bar'])
        );
    }

    /**
     * @return array
     */
    public function scopesProvider(): array
    {
        return [
            [
                'foo',
                ['*']
            ],
            [
                'foo.bar',
                ['*', 'foo']
            ],
            [
                'foo.bar.baz',
                ['*', 'foo', 'bar']
            ]
        ];
    }

    /**
     * @return array
     */
    public function mergeProvider(): array
    {
        return [
            [
                ['*'],
                ['*' => ['foo' => 'bar']]
            ],
            [
                ['*', 'foo'],
                ['*' => ['foo' => ['foo' => 'bar']]]
            ],
            [
                ['*', 'foo', 'bar'],
                ['*' => ['foo' => ['bar' => ['foo' => 'bar']]]]
            ]
        ];
    }
}
