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

namespace Chevere\Components\Api;

use Chevere\Components\Api\Interfaces\EndpointMethodInterface;
use Chevere\Components\Controller\Interfaces\ControllerInterface;
use Chevere\Components\Filesystem\Dir;
use Chevere\Components\Filesystem\Interfaces\Dir\DirInterface;
use Chevere\Components\Filesystem\Path;
use Chevere\Components\Http\Interfaces\MethodInterface;
use Chevere\Components\Http\Methods\GetMethod;
use Chevere\Components\Str\Str;
use ReflectionClass;

abstract class EndpointMethod implements EndpointMethodInterface
{
    private string $whereIs;

    private MethodInterface $method;

    private DirInterface $root;

    abstract public function controller(): ControllerInterface;

    final public function __construct()
    {
        $this->whereIs = (new ReflectionClass($this))->getFileName();
        $this->root = new Dir(new Path(__DIR__ . '/'));
        $this->handleSetPath();
        $name = basename($this->whereIs, '.php');
        $knownMethods = [
            'Get' => GetMethod::class,
        ];
        $this->method = new $knownMethods[$name];
    }

    final public function whereIs(): string
    {
        return $this->whereIs;
    }

    final public function root(): DirInterface
    {
        return $this->root;
    }

    final public function path(): string
    {
        return $this->path;
    }

    final public function method(): MethodInterface
    {
        return $this->method;
    }

    final public function withRootDir(DirInterface $root): EndpointMethodInterface
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
