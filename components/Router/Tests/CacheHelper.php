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
        if ($this->getWorkingDir()->exists() === false) {
            $this->getWorkingDir()->create();
        }
    }

    public function tearDown(): void
    {
        if ($this->getWorkingDir()->exists()) {
            $this->getWorkingDir()->remove();
        }
    }

    public function getEmptyDir(): DirInterface
    {
        return $this->getResourcesChildDir('empty');
    }

    public function getEmptyCache(): CacheInterface
    {
        return new Cache($this->getEmptyDir());
    }

    public function getWorkingDir(): DirInterface
    {
        return $this->getResourcesChildDir('working');
    }

    public function getWorkingCache(): CacheInterface
    {
        return new Cache($this->getWorkingDir());
    }

    public function getCachedDir(): DirInterface
    {
        return $this->getResourcesChildDir('cached');
    }

    public function getCachedCache(): CacheInterface
    {
        return new Cache($this->getCachedDir());
    }

    private function getResourcesChildDir(string $child): DirInterface
    {
        return new Dir(
            $this->path->getChild('_resources')->getChild($child)
        );
    }
}
