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
use Chevere\Components\Message\Message;
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
use function Chevere\Components\Filesystem\getDirFromString;
use function Chevere\Components\Filesystem\getFilePhpReturnFromString;

final class RoutingDescriptorsMaker implements RoutingDescriptorsMakerInterface
{
    private RoutingDescriptorsInterface $descriptors;

    public function __construct(DirInterface $dir)
    {
        $this->descriptors = new RoutingDescriptors;
        try {
            $dirIterator = $this->getRecursiveDirectoryIterator($dir);
            $filterIterator = $this->getRecursiveFilterIterator($dirIterator);
            $iteratorIterator = new RecursiveIteratorIterator($filterIterator);
            $iteratorIterator->rewind();
            while ($iteratorIterator->valid()) {
                $pathName = $iteratorIterator->current()->getPathName();
                $routeName = $this->getVar($pathName);
                $current = dirname($pathName) . '/';
                $endpoints = getRouteEndpointsForDir(new Dir(new Path($current)));
                $generator = $endpoints->getGenerator();
                /** @var RouteEndpointInterface $routeEndpoint */
                $routeEndpoint = $generator->current();
                $path = $this->getPathForParameters(
                    (new Str($current))
                    ->withReplaceFirst(
                        rtrim($dir->path()->absolute(), '/'),
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
                    throw new LogicException(
                        $e->message(),
                        $e->getCode(),
                        $e
                    );
                }
                // @codeCoverageIgnoreEnd
                $this->descriptors = $this->descriptors
                    ->withAdded(
                        new RoutingDescriptor(
                            getDirFromString($current),
                            new RoutePath($path),
                            new RouteDecorator($routeName)
                        )
                    );
                $iteratorIterator->next();
            }
        } catch (Throwable $e) {
            throw new LogicException(
                new Message('Unable to make routing descriptors'),
                $e->getCode(),
                $e
            );
        }
    }

    public function descriptors(): RoutingDescriptorsInterface
    {
        return $this->descriptors;
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
            return getFilePhpReturnFromString($path)
                ->withStrict(false)
                ->varType(new Type(RouteNameInterface::class));
        } catch (FileReturnInvalidTypeException $e) {
            throw new ExpectingRouteNameException($e->message());
        }
    }

    private function getRecursiveDirectoryIterator(DirInterface $dir): RecursiveDirectoryIterator
    {
        return new RecursiveDirectoryIterator(
            $dir->path()->absolute(),
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
