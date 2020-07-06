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

use Chevere\Components\Controller\ControllerParameter;
use Chevere\Components\Controller\ControllerParameters;
use Chevere\Components\Filesystem\Dir;
use Chevere\Components\Filesystem\File;
use Chevere\Components\Filesystem\FilePhp;
use Chevere\Components\Filesystem\FilePhpReturn;
use Chevere\Components\Filesystem\FilesystemFactory;
use Chevere\Components\Filesystem\Path;
use Chevere\Components\Message\Message;
use Chevere\Components\Regex\Regex;
use Chevere\Components\Route\RouteDecorator;
use Chevere\Components\Route\RoutePath;
use Chevere\Components\Str\Str;
use Chevere\Components\Type\Type;
use Chevere\Exceptions\Core\LogicException;
use Chevere\Exceptions\Filesystem\FileReturnInvalidTypeException;
use Chevere\Exceptions\Routing\ExpectingRouteNameException;
use Chevere\Interfaces\Filesystem\DirInterface;
use Chevere\Interfaces\Route\RouteEndpointInterface;
use Chevere\Interfaces\Route\RouteNameInterface;
use Chevere\Interfaces\Routing\FsRoutesInterface;
use Chevere\Interfaces\Routing\FsRoutesMakerInterface;
use Ds\Map;
use RecursiveDirectoryIterator;
use RecursiveFilterIterator;
use RecursiveIteratorIterator;

final class FsRoutesMaker implements FsRoutesMakerInterface
{
    private FsRoutesInterface $fsRoutes;

    public function __construct(DirInterface $dir)
    {
        $dirIterator = $this->getRecursiveDirectoryIterator($dir);
        $filterIterator = $this->getRecursiveFilterIterator($dirIterator);
        $iteratorIterator = new RecursiveIteratorIterator($filterIterator);
        $this->fsRoutes = new FsRoutes;
        $iteratorIterator->rewind();
        while ($iteratorIterator->valid()) {
            $pathName = $iteratorIterator->current()->getPathName();
            $routeName = $this->getVar($pathName);
            $current = dirname($pathName) . '/';
            $endpointsIterator = new RouteEndpointsIterator(
                new Dir(new Path($current))
            );
            $generator = $endpointsIterator->routeEndpoints()->getGenerator();
            $known = $generator->current();
            $knownParameters = $known->parameters();
            foreach ($endpointsIterator->routeEndpoints()->getGenerator() as $key => $routeEndpoint) {
                $parameters = $routeEndpoint->parameters();
                if ($knownParameters !== $parameters) {
                    throw new LogicException(
                        (new Message("Controller parameters defined at %endpoint% doesn't match with the parameters found at %conflict%"))
                            ->code('%endpoint%', get_class($known))
                            ->code('%conflict%', 'eee')
                    );
                }
            }
            $path = (new Str($current))
                ->withReplaceFirst(
                    rtrim($dir->path()->absolute(), '/'),
                    ''
                );
            foreach ($routeEndpoint->parameters() as $key => $param) {
                $path = $path->withReplaceAll("{$key}", (new Regex($param['regex']))->toNoDelimitersNoAnchors());
            }
            $path = $path->toString();
            $this->fsRoutes = $this->fsRoutes->withDecorated(
                new FsRoute(
                    (new FilesystemFactory)->getDirFromString($current),
                    new RoutePath($path),
                    new RouteDecorator($routeName)
                )
            );
            $iteratorIterator->next();
        }
    }

    public function fsRoutes(): FsRoutesInterface
    {
        return $this->fsRoutes;
    }

    private function getVar(string $path): RouteNameInterface
    {
        try {
            return (new FilePhpReturn(new FilePhp(new File(new Path($path)))))
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

                return $this->current()->getFilename() === FsRoutesMaker::ROUTE_NAME_BASENAME;
            }
        };
    }
}
