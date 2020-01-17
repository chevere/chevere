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

namespace Chevere\Components\Serialize\Tests;

use stdClass;
use Chevere\Components\Serialize\Serialize;
use Chevere\Components\Serialize\Unserialize;
use Chevere\Components\Variable\VariableExport;
use Chevere\Components\Type\Interfaces\TypeInterface;
use PHPUnit\Framework\TestCase;

final class SerializeTest extends TestCase
{
    public function testConstruct(): void
    {
        foreach ([
            TypeInterface::BOOLEAN => true,
            TypeInterface::INTEGER => 1,
            TypeInterface::FLOAT => 13.13,
            TypeInterface::STRING => 'test',
            TypeInterface::ARRAY => ['test'],
            TypeInterface::OBJECT => new stdClass(),
            TypeInterface::CALLABLE => 'phpinfo',
            TypeInterface::ITERABLE => [4, 2, 1, 3],
            TypeInterface::NULL => null,
        ] as $k => $v) {
            $serialize = new Serialize(
                new VariableExport($v)
            );
            $unserialize = new Unserialize($serialize->toString());
            if (TypeInterface::OBJECT == $k) {
                $this->assertEquals($v, $unserialize->var());
            } else {
                $this->assertSame($v, $unserialize->var());
            }
        }
    }
}
