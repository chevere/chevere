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

use Chevere\Http\Traits\ServerError\StatusInternalServerErrorTrait;
use Chevere\Http\Traits\Successful\StatusAcceptedTrait;
use Chevere\Http\Traits\Successful\StatusAlreadyReportedTrait;
use Chevere\Http\Traits\Successful\StatusCreatedTrait;
use Chevere\Http\Traits\Successful\StatusIMUsedTrait;
use Chevere\Http\Traits\Successful\StatusMultiStatusTrait;
use Chevere\Http\Traits\Successful\StatusNoContentTrait;
use Chevere\Http\Traits\Successful\StatusNonAuthoritativeInformationTrait;
use Chevere\Http\Traits\Successful\StatusOkTrait;
use Chevere\Http\Traits\Successful\StatusPartialContentTrait;
use Chevere\Http\Traits\Successful\StatusResetContentTrait;
use Chevere\HttpController\Traits\ResponseHtmlTrait;
use Chevere\Tests\HttpController\_resources\TestHttpController;
use PHPUnit\Framework\TestCase;

final class HttpControllerTraitsTest extends TestCase
{
    public function testStatusAcceptedTrait(): void
    {
        $class = new class() extends TestHttpController {
            use StatusAcceptedTrait;
        };
        $this->assertSame(202, $class->statusSuccess());
    }

    public function testStatusAlreadyReportedTrait(): void
    {
        $class = new class() extends TestHttpController {
            use StatusAlreadyReportedTrait;
        };
        $this->assertSame(208, $class->statusSuccess());
    }

    public function testStatusCreatedTrait(): void
    {
        $class = new class() extends TestHttpController {
            use StatusCreatedTrait;
        };
        $this->assertSame(201, $class->statusSuccess());
    }

    public function testStatusIMUsedTrait(): void
    {
        $class = new class() extends TestHttpController {
            use StatusIMUsedTrait;
        };
        $this->assertSame(226, $class->statusSuccess());
    }

    public function testStatusInternalServerErrorTrait(): void
    {
        $class = new class() extends TestHttpController {
            use StatusInternalServerErrorTrait;
            use StatusNoContentTrait;
        };
        $this->assertSame(500, $class->statusError());
        $this->assertSame(204, $class->statusSuccess());
    }

    public function testStatusMultiStatusTrait(): void
    {
        $class = new class() extends TestHttpController {
            use StatusMultiStatusTrait;
        };
        $this->assertSame(207, $class->statusSuccess());
    }

    public function testStatusNoContentTrait(): void
    {
        $class = new class() extends TestHttpController {
            use StatusNoContentTrait;
        };
        $this->assertSame(204, $class->statusSuccess());
    }

    public function testStatusNonAuthoritativeInformationTrait(): void
    {
        $class = new class() extends TestHttpController {
            use StatusNonAuthoritativeInformationTrait;
        };
        $this->assertSame(203, $class->statusSuccess());
    }

    public function testStatusOkTrait(): void
    {
        $class = new class() extends TestHttpController {
            use StatusOkTrait;
        };
        $this->assertSame(200, $class->statusSuccess());
    }

    public function testStatusPartialContentTrait(): void
    {
        $class = new class() extends TestHttpController {
            use StatusPartialContentTrait;
        };
        $this->assertSame(206, $class->statusSuccess());
    }

    public function testStatusResetContentTrait(): void
    {
        $class = new class() extends TestHttpController {
            use StatusResetContentTrait;
        };
        $this->assertSame(205, $class->statusSuccess());
    }

    public function testResponseHtmlTrait(): void
    {
        $class = new class() extends TestHttpController {
            use ResponseHtmlTrait;
        };
        $this->assertSame(
            'text/html; charset=utf-8',
            $class->responseHeaders()['Content-Type']
        );
    }
}
