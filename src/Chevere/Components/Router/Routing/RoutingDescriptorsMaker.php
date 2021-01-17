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
use Chevere\Components\Writer\NullWriter;
use Chevere\Interfaces\Filesystem\DirInterface;
use Chevere\Interfaces\Router\Route\RouteEndpointInterface;
use Chevere\Interfaces\Router\Routing\RoutingDescriptorsInterface;
use Chevere\Interfaces\Router\Routing\RoutingDescriptorsMakerInterface;
use Chevere\Interfaces\Str\StrInterface;
use Chevere\Interfaces\Writer\WriterInterface;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

final class RoutingDescriptorsMaker implements RoutingDescriptorsMakerInterface
{
    private WriterInterface $writer;

    private string $repository;

    private DirInterface $dir;

    private RoutingDescriptorsInterface $descriptors;

    private bool $useTrailingSlash;

    public function __construct(string $repository)
    {
        $this->writer = new NullWriter();
        $this->repository = $repository;
        $this->descriptors = new RoutingDescriptors();
        $this->useTrailingSlash = false;
    }

    public function withWriter(WriterInterface $writer): self
    {
        $new = clone $this;
        $new->writer = $writer;

        return $new;
    }

    public function withTrailingSlash(bool $bool): self
    {
        $new = clone $this;
        $new->useTrailingSlash = $bool;

        return $new;
    }

    public function withDescriptorsFor(DirInterface $dir): self
    {
        $new = clone $this;
        $new->dir = $dir;
        $new->iterate(
            new RecursiveIteratorIterator(
                new RoutingDescriptorsIterator(
                    recursiveDirectoryIteratorFor(
                        $new->dir,
                        RecursiveDirectoryIterator::SKIP_DOTS | RecursiveDirectoryIterator::KEY_AS_PATHNAME
                    )
                )
            )
        );

        return $new;
    }

    public function repository(): string
    {
        return $this->repository;
    }

    public function writer(): WriterInterface
    {
        return $this->writer;
    }

    public function useTrailingSlash(): bool
    {
        return $this->useTrailingSlash;
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
            $routePath = str_replace($this->dir->path()->toString(), '/', $dirName);
            if (! $this->useTrailingSlash && $routePath !== '/') {
                $routePath = rtrim($routePath, '/');
            }
            $locator = new RouteLocator($this->repository, $routePath);
            $endpoints = routeEndpointsForDir(dirForPath($dirName));
            $generator = $endpoints->getGenerator();
            /** @var RouteEndpointInterface $routeEndpoint */
            $endpoint = $generator->current();
            $routePath = $this->getPathForParameters(
                (new Str($routePath)),
                $endpoint->parameters()
            );
            $route = new Route(new RoutePath($routePath));
            foreach ($generator as $endpoint) {
                $route = $route->withAddedEndpoint($endpoint);
            }
            $this->descriptors = $this->descriptors
                ->withAdded(
                    new RoutingDescriptor(
                        dirForPath($dirName),
                        new RoutePath($routePath),
                        new RouteDecorator($locator)
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

        return $path->toString();

        // return $path->withReplaceLast('/', '')->toString();
    }
}
