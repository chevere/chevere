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
use Chevere\Components\Filesystem\Interfaces\Dir\DirInterface;
use Chevere\Components\Filesystem\Interfaces\Path\PathInterface;
use Chevere\Components\Filesystem\PhpFile;
use Chevere\Components\Filesystem\PhpFileReturn;
use Chevere\Components\Message\Message;
use Chevere\Components\Route\Interfaces\RouteDecoratorInterface;
use Chevere\Components\Route\Interfaces\RouteEndpointInterface;
use Chevere\Components\Routing\Exceptions\ExpectingRouteDecoratorException;
use Chevere\Components\Routing\Interfaces\RouteEndpointIteratorInterface;
use Chevere\Components\Type\Type;
use SplObjectStorage;

final class RouteEndpointIterator implements RouteEndpointIteratorInterface
{
    private SplObjectStorage $objects;

    public function __construct(DirInterface $dir)
    {
        $this->objects = new SplObjectStorage();
        $path = $dir->path();
        foreach (array_keys(RouteEndpointInterface::KNOWN_METHODS) as $name) {
            $endpointPath = $path->getChild($name . '.php');
            if (!$endpointPath->exists()) {
                continue;
            }
            $endpoint = $this->getVar($endpointPath);
            if (!(new Type(RouteEndpointInterface::class))->validate($endpoint)) {
                throw new ExpectingRouteDecoratorException(
                    (new Message('Expecting file return object implementing interface %interfaceName%, something else provided in %fileName%'))
                        ->code('%interfaceName%', RouteDecoratorInterface::class)
                        ->code('%provided%', gettype($endpoint))
                        ->strong('%fileName%', $endpointPath->absolute())
                        ->toString()
                );
            }
            $this->objects->attach($endpoint);
        }
    }

    public function routeEndpointObjects(): RouteEndpointObjectsRead
    {
        return new RouteEndpointObjectsRead($this->objects);
    }

    private function getVar(PathInterface $path)
    {
        return (
            new PhpFileReturn(new PhpFile(new File($path)))
        )->withStrict(false)->var();
    }
}
