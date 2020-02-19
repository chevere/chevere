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

namespace Chevere\Components\Variable\Tests;

use Chevere\Components\Variable\Exceptions\VariableNotExportableException;
use Chevere\Components\Variable\VariableExport;
use PHPUnit\Framework\TestCase;
use stdClass;

final class VariableExportTest extends TestCase
{
    public function testNotExportable(): void
    {
        $this->expectException(VariableNotExportableException::class);
        $resource = fopen(__FILE__, 'r');
        if (is_resource($resource) === false) {
            $this->markTestIncomplete('Unable to fopen ' . __FILE__);
        }
        new VariableExport($resource);
        if (is_resource($resource)) {
            fclose($resource);
        }
    }

    public function testContainsNotExportable(): void
    {
        $object = new stdClass();
        $resource = fopen(__FILE__, 'r');
        if (is_resource($resource) === false) {
            $this->markTestIncomplete('Unable to fopen ' . __FILE__);
        }
        $object->array = [1, 2, 3, $resource];
        $this->expectException(VariableNotExportableException::class);
        new VariableExport($object);
        if (is_resource($resource)) {
            fclose($resource);
        }
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
            $variableExport = new VariableExport($val);
            $this->assertSame($val, $variableExport->var());
            $this->assertSame(serialize($val), $variableExport->toSerialize());
            $this->assertSame(var_export($val, true), $variableExport->toExport());
        }
    }
}
