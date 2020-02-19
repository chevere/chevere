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

/*
 * This file is part of Chevere.
 *
 * (c) Rodolfo Berrios <rodolfo@chevere.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Chevere\Tests\Router\Properties;

use BadMethodCallException;
use Chevere\Components\Regex\Regex;
use Chevere\Components\Route\PathUri;
use Chevere\Components\Router\RouterIndex;
use Chevere\Components\Router\RouterNamed;
use Chevere\Components\Router\RouterRegex;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class RouterIndexTest extends TestCase
{
    public function testConstruct(): void
    {
        $pathUri = new PathUri('/path');
        $routerIndex = new RouterIndex();
        $this->assertSame([], $routerIndex->toArray());
        $this->assertFalse($routerIndex->has($pathUri));
        $this->expectException(BadMethodCallException::class);
        $routerIndex->get($pathUri);
    }

    public function testWithAdded(): void
    {
        $patUri = new PathUri('/path');
        $id = 0;
        $routerIndex = (new RouterIndex())
            ->withAdded($patUri, $id, 'some-group', 'some-name');
        $this->assertTrue($routerIndex->has($patUri));
        $this->assertIsArray($routerIndex->get($patUri));
    }
}
