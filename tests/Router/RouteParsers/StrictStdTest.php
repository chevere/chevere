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
    public function testRoutes(): void
    {
        $this->expectNotToPerformAssertions();
        $strictStd = new StrictStd();
        foreach ([
            '/',
            '/file',
            '/{var}',
            '/{var:\d+}',
            '/folder/',
            '/folder/file',
            '/folder/{var}',
            '/folder/{var:\d+}',
            '/folder/folder/',
        ] as $route) {
            $strictStd->parse($route);
        }
    }

    public function testInvalidOptionalVariable(): void
    {
        $strictStd = new StrictStd();
        $this->expectException(InvalidArgumentException::class);
        $strictStd->parse('/hello-error/[optional]');
    }

    public function testParse(): void
    {
        $strictStd = new StrictStd();
        $datas = $strictStd->parse('/hello-world');
        $this->assertSame([
            0 => ['/hello-world'],
        ], $datas);
    }
}
