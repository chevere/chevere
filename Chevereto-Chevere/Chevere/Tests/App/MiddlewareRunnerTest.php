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

namespace Chevere\Tests\App;

use Chevere\Components\App\App;
use Chevere\Components\App\Exceptions\AppWithoutRequestException;
use Chevere\Components\App\MiddlewareRunner;
use Chevere\Components\Http\Response;
use PHPUnit\Framework\TestCase;

final class MiddlewareRunnerTest extends TestCase
{
    public function testConstructorAppWithoutRequest(): void
    {
        $app = new App(new Response());
        $this->expectException(AppWithoutRequestException::class);
        new MiddlewareRunner([], $app);
    }
}
