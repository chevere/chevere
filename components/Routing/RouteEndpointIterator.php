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
use Chevere\Components\Routing\Interfaces\RouteEndpointIteratorInterface;
use Chevere\Components\Type\Type;
use LogicException;
use SplObjectStorage;

final class RouteEndpointIterator implements RouteEndpointIteratorInterface
{
    private RouteEndpointObjects $objects;

    public function __construct(DirInterface $dir)
    {
        $this->objects = new RouteEndpointObjects();
        $path = $dir->path();
        foreach (array_keys(RouteEndpointInterface::KNOWN_METHODS) as $name) {
            $endpointPath = $path->getChild($name . '.php');
            if (!$endpointPath->exists()) {
                continue;
            }
            $endpoint = $this->getVar($endpointPath);
            if (!(new Type(RouteEndpointInterface::class))->validate($endpoint)) {
                throw new LogicException(
                    (new Message('Expecting file return implementing interface %interfaceName%, type %provided% provided'))
                        ->code('%expected%', RouteDecoratorInterface::class)
                        ->code('%provided%', gettype($endpoint))
                        ->toString()
                );
            }
            $this->objects->attach($endpoint);
        }
    }

    public function objects(): RouteEndpointObjects
    {
        return $this->objects;
    }

    private function getVar(PathInterface $path)
    {
        return (
            new PhpFileReturn(new PhpFile(new File($path)))
        )->withStrict(false)->var();
    }
}
