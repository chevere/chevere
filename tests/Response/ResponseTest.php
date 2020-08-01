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

namespace Chevere\Tests\Response;

use Chevere\Components\Response\ResponseFailure;
use Chevere\Components\Response\ResponseProvisional;
use Chevere\Components\Response\ResponseSuccess;
use PHPUnit\Framework\TestCase;

final class ResponseTest extends TestCase
{
    public function testConstructSuccess(): void
    {
        $data = ['data'];
        $controllerResponse = new ResponseSuccess($data);
        $this->assertSame($data, $controllerResponse->data());
    }

    public function testSuccessWithData(): void
    {
        $controllerResponse = new ResponseSuccess([]);
        $this->assertSame([], $controllerResponse->data());
        $data = ['data'];
        $controllerResponse = $controllerResponse->withData($data);
        $this->assertSame($data, $controllerResponse->data());
    }

    public function testConstructFailure(): void
    {
        $data = ['data'];
        $controllerResponse = new ResponseFailure($data);
        $this->assertSame($data, $controllerResponse->data());
    }

    public function testFailureWithData(): void
    {
        $controllerResponse = new ResponseFailure([]);
        $this->assertSame([], $controllerResponse->data());
        $data = ['data'];
        $controllerResponse = $controllerResponse->withData($data);
        $this->assertSame($data, $controllerResponse->data());
    }

    public function testConstructProvisional(): void
    {
        $data = ['data'];
        $controllerResponse = new ResponseProvisional($data);
        $this->assertSame($data, $controllerResponse->data());
    }

    public function testProvisionalWithData(): void
    {
        $controllerResponse = new ResponseProvisional([]);
        $this->assertSame([], $controllerResponse->data());
        $data = ['data'];
        $controllerResponse = $controllerResponse->withData($data);
        $this->assertSame($data, $controllerResponse->data());
    }
}
