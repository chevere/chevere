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

use Chevere\Components\Controller\ControllerRan;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class ControllerRanTest extends TestCase
{
    public function testConstructInvalidArgumentMin(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new ControllerRan(-1, []);
    }

    public function testConstructInvalidArgumentMax(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new ControllerRan(255, []);
    }

    public function testConstruct(): void
    {
        $code = (int) range(1, 254);
        $data = ['The data'];
        $controllerRan = new ControllerRan($code, $data);
        $this->assertSame($code, $controllerRan->code());
        $this->assertSame($data, $controllerRan->data());
    }
}
