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

namespace Chevere\Tests\Controller;

use Chevere\Components\Controller\ControllerResponse;
use PHPUnit\Framework\TestCase;

final class ControllerResponseTest extends TestCase
{
    public function testConstructSuccess(): void
    {
        $controllerResponse = new ControllerResponse(true, []);
        $this->assertTrue($controllerResponse->isSuccess());
    }

    public function testConstructFailure(): void
    {
        $controllerResponse = new ControllerResponse(false, []);
        $this->assertFalse($controllerResponse->isSuccess());
    }

    public function testWithData(): void
    {
        $data = ['The data'];
        $controllerResponse = new ControllerResponse(true, []);
        $this->assertSame([], $controllerResponse->data());
        $controllerResponse = $controllerResponse->withData($data);
        $this->assertSame($data, $controllerResponse->data());
    }
}
