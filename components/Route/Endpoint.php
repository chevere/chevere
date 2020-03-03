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

namespace Chevere\Components\Route;

use Chevere\Components\Api\Interfaces\EndpointInterface;
use Chevere\Components\Controller\Interfaces\ControllerInterface;
use Chevere\Components\Filesystem\Dir;
use Chevere\Components\Filesystem\Interfaces\Dir\DirInterface;
use Chevere\Components\Filesystem\Path;
use Chevere\Components\Http\Interfaces\MethodInterface;
use Chevere\Components\Message\Message;
use Chevere\Components\Route\Exceptions\EndpointException;
use Chevere\Components\Route\Interfaces\WildcardCollectionInterface;
use Chevere\Components\Str\Str;
use ReflectionClass;

abstract class Endpoint implements EndpointInterface
{
    private string $whereIs;

    private MethodInterface $method;

    private DirInterface $root;

    private WildcardCollectionInterface $wildcards;

    abstract public function controller(): ControllerInterface;

    final public function __construct()
    {
        $this->whereIs = (new ReflectionClass($this))->getFileName();
        $dirWhereIs = dirname($this->whereIs);
        $this->root = new Dir(new Path($dirWhereIs . '/'));
        $name = basename($this->whereIs, '.php');
        $method = self::KNOWN_METHODS[$name] ?? null;
        if ($method === null) {
            throw new EndpointException(
                (new Message('Unknown method name %provided% provided (inherithed from %basename%)'))
                    ->code('%provided%', $name)
                    ->code('%basename%', basename($this->whereIs))
                    ->toString()
            );
        }
        $this->handleSetPath();
        $this->method = new $method;
        $this->wildcards = new WildcardCollection();
    }

    final public function whereIs(): string
    {
        return $this->whereIs;
    }

    final public function path(): string
    {
        return $this->path;
    }

    final public function method(): MethodInterface
    {
        return $this->method;
    }

    public function wildcards(): WildcardCollectionInterface
    {
        return $this->wildcards;
    }

    final public function withRootDir(DirInterface $root): EndpointInterface
    {
        $new = clone $this;
        $new->root = $root;
        $new->handleSetPath();

        return $new;
    }

    private function handleSetPath(): void
    {
        $this->path = (string) (new Str(dirname($this->whereIs)))
            ->replaceFirst(
                rtrim($this->root->path()->absolute(), '/'),
                ''
            );
    }
}
