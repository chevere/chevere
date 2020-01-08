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

namespace Chevere\Tests\Serialize;

use Chevere\Components\Serialize\Serialize;
use Chevere\Components\Serialize\Unserialize;
use Chevere\Components\Variable\VariableExport;
use Chevere\Components\Type\Contracts\TypeContract;
use PHPUnit\Framework\TestCase;
use stdClass;

final class SerializeTest extends TestCase
{
    public function testConstruct(): void
    {
        foreach ([
            TypeContract::BOOLEAN => true,
            TypeContract::INTEGER => 1,
            TypeContract::FLOAT => 13.13,
            TypeContract::STRING => 'test',
            TypeContract::ARRAY => ['test'],
            TypeContract::OBJECT => new stdClass(),
            TypeContract::CALLABLE => 'phpinfo',
            TypeContract::ITERABLE => [4, 2, 1, 3],
            TypeContract::NULL => null,
        ] as $k => $v) {
            $serialize = new Serialize(
                new VariableExport($v)
            );
            $unserialize = new Unserialize($serialize->toString());
            if (TypeContract::OBJECT == $k) {
                $this->assertEquals($v, $unserialize->var());
            } else {
                $this->assertSame($v, $unserialize->var());
            }
        }
    }
}
