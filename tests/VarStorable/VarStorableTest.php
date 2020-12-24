<?php

/*
 * This file is part of Chevere.
 *
 * (c) Rodolfo Berrios <rodolfo@chevere.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Chevere\Tests\VarExportable;

use Chevere\Components\VarStorable\VarStorable;
use Chevere\Exceptions\VarStorable\VarStorableException;
use PHPUnit\Framework\TestCase;
use stdClass;

final class VarStorableTest extends TestCase
{
    public function testNotExportable(): void
    {
        $this->expectException(VarStorableException::class);
        $resource = fopen(__FILE__, 'r');
        if (is_resource($resource) === false) {
            $this->markTestIncomplete('Unable to open ' . __FILE__);
        }
        /** @var resource $resource */
        new VarStorable($resource);
        fclose($resource);
    }

    public function testContainsNotExportable(): void
    {
        $object = new stdClass();
        $resource = fopen(__FILE__, 'r');
        if (is_resource($resource) === false) {
            $this->markTestIncomplete('Unable to open ' . __FILE__);
        }
        $object->array = [1, 2, 3, $resource];
        $this->expectException(VarStorableException::class);
        new VarStorable($object);
        /** @var resource $resource */
        fclose($resource);
    }

    public function testConstruct(): void
    {
        foreach ([
            1,
            1.1,
            true,
            'test',
            [1, 2, 3],
            [1, 1.1, true, 'test'],
            [[1, 1.1, true, 'test']],
            new stdClass(),
            ['test', [1, false], new stdClass()],
        ] as $val) {
            $varStorable = new VarStorable($val);
            $this->assertSame($val, $varStorable->var());
            $this->assertSame(serialize($val), $varStorable->toSerialize());
            $this->assertSame(var_export($val, true), $varStorable->toExport());
        }
    }
}
