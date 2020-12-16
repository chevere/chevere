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

use Chevere\Components\Regex\Regex;
use Chevere\Components\Router\Route\Route;
use Chevere\Components\Router\Route\RouteDecorator;
use Chevere\Components\Router\Route\RouteName;
use Chevere\Components\Router\Route\RoutePath;
use Chevere\Components\Str\Str;
use Chevere\Interfaces\Filesystem\DirInterface;
use Chevere\Interfaces\Router\Route\RouteEndpointInterface;
use Chevere\Interfaces\Router\Routing\RoutingDescriptorsInterface;
use Chevere\Interfaces\Router\Routing\RoutingDescriptorsMakerInterface;
use Chevere\Interfaces\Str\StrInterface;
use Ds\Set;
use RecursiveDirectoryIterator;
use RecursiveFilterIterator;
use RecursiveIteratorIterator;
use function Chevere\Components\Filesystem\dirForPath;
use function Chevere\Components\Iterator\recursiveDirectoryIteratorFor;

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
                $this->getRecursiveFilterIterator(
                    recursiveDirectoryIteratorFor($dir, $dirFlags),
                )
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
            $pathName = $iterator->current()->getPathName();
            $dirName = rtrim(dirname($pathName), '/') . '/';
            $path = str_replace($this->dir->path()->absolute(), '/', $dirName);
            $routeName = new RouteName($this->repository . ':' . $path);
            $endpoints = routeEndpointsForDir(dirForPath($dirName));
            $generator = $endpoints->getGenerator();
            $routeEndpoint = $generator->current();
            /** @var RouteEndpointInterface $routeEndpoint */
            $path = $this->getPathForParameters(
                (new Str($dirName))
                    ->withReplaceFirst(
                        rtrim($this->dir->path()->absolute(), '/'),
                        ''
                    ),
                $routeEndpoint->parameters()
            );
            $route = new Route(new RoutePath($path));
            foreach ($generator as $routeEndpoint) {
                $route = $route->withAddedEndpoint($routeEndpoint);
            }
            $this->descriptors = $this->descriptors
                    ->withAdded(
                        new RoutingDescriptor(
                            dirForPath($dirName),
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

    private function getRecursiveFilterIterator(RecursiveDirectoryIterator $recursiveDirectoryIterator): RecursiveFilterIterator
    {
        return new class($recursiveDirectoryIterator) extends RecursiveFilterIterator {
            private Set $methodFilenames;

            private Set $knownPaths;

            public function __construct(RecursiveDirectoryIterator $iterator)
            {
                parent::__construct($iterator);
                $this->methodFilenames = new Set();
                $this->knownPaths = new Set();
                foreach (array_keys(RouteEndpointInterface::KNOWN_METHODS) as $method) {
                    $this->methodFilenames->add("$method.php");
                }
            }

            public function accept(): bool
            {
                if ($this->hasChildren()) {
                    return true;
                }
                $dirname = dirname((string) $this->current());
                if ($this->knownPaths->contains($dirname)) {
                    return false;
                }
                if ($this->methodFilenames->contains($this->current()->getFilename())) {
                    $this->knownPaths->add($dirname);

                    return true;
                }

                return false;
            }
        };
    }
}
