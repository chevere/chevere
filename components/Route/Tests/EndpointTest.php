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

namespace Chevere\Components\Route\Tests;

use Chevere\Components\Filesystem\Dir;
use Chevere\Components\Filesystem\Interfaces\Dir\DirInterface;
use Chevere\Components\Filesystem\Path;
use Chevere\Components\Http\Methods\GetMethod;
use Chevere\Components\Route\Exceptions\EndpointException;
use Chevere\Components\Route\Interfaces\WildcardCollectionInterface;
use Chevere\Components\Route\Wildcard;
use PHPUnit\Framework\TestCase;

final class EndpointTest extends TestCase
{
    private DirInterface $resourcesDir;

    public function setUp(): void
    {
        $this->resourcesDir = (new Dir(new Path(__DIR__ . '/')))->getChild('_resources/');
    }

    public function testPath(): void
    {
        $absolute = $this->resourcesDir->path()->getChild('routes/articles/Get.php')->absolute();
        $endpoint = include $absolute;
        $this->assertSame($absolute, $endpoint->whereIs());
        $this->assertSame('', $endpoint->path());
        $this->assertInstanceOf(WildcardCollectionInterface::class, $endpoint->wildcardCollection());
        $this->assertInstanceOf(GetMethod::class, $endpoint->method());
    }

    public function testPathWildcard(): void
    {
        $endpoint = include $this->resourcesDir->path()->getChild('routes/articles/{id}/Get.php')->absolute();
        $collection = $endpoint->wildcardCollection();
        $this->assertTrue($collection->has(new Wildcard('id')));
    }

    public function testWithRoot(): void
    {
        $locator = 'routes/articles/{id}';
        $endpoint = include $this->resourcesDir->path()->getChild($locator . '/Get.php')->absolute();
        $endpoint = $endpoint->withRootDir($this->resourcesDir);
        $this->assertSame('/' . $locator, $endpoint->path());
    }

    public function testWrongFilename(): void
    {
        $this->expectException(EndpointException::class);
        include $this->resourcesDir->path()->getChild('routes/articles/Wrong.php')->absolute();
    }
}
