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
    public function testConstruct(): void
    {
        $array = [
            'id' => '1',
            'name' => 'some-value',
        ];
        $controllerArguments = new ControllerArguments;
        foreach ($array as $name => $value) {
            $controllerArguments->put($name, $value);
            $this->assertTrue($controllerArguments->has($name));
            $this->assertSame($value, $controllerArguments->get($name));
        }
        $notFoundKey = '404';
        $this->assertFalse($controllerArguments->has($notFoundKey));
        $this->expectException(OutOfBoundsException::class);
        $controllerArguments->get($notFoundKey);
    }
}
