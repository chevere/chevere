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
use LogicException;
use OutOfBoundsException;
use PHPUnit\Framework\TestCase;
use stdClass;

final class ControllerArgumentsTest extends TestCase
{
    public function testsIntegerKey(): void
    {
        $this->expectException(LogicException::class);
        new ControllerArguments([1 => 'value']);
    }

    public function testsIntegerValue(): void
    {
        $this->expectException(LogicException::class);
        new ControllerArguments(['name' => 1]);
    }

    public function testConstruct(): void
    {
        $array = [
            'id' => '1',
            'name' => 'some-name',
        ];
        $controllerArguments = new ControllerArguments($array);
        foreach ($array as $key => $value) {
            $this->assertTrue($controllerArguments->hasKey($key));
            $this->assertSame($value, $controllerArguments->get($key));
        }
        $notFoundKey = '404';
        $this->assertFalse($controllerArguments->hasKey($notFoundKey));
        $this->expectException(OutOfBoundsException::class);
        $controllerArguments->get($notFoundKey);
    }
}
