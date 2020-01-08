<?php

/*
 * This file is part of Chevere.
 *
 * (c) Rodolfo Berrios <rodolfo@chevereto.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Chevere\Tests\Http;

use Chevere\Components\Http\Method;
use Chevere\Components\Http\Request;
use Chevere\Components\Route\PathUri;
use PHPUnit\Framework\TestCase;

final class RequestTest extends TestCase
{
    public function testBasicConstruct(): void
    {
        $this->expectNotToPerformAssertions();
        new Request(new Method('GET'), new PathUri('/'));
    }

    public function testFromGlobals(): void
    {
        $this->expectNotToPerformAssertions();
        Request::fromGlobals();
    }
}
