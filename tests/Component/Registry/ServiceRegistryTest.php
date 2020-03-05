<?php
/**
 * Copyright (C) GrizzIT, Inc. All rights reserved.
 * See LICENSE for license details.
 */
namespace Ulrack\Services\Tests\Component\Registry;

use PHPUnit\Framework\TestCase;
use Ulrack\Services\Component\Registry\ServiceRegistry;

/**
 * @coversDefaultClass Ulrack\Services\Component\Registry\ServiceRegistry
 */
class ServiceRegistryTest extends TestCase
{
    /**
     * @return void
     *
     * @covers ::add
     * @covers ::get
     * @covers ::getKeys
     */
    public function testRegistry(): void
    {
        $subject = new ServiceRegistry();
        $this->assertEquals([], $subject->getKeys());
        $this->assertEquals([], $subject->get('foo'));
        $subject->add('foo', 'bar', 'baz');
        $this->assertEquals(['foo'], $subject->getKeys());
        $this->assertEquals(['bar' => 'baz'], $subject->get('foo'));
    }
}
