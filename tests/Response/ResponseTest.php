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

use Chevere\Response\Response;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Rfc4122\Validator;

final class ResponseTest extends TestCase
{
    public function testConstruct(): void
    {
        $response = new Response();
        $this->assertSame([], $response->data());
        $this->assertSame(0, $response->code());
        $this->assertTrue(
            (new Validator())->validate($response->uuid()),
            'Invalid UUID'
        );
        $this->assertIsString($response->token());
    }

    public function testWithData(): void
    {
        $response = new Response();
        $this->assertSame([], $response->data());
        $data = ['data'];
        $response = new Response(...$data);
        $this->assertSame($data, $response->data());
    }

    public function testWithStatus(): void
    {
        $data = ['data'];
        $code = 123;
        $response = (new Response())
            ->withData(...$data)
            ->withCode($code);
        $this->assertSame($data, $response->data());
        $this->assertSame($code, $response->code());
    }
}
