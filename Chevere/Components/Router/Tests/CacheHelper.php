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

namespace Chevere\Components\Router\Tests;

use Chevere\Components\Cache\Cache;
use Chevere\Components\Cache\Interfaces\CacheInterface;
use Chevere\Components\Filesystem\Dir;
use Chevere\Components\Filesystem\Interfaces\Dir\DirInterface;
use Chevere\Components\Filesystem\Interfaces\Path\PathInterface;
use Chevere\Components\Filesystem\Path;

final class CacheHelper
{
    private PathInterface $path;

    public function __construct(string $dir)
    {
        $this->path = new Path($dir);
    }

    public function getResourcesChildDir(string $child): DirInterface
    {
        return new Dir(
            $this->path->getChild('_resources')->getChild($child)
        );
    }

    public function getEmptyCache(): CacheInterface
    {
        return new Cache(
            $this->getResourcesChildDir('empty')
        );
    }

    public function getWorkingCache(): CacheInterface
    {
        return new Cache(
            $this->getResourcesChildDir('working')
        );
    }

    public function getCachedCache(): CacheInterface
    {
        return new Cache(
            $this->getResourcesChildDir('cached')
        );
    }
}
