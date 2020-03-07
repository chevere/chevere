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

namespace Chevere\Components\Route;

use Chevere\Components\Filesystem\File;
use Chevere\Components\Filesystem\Interfaces\Dir\DirInterface;
use Chevere\Components\Filesystem\Path;
use Chevere\Components\Filesystem\PhpFile;
use Chevere\Components\Filesystem\PhpFileReturn;
use Chevere\Components\Route\Interfaces\RoutePathIteratorInterface;
use Chevere\Components\Str\Str;
use RecursiveDirectoryIterator;
use RecursiveFilterIterator;
use RecursiveIteratorIterator;
use SplObjectStorage;

/**
 * Iterates over the target dir for files matching RouteName.php
 */
final class RoutePathIterator implements RoutePathIteratorInterface
{
    private RecursiveDirectoryIterator $directoryIterator;

    private RecursiveIteratorIterator $recursiveIterator;

    private SplObjectStorage $objects;

    public function __construct(DirInterface $dir)
    {
        $this->directoryIterator = new RecursiveDirectoryIterator(
            $dir->path()->absolute(),
            RecursiveDirectoryIterator::SKIP_DOTS
            | RecursiveDirectoryIterator::KEY_AS_PATHNAME
        );
        $this->recursiveIterator = new RecursiveIteratorIterator($this->routeNameFilter());
        $this->objects = new SplObjectStorage;
        $this->recursiveIterator->rewind();
        while ($this->recursiveIterator->valid()) {
            $pathName = $this->recursiveIterator->current()->getPathName();
            $fileReturn = new PhpFileReturn(
                new PhpFile(
                    new File(
                        new Path($pathName)
                    )
                )
            );
            $fileReturn = $fileReturn->withStrict(false);
            $routePath = (string) (new Str(dirname($pathName) . '/'))
                ->replaceFirst(
                    rtrim($dir->path()->absolute(), '/'),
                    ''
                );
            $this->objects->attach(
                new RoutePath($routePath),
                $fileReturn->var()
            );

            $this->recursiveIterator->next();
        }
    }

    public function recursiveIterator(): RecursiveIteratorIterator
    {
        return $this->recursiveIterator;
    }

    public function objects(): SplObjectStorage
    {
        return $this->objects;
    }

    private function routeNameFilter(): RecursiveFilterIterator
    {
        return new class($this->directoryIterator) extends RecursiveFilterIterator
        {
            public function accept(): bool
            {
                if ($this->hasChildren()) {
                    return true;
                }

                return $this->current()->getFilename() === RoutePathIterator::ROUTE_NAME_BASENAME;
            }
        };
    }
}
