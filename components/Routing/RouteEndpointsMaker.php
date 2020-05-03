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
use Chevere\Components\Route\RouteEndpoints;
use Chevere\Components\Routing\Exceptions\ExpectingRouteDecoratorException;
use Chevere\Components\Routing\Interfaces\RouteEndpointIteratorInterface;
use Chevere\Components\Type\Type;
use function DeepCopy\deep_copy;

final class RouteEndpointsMaker implements RouteEndpointIteratorInterface
{
    private RouteEndpoints $routeEndpoints;

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
                throw new ExpectingRouteDecoratorException(
                    (new Message('Expecting file return object implementing interface %interfaceName%, type %provided% provided in %fileName%'))
                        ->code('%interfaceName%', RouteDecoratorInterface::class)
                        ->code('%provided%', gettype($routeEndpoint))
                        ->strong('%fileName%', $routeEndpointPath->absolute())
                        ->toString()
                );
            }
            $this->routeEndpoints->put($routeEndpoint);
        }
    }

    public function routeEndpointsMap(): RouteEndpoints
    {
        return deep_copy($this->routeEndpoints);
    }

    private function getVar(PathInterface $path)
    {
        return (
            new FilePhpReturn(new FilePhp(new File($path)))
        )->withStrict(false)->var();
    }
}
