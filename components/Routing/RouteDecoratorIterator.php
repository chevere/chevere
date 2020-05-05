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
use Chevere\Components\Filesystem\FilePhp;
use Chevere\Components\Filesystem\FilePhpReturn;
use Chevere\Components\Filesystem\Interfaces\DirInterface;
use Chevere\Components\Filesystem\Path;
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

final class RouteDecoratorIterator implements RoutePathIteratorInterface
{
    private RecursiveIteratorIterator $recursiveIterator;

    private DecoratedRoutes $decoratedRoutes;

    public function __construct(DirInterface $dir)
    {
        $this->directoryIterator = new RecursiveDirectoryIterator(
            $dir->path()->absolute(),
            RecursiveDirectoryIterator::SKIP_DOTS
            | RecursiveDirectoryIterator::KEY_AS_PATHNAME
        );
        $this->recursiveIterator = new RecursiveIteratorIterator($this->recursiveFilterIterator());
        $this->decoratedRoutes = new DecoratedRoutes;
        $this->recursiveIterator->rewind();
        while ($this->recursiveIterator->valid()) {
            $pathName = $this->recursiveIterator->current()->getPathName();
            $routeDecorator = $this->getVar($pathName);
            if (!(new Type(RouteDecoratorInterface::class))->validate($routeDecorator)) {
                throw new ExpectingRouteDecoratorException(
                    (new Message('Expecting file return object implementing interface %interfaceName%, something else provided in %fileName%'))
                        ->code('%interfaceName%', RouteDecoratorInterface::class)
                        ->strong('%fileName%', $pathName)
                );
            }
            $routePath = (new Str(dirname($pathName) . '/'))
                ->replaceFirst(
                    rtrim($dir->path()->absolute(), '/'),
                    ''
                )
                ->toString();
            $this->decoratedRoutes = $this->decoratedRoutes->withDecorated(
                new DecoratedRoute(
                    new RoutePath($routePath),
                    $routeDecorator
                )
            );
            $this->recursiveIterator->next();
        }
    }

    public function decoratedRoutes(): DecoratedRoutes
    {
        return $this->decoratedRoutes;
    }

    private function getVar(string $pathName)
    {
        return (
            new FilePhpReturn(new FilePhp(new File(new Path($pathName))))
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

                return $this->current()->getFilename() === RouteDecoratorIterator::ROUTE_DECORATOR_BASENAME;
            }
        };
    }
}
