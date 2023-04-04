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

use Chevere\HttpController\Traits\ResponseHtmlTrait;
use Chevere\HttpController\Traits\StatusAcceptedTrait;
use Chevere\HttpController\Traits\StatusAlreadyReportedTrait;
use Chevere\HttpController\Traits\StatusCreatedTrait;
use Chevere\HttpController\Traits\StatusIMUsedTrait;
use Chevere\HttpController\Traits\StatusInternalServerErrorTrait;
use Chevere\HttpController\Traits\StatusMultiStatusTrait;
use Chevere\HttpController\Traits\StatusNoContentTrait;
use Chevere\HttpController\Traits\StatusNonAuthoritativeInformationTrait;
use Chevere\HttpController\Traits\StatusOkTrait;
use Chevere\HttpController\Traits\StatusPartialContentTrait;
use Chevere\HttpController\Traits\StatusResetContentTrait;
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
