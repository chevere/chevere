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

namespace Chevere\Tests\Variable;

use Chevere\Components\Variable\Exceptions\VariableExportableException;
use Chevere\Components\Variable\VariableExportable;
use PHPUnit\Framework\TestCase;
use stdClass;

final class VariableExportableTest extends TestCase
{
    public function testCreateNotExportable(): void
    {
        $this->expectException(VariableExportableException::class);
        new VariableExportable(fopen(__FILE__, 'r'));
    }

    public function testCreateContainsNotExportable(): void
    {
        $object = new stdClass();
        $object->array = [1, 2, 3, fopen(__FILE__, 'r')];
        $this->expectException(VariableExportableException::class);
        new VariableExportable($object);
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
            $variableExportable = new VariableExportable($val);
            $this->assertSame($val, $variableExportable->var());
        }
    }
}
