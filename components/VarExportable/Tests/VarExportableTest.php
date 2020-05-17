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

namespace Chevere\Components\VarExportable\Tests;

use Chevere\Components\VarExportable\Exceptions\VarNotExportableException;
use Chevere\Components\VarExportable\VarExportable;
use PHPUnit\Framework\TestCase;
use stdClass;

final class VarExportableTest extends TestCase
{
    public function testNotExportable(): void
    {
        $this->expectException(VarNotExportableException::class);
        $resource = fopen(__FILE__, 'r');
        if (is_resource($resource) === false) {
            $this->markTestIncomplete('Unable to open ' . __FILE__);
        }
        /** @var resource $resource */
        new VarExportable($resource);
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
        $this->expectException(VarNotExportableException::class);
        new VarExportable($object);
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
            $varExportable = new VarExportable($val);
            $this->assertSame($val, $varExportable->var());
            $this->assertSame(serialize($val), $varExportable->toSerialize());
            $this->assertSame(var_export($val, true), $varExportable->toExport());
        }
    }
}
