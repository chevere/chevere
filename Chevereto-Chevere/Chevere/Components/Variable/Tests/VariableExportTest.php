<?php

/*
 * This file is part of Chevere.
 *
 * (c) Rodolfo Berrios <rodolfo@chevereto.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Chevere\Components\Variable\Tests;

use Chevere\Components\Variable\Exceptions\VariableExportException;
use Chevere\Components\Variable\VariableExport;
use PHPUnit\Framework\TestCase;
use stdClass;

final class VariableExportTest extends TestCase
{
    public function testCreateNotExportable(): void
    {
        $this->expectException(VariableExportException::class);
        new VariableExport(fopen(__FILE__, 'r'));
    }

    public function testCreateContainsNotExportable(): void
    {
        $object = new stdClass();
        $object->array = [1, 2, 3, fopen(__FILE__, 'r')];
        $this->expectException(VariableExportException::class);
        new VariableExport($object);
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
        }
    }
}
