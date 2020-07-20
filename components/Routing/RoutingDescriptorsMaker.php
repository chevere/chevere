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

use Chevere\Components\Filesystem\Dir;
use Chevere\Components\Filesystem\Path;
use Chevere\Components\Regex\Regex;
use Chevere\Components\Route\Route;
use Chevere\Components\Route\RouteDecorator;
use Chevere\Components\Route\RouteName;
use Chevere\Components\Route\RoutePath;
use Chevere\Components\Routing\RoutingDescriptor;
use Chevere\Components\Str\Str;
use Chevere\Components\Type\Type;
use Chevere\Exceptions\Core\Exception;
use Chevere\Exceptions\Core\LogicException;
use Chevere\Exceptions\Filesystem\FileReturnInvalidTypeException;
use Chevere\Exceptions\Routing\ExpectingRouteNameException;
use Chevere\Interfaces\Filesystem\DirInterface;
use Chevere\Interfaces\Route\RouteEndpointInterface;
use Chevere\Interfaces\Route\RouteNameInterface;
use Chevere\Interfaces\Routing\RoutingDescriptorsInterface;
use Chevere\Interfaces\Routing\RoutingDescriptorsMakerInterface;
use Chevere\Interfaces\Str\StrInterface;
use RecursiveDirectoryIterator;
use RecursiveFilterIterator;
use RecursiveIteratorIterator;
use Throwable;
use function Chevere\Components\Filesystem\dirFromString;
use function Chevere\Components\Filesystem\filePhpReturnFromString;

final class RoutingDescriptorsMaker implements RoutingDescriptorsMakerInterface
{
    private DirInterface $dir;

    private RoutingDescriptorsInterface $descriptors;

    public function __construct(DirInterface $dir)
    {
        $this->dir = $dir;
        $this->descriptors = new RoutingDescriptors;
        try {
            $dirIterator = $this->getRecursiveDirectoryIterator();
            $this->iterate(
                new RecursiveIteratorIterator(
                    $this->getRecursiveFilterIterator($dirIterator)
                )
            );
        } catch (Throwable $e) {
            throw new LogicException(null, 0, $e);
        }
    }

    public function descriptors(): RoutingDescriptorsInterface
    {
        return $this->descriptors;
    }

    private function iterate(RecursiveIteratorIterator $iterator): void
    {
        $iterator->rewind();
        while ($iterator->valid()) {
            $pathName = $iterator->current()->getPathName();
            $routeName = $this->getVar($pathName);
            $current = dirname($pathName) . '/';
            $endpoints = routeEndpointsForDir(new Dir(new Path($current)));
            $generator = $endpoints->getGenerator();
            /** @var RouteEndpointInterface $routeEndpoint */
            $routeEndpoint = $generator->current();
            $path = $this->getPathForParameters(
                (new Str($current))
                    ->withReplaceFirst(
                        rtrim($this->dir->path()->absolute(), '/'),
                        ''
                    ),
                $routeEndpoint->parameters()
            );
            $route = new Route(new RouteName('name'), new RoutePath($path));
            try {
                foreach ($generator as $routeEndpoint) {
                    $route = $route->withAddedEndpoint($routeEndpoint);
                }
            }
            // @codeCoverageIgnoreStart
            catch (Exception $e) {
                throw new LogicException(null, 0, $e);
            }
            // @codeCoverageIgnoreEnd
            $this->descriptors = $this->descriptors
                    ->withAdded(
                        new RoutingDescriptor(
                            dirFromString($current),
                            new RoutePath($path),
                            new RouteDecorator($routeName)
                        )
                    );
            $iterator->next();
        }
    }

    private function getPathForParameters(StrInterface $path, array $parameters): string
    {
        foreach ($parameters as $key => $param) {
            $regex = (new Regex($param['regex']))->toNoDelimitersNoAnchors();
            $path = $path->withReplaceAll("{$key}", "$key:$regex");
        }

        return $path->toString();
    }

    private function getVar(string $path): RouteNameInterface
    {
        try {
            return filePhpReturnFromString($path)
                ->withStrict(false)
                ->varType(new Type(RouteNameInterface::class));
        } catch (FileReturnInvalidTypeException $e) {
            throw new ExpectingRouteNameException($e->message());
        }
    }

    private function getRecursiveDirectoryIterator(): RecursiveDirectoryIterator
    {
        return new RecursiveDirectoryIterator(
            $this->dir->path()->absolute(),
            RecursiveDirectoryIterator::SKIP_DOTS
            | RecursiveDirectoryIterator::KEY_AS_PATHNAME
        );
    }

    private function getRecursiveFilterIterator(RecursiveDirectoryIterator $recursiveDirectoryIterator): RecursiveFilterIterator
    {
        return new class($recursiveDirectoryIterator) extends RecursiveFilterIterator
        {
            public function accept(): bool
            {
                if ($this->hasChildren()) {
                    return true;
                }

                return $this->current()->getFilename() === RoutingDescriptorsMaker::ROUTE_NAME_BASENAME;
            }
        };
    }
}
