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

use Chevere\Response\Interfaces\ResponseInterface;
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
        $this->assertSame(ResponseInterface::TOKEN_LENGTH, strlen($response->token()));
        $this->assertIsString($response->token());
        $data = [
            'key' => 'value',
        ];
        $response = new Response(...$data);
        $this->assertSame($data, $response->data());
    }

    public function testWithData(): void
    {
        $response = new Response();
        $this->assertSame([], $response->data());
        $data = [
            'key' => 'value',
        ];
        $withData = $response->withData(...$data);
        $this->assertNotSame($response, $withData);
        $this->assertSame($data, $withData->data());
    }

    public function testWithCode(): void
    {
        $code = 123;
        $response = new Response();
        $withCode = $response->withCode($code);
        $this->assertNotSame($response, $withCode);
        $this->assertSame($code, $withCode->code());
    }
}
