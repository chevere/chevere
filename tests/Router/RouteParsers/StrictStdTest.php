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

namespace Chevere\Tests\Router\RouteParsers;

use Chevere\Components\Router\RouteParsers\StrictStd;
use Chevere\Exceptions\Core\InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class StrictStdTest extends TestCase
{
    public function testInvalidRoute(): void
    {
        $routeParser = new StrictStd();
        $this->expectException(InvalidArgumentException::class);
        $routeParser->parse('/hello-error/');
    }

    public function testInvalidOptionalVariable(): void
    {
        $routeParser = new StrictStd();
        $this->expectException(InvalidArgumentException::class);
        $routeParser->parse('/hello-error/[optional]');
    }

    public function testParse(): void
    {
        $routeParser = new StrictStd();
        $datas = $routeParser->parse('/hello-world');
        $this->assertSame([
            0 => ['/hello-world'],
        ], $datas);
    }
}
