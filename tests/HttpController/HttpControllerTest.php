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

namespace Chevere\Tests\HttpController;

use Chevere\Tests\HttpController\_resources\TestHttpAcceptController;
use Chevere\Tests\HttpController\_resources\TestHttpController;
use Chevere\Throwable\Errors\ArgumentCountError;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class HttpControllerTest extends TestCase
{
    public function testDefaults(): void
    {
        $controller = new TestHttpController();
        $this->assertSame(200, $controller->statusSuccess());
        $this->assertSame(500, $controller->statusError());
        $this->assertCount(0, $controller->acceptQuery()->items());
        $this->assertCount(0, $controller->acceptBody()->items());
        $this->assertCount(0, $controller->acceptFiles()->items());
        $this->assertSame(
            [
                'Content-Disposition' => 'inline',
                'Content-Type' => 'application/json',
            ],
            $controller->responseHeaders()
        );
    }

    public function testAcceptGetParameters(): void
    {
        $controller = new TestHttpAcceptController();
        $this->assertSame([], $controller->query());
        $controllerWith = $controller->withQuery([
            'foo-foo' => 'abc',
        ]);
        $this->assertNotSame($controller, $controllerWith);
        $this->assertNotEquals($controller, $controllerWith);
        $this->assertSame('abc', $controllerWith->query()['foo-foo']);
        $this->expectException(InvalidArgumentException::class);
        $controller->withQuery([
            'foo-foo' => '123',
        ]);
    }

    public function testAcceptPostParameters(): void
    {
        $controller = new TestHttpAcceptController();
        $this->assertSame([], $controller->body());
        $controllerWith = $controller->withBody([
            'bar.bar' => '123',
        ]);
        $this->assertNotSame($controller, $controllerWith);
        $this->assertNotEquals($controller, $controllerWith);
        $this->assertSame('123', $controllerWith->body()['bar.bar']);
        $this->expectException(InvalidArgumentException::class);
        $controller->withBody([
            'bar.bar' => 'abc',
        ]);
    }

    public function testAcceptFileParameters(): void
    {
        $controller = new TestHttpAcceptController();
        $file = [
            'type' => 'text/plain',
            'tmp_name' => '/tmp/file.yx5kVl',
            'size' => 1313,
            'name' => 'readme.txt',
            'error' => UPLOAD_ERR_OK,
        ];
        $this->assertSame([], $controller->files());
        $controllerWith = $controller->withFiles([
            'MyFile!' => $file,
        ]);
        $this->assertNotSame($controller, $controllerWith);
        $this->assertNotEquals($controller, $controllerWith);
        $this->assertSame($file, $controllerWith->files()['MyFile!']);
        $this->expectException(ArgumentCountError::class);
        $controller->withFiles([
            'MyFile!' => [],
        ]);
    }
}
