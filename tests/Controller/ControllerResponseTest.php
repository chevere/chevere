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

use Chevere\Components\Controller\ControllerResponseFailure;
use Chevere\Components\Controller\ControllerResponseProvisional;
use Chevere\Components\Controller\ControllerResponseSuccess;
use PHPUnit\Framework\TestCase;

final class ControllerResponseTest extends TestCase
{
    public function testConstructSuccess(): void
    {
        $data = ['data'];
        $controllerResponse = new ControllerResponseSuccess($data);
        $this->assertSame($data, $controllerResponse->data());
    }

    public function testSuccessWithData(): void
    {
        $controllerResponse = new ControllerResponseSuccess([]);
        $this->assertSame([], $controllerResponse->data());
        $data = ['data'];
        $controllerResponse = $controllerResponse->withData($data);
        $this->assertSame($data, $controllerResponse->data());
    }

    public function testConstructFailure(): void
    {
        $data = ['data'];
        $controllerResponse = new ControllerResponseFailure($data);
        $this->assertSame($data, $controllerResponse->data());
    }

    public function testFailureWithData(): void
    {
        $controllerResponse = new ControllerResponseFailure([]);
        $this->assertSame([], $controllerResponse->data());
        $data = ['data'];
        $controllerResponse = $controllerResponse->withData($data);
        $this->assertSame($data, $controllerResponse->data());
    }

    public function testConstructProvisional(): void
    {
        $data = ['data'];
        $controllerResponse = new ControllerResponseProvisional($data);
        $this->assertSame($data, $controllerResponse->data());
    }

    public function testProvisionalWithData(): void
    {
        $controllerResponse = new ControllerResponseProvisional([]);
        $this->assertSame([], $controllerResponse->data());
        $data = ['data'];
        $controllerResponse = $controllerResponse->withData($data);
        $this->assertSame($data, $controllerResponse->data());
    }
}
