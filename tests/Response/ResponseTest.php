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
use Ramsey\Uuid\Rfc4122\Validator;

final class ResponseTest extends TestCase
{
    public function testConstructSuccess(): void
    {
        $data = ['data'];
        $response = new ResponseSuccess($data);
        $this->assertSame($data, $response->data());
        $this->assertTrue(
            (new Validator())->validate($response->uuid()),
            'Invalid UUID'
        );
        $this->assertIsString($response->token());
    }

    public function testSuccessWithData(): void
    {
        $response = new ResponseSuccess([]);
        $this->assertSame([], $response->data());
        $data = ['data'];
        $response = $response->withData($data);
        $this->assertSame($data, $response->data());
    }

    public function testConstructFailure(): void
    {
        $data = ['data'];
        $response = new ResponseFailure($data);
        $this->assertSame($data, $response->data());
    }

    public function testFailureWithData(): void
    {
        $response = new ResponseFailure([]);
        $this->assertSame([], $response->data());
        $data = ['data'];
        $response = $response->withData($data);
        $this->assertSame($data, $response->data());
    }

    public function testConstructProvisional(): void
    {
        $data = ['data'];
        $response = new ResponseProvisional($data);
        $this->assertSame($data, $response->data());
    }

    public function testProvisionalWithData(): void
    {
        $response = new ResponseProvisional([]);
        $this->assertSame([], $response->data());
        $data = ['data'];
        $response = $response->withData($data);
        $this->assertSame($data, $response->data());
    }
}
