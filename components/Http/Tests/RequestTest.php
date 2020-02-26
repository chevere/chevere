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

namespace Chevere\Components\Http\Tests;

use Chevere\Components\Globals\Globals;
use Chevere\Components\Http\Method;
use Chevere\Components\Http\Methods\GetMethod;
use Chevere\Components\Http\Request;
use Chevere\Components\Route\PathUri;
use PHPUnit\Framework\TestCase;

final class RequestTest extends TestCase
{
    public function testConstruct(): void
    {
        $request = new Request(
            new GetMethod(),
            new PathUri('/')
        );
        $globals = new Globals([]);
        $this->assertSame($globals->globals(), $request->globals()->globals());
    }

    // public function testConstructAllArguments(): void
    // {
    //     $globs = [
    //         'server' => ['server'],
    //         'get' => ['get'],
    //         'post' => ['post'],
    //         'files' => ['files'],
    //         'cookie' => ['cookie'],
    //         'session' => ['session'],
    //     ];
    //     new Request(
    //         new GetMethod(),
    //         new PathUri('/'),
    //         ['headers'],
    //         'body request',
    //         '1.1',
    //         $globs['server']
    //     );
    //     // $globals = $request->globals();
    //     // xdd($globals);
    //     // $this->assertSame();
    // }

    // public function testFromGlobals(): void
    // {
    //     $this->expectNotToPerformAssertions();
    //     Request::fromGlobals();
    // }
}
