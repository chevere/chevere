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
use Chevere\Components\Filesystem\Interfaces\DirInterface;
use Chevere\Components\Filesystem\Interfaces\PathInterface;
use Chevere\Components\Filesystem\Path;
use PHPUnit\Framework\TestCase;
use ReflectionObject;

final class CacheHelper
{
    private PathInterface $path;

    public function __construct(string $dir, TestCase $object)
    {
        $this->path = new Path(
            $dir . '/_resources/' . (new ReflectionObject($object))->getShortName()
            . '/'
        );
        if (!$this->getWorkingDir()->exists()) {
            $this->getWorkingDir()->create();
        }
        if (!$this->getEmptyDir()->exists()) {
            $this->getEmptyDir()->create();
        }
        if (!$this->getCachedDir()->exists()) {
            $this->getCachedDir()->create();
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
        return $this->getChildDir('empty/');
    }

    public function getEmptyCache(): CacheInterface
    {
        return new Cache($this->getEmptyDir());
    }

    public function getWorkingDir(): DirInterface
    {
        return $this->getChildDir('working/');
    }

    public function getWorkingCache(): CacheInterface
    {
        return new Cache($this->getWorkingDir());
    }

    public function getWrongDir(): DirInterface
    {
        return $this->getChildDir('wrong/');
    }

    public function getWrongCache(): CacheInterface
    {
        return new Cache($this->getWrongDir());
    }

    public function getCachedDir(): DirInterface
    {
        return $this->getChildDir('cached/');
    }

    public function getCachedCache(): CacheInterface
    {
        return new Cache($this->getCachedDir());
    }

    private function getChildDir(string $child): DirInterface
    {
        return new Dir($this->path->getChild($child));
    }
}
