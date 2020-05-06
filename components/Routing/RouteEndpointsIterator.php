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
use Chevere\Components\Filesystem\Interfaces\PathInterface;
use Chevere\Components\Message\Message;
use Chevere\Components\Route\Interfaces\RouteDecoratorInterface;
use Chevere\Components\Route\Interfaces\RouteEndpointInterface;
use Chevere\Components\Route\Interfaces\RouteEndpointsInterface;
use Chevere\Components\Route\RouteEndpoints;
use Chevere\Components\Routing\Exceptions\ExpectingRouteNameException;
use Chevere\Components\Routing\Interfaces\RouteEndpointIteratorInterface;
use Chevere\Components\Type\Type;

final class RouteEndpointsIterator implements RouteEndpointIteratorInterface
{
    private RouteEndpointsInterface $routeEndpoints;

    /**
     * Iterates over the target dir for files matching KNOWN_METHODS filenames
     * as GET.php
     */
    public function __construct(DirInterface $dir)
    {
        $this->routeEndpoints = new RouteEndpoints;
        $path = $dir->path();
        foreach (array_keys(RouteEndpointInterface::KNOWN_METHODS) as $methodName) {
            $routeEndpointPath = $path->getChild($methodName . '.php');
            if (!$routeEndpointPath->exists()) {
                continue;
            }
            $routeEndpoint = $this->getVar($routeEndpointPath);
            if (!(new Type(RouteEndpointInterface::class))->validate($routeEndpoint)) {
                throw new ExpectingRouteNameException(
                    (new Message('Expecting file return object implementing interface %interfaceName%, type %provided% provided in %fileName%'))
                        ->code('%interfaceName%', RouteDecoratorInterface::class)
                        ->code('%provided%', gettype($routeEndpoint))
                        ->strong('%fileName%', $routeEndpointPath->absolute())
                );
            }
            $this->routeEndpoints->put($routeEndpoint);
        }
    }

    public function routeEndpoints(): RouteEndpointsInterface
    {
        return $this->routeEndpoints;
    }

    private function getVar(PathInterface $path)
    {
        return (
            new FilePhpReturn(new FilePhp(new File($path)))
        )->withStrict(false)->var();
    }
}
