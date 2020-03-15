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

namespace Chevere\Components\Routing;

use Chevere\Components\Filesystem\File;
use Chevere\Components\Filesystem\Interfaces\Dir\DirInterface;
use Chevere\Components\Filesystem\Path;
use Chevere\Components\Filesystem\PhpFile;
use Chevere\Components\Filesystem\PhpFileReturn;
use Chevere\Components\Message\Message;
use Chevere\Components\Route\Interfaces\RouteDecoratorInterface;
use Chevere\Components\Route\RoutePath;
use Chevere\Components\Routing\Exceptions\ExpectingRouteDecoratorException;
use Chevere\Components\Routing\Interfaces\RoutePathIteratorInterface;
use Chevere\Components\Str\Str;
use Chevere\Components\Type\Type;
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
        $this->recursiveIterator = new RecursiveIteratorIterator($this->recursiveFilterIterator());
        $this->objects = new SplObjectStorage();
        $this->recursiveIterator->rewind();
        while ($this->recursiveIterator->valid()) {
            $pathName = $this->recursiveIterator->current()->getPathName();
            $routeDecorator = $this->getVar($pathName);
            if (!(new Type(RouteDecoratorInterface::class))->validate($routeDecorator)) {
                throw new ExpectingRouteDecoratorException(
                    (new Message('Expecting file return object implementing interface %interfaceName%, something else provided in %fileName%'))
                        ->code('%interfaceName%', RouteDecoratorInterface::class)
                        ->strong('%fileName%', $pathName)
                        ->toString()
                );
            }
            $routePath = (string) (new Str(dirname($pathName) . '/'))
                ->replaceFirst(
                    rtrim($dir->path()->absolute(), '/'),
                    ''
                );
            $this->objects->attach(
                new RoutePath($routePath),
                $routeDecorator
            );
            $this->recursiveIterator->next();
        }
    }

    final public function objects(): RoutePathObjectsRead
    {
        return new RoutePathObjectsRead($this->objects);
    }

    private function getVar(string $pathName)
    {
        return (
            new PhpFileReturn(new PhpFile(new File(new Path($pathName))))
        )->withStrict(false)->var();
    }

    private function recursiveFilterIterator(): RecursiveFilterIterator
    {
        return new class($this->directoryIterator) extends RecursiveFilterIterator
        {
            public function accept(): bool
            {
                if ($this->hasChildren()) {
                    return true;
                }

                return $this->current()->getFilename() === RoutePathIterator::ROUTE_DECORATOR_BASENAME;
            }
        };
    }
}
