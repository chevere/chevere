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

namespace Chevere\Components\Controller\Tests;

use Chevere\Components\Controller\ControllerArguments;
use Chevere\Components\Str\Exceptions\StrCtypeDigitException;
use Chevere\Components\Str\Exceptions\StrCtypeSpaceException;
use Chevere\Components\Str\Exceptions\StrEmptyException;
use LogicException;
use PHPUnit\Framework\TestCase;
use stdClass;

final class ControllerArgumentsTest extends TestCase
{
    public function testsIntegerKey(): void
    {
        $this->expectException(LogicException::class);
        new ControllerArguments([1 => 'value']);
    }

    public function testsFloatKey(): void
    {
        $this->expectException(LogicException::class);
        new ControllerArguments([1.1 => 'value']);
    }

    public function testConstruct(): void
    {
        $array = [
            'id' => '1',
            'array' => [0, 1, 2],
            'object' => new stdClass,
        ];
        $controllerArguments = new ControllerArguments($array);
        foreach ($array as $key => $value) {
            $this->assertTrue($controllerArguments->hasKey($key));
            $this->assertSame($value, $controllerArguments->get($key));
        }
    }
}
