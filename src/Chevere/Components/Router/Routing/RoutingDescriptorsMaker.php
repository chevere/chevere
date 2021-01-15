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

namespace Chevere\Components\Router\Routing;

use function Chevere\Components\Filesystem\dirForPath;
use function Chevere\Components\Iterator\recursiveDirectoryIteratorFor;
use Chevere\Components\Regex\Regex;
use Chevere\Components\Router\Route\Route;
use Chevere\Components\Router\Route\RouteDecorator;
use Chevere\Components\Router\Route\RouteLocator;
use Chevere\Components\Router\Route\RoutePath;
use Chevere\Components\Str\Str;
use Chevere\Interfaces\Filesystem\DirInterface;
use Chevere\Interfaces\Router\Route\RouteEndpointInterface;
use Chevere\Interfaces\Router\Routing\RoutingDescriptorsInterface;
use Chevere\Interfaces\Router\Routing\RoutingDescriptorsMakerInterface;
use Chevere\Interfaces\Str\StrInterface;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

final class RoutingDescriptorsMaker implements RoutingDescriptorsMakerInterface
{
    private string $repository;

    private DirInterface $dir;

    private RoutingDescriptorsInterface $descriptors;

    public function __construct(string $repository, DirInterface $dir)
    {
        $this->repository = $repository;
        $this->dir = $dir;
        $this->descriptors = new RoutingDescriptors();
        $dirFlags = RecursiveDirectoryIterator::SKIP_DOTS | RecursiveDirectoryIterator::KEY_AS_PATHNAME;
        $this->iterate(
            new RecursiveIteratorIterator(
                new RoutingDescriptorsIterator(recursiveDirectoryIteratorFor($dir, $dirFlags))
            )
        );
    }

    public function descriptors(): RoutingDescriptorsInterface
    {
        return $this->descriptors;
    }

    private function iterate(RecursiveIteratorIterator $iterator): void
    {
        $iterator->rewind();
        while ($iterator->valid()) {
            $dirName = rtrim(dirname($iterator->current()->getPathName()), '/') . '/';
            $routePath = rtrim(
                str_replace($this->dir->path()->toString(), '/', $dirName),
                '/'
            ) . '/';
            $routeLocator = new RouteLocator($this->repository, $routePath);
            $endpoints = routeEndpointsForDir(dirForPath($dirName));
            $generator = $endpoints->getGenerator();
            /** @var RouteEndpointInterface $routeEndpoint */
            $routeEndpoint = $generator->current();
            $pathParams = $this->getPathForParameters(
                (new Str($dirName))
                    ->withReplaceFirst(
                        rtrim($this->dir->path()->toString(), '/'),
                        ''
                    ),
                $routeEndpoint->parameters()
            );
            $route = new Route(new RoutePath($pathParams));
            foreach ($generator as $routeEndpoint) {
                $route = $route->withAddedEndpoint($routeEndpoint);
            }
            $this->descriptors = $this->descriptors
                ->withAdded(
                    new RoutingDescriptor(
                        dirForPath($dirName),
                        new RoutePath($pathParams),
                        new RouteDecorator($routeLocator)
                    )
                );
            $iterator->next();
        }
    }

    private function getPathForParameters(StrInterface $path, array $parameters): string
    {
        foreach ($parameters as $key => $param) {
            $regex = (new Regex($param['regex']))->toNoDelimitersNoAnchors();
            $path = $path->withReplaceAll("{$key}", "${key}:${regex}");
        }
        if ($path->toString() === '/') {
        }

        return $path->toString();

        // return $path->withReplaceLast('/', '')->toString();
    }
}
