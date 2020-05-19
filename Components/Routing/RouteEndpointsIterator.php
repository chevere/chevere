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

use Chevere\Interfaces\Controller\ControllerInterface;
use Chevere\Exceptions\Filesystem\FileReturnInvalidTypeException;
use Chevere\Components\Filesystem\File;
use Chevere\Components\Filesystem\FilePhp;
use Chevere\Components\Filesystem\FilePhpReturn;
use Chevere\Interfaces\Filesystem\DirInterface;
use Chevere\Interfaces\Filesystem\PathInterface;
use Chevere\Components\Message\Message;
use Chevere\Interfaces\Route\RouteDecoratorInterface;
use Chevere\Interfaces\Route\RouteEndpointInterface;
use Chevere\Interfaces\Route\RouteEndpointsInterface;
use Chevere\Components\Route\RouteEndpoint;
use Chevere\Components\Route\RouteEndpoints;
use Chevere\Exceptions\Routing\ExpectingControllerException;
use Chevere\Exceptions\Routing\ExpectingRouteNameException;
use Chevere\Interfaces\Routing\RouteEndpointIteratorInterface;
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
        foreach (array_keys(RouteEndpointInterface::KNOWN_METHODS) as $name) {
            $controllerPath = $path->getChild($name . '.php');
            if (!$controllerPath->exists()) {
                continue;
            }
            $method = RouteEndpointInterface::KNOWN_METHODS[$name];
            $controller = $this->getVar($controllerPath);
            $routeEndpoint = new RouteEndpoint(new $method, $controller);
            if (!(new Type(RouteEndpointInterface::class))->validate($routeEndpoint)) {
                $provided = is_object($routeEndpoint)
                    ? get_class($routeEndpoint)
                    : gettype($routeEndpoint);
                if ($provided === 'object') {
                    throw new ExpectingRouteNameException(
                        (new Message('Expecting file return object implementing interface %interfaceName%, type %provided% provided in %fileName%'))
                        ->code('%interfaceName%', RouteDecoratorInterface::class)
                        ->code('%provided%', $provided)
                        ->strong('%fileName%', $controllerPath->absolute())
                    );
                }
            }
            $this->routeEndpoints->put($routeEndpoint);
        }
    }

    public function routeEndpoints(): RouteEndpointsInterface
    {
        return $this->routeEndpoints;
    }

    private function getVar(PathInterface $path): ControllerInterface
    {
        try {
            return (new FilePhpReturn(new FilePhp(new File($path))))
                ->withStrict(false)
                ->varType(new Type(ControllerInterface::class));
        } catch (FileReturnInvalidTypeException $e) {
            throw new ExpectingControllerException($e->message());
        }
    }
}
