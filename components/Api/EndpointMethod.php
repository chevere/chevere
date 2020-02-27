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
use Chevere\Components\Filesystem\Interfaces\Path\PathInterface;
use Chevere\Components\Http\Interfaces\MethodInterface;
use Chevere\Components\Http\Interfaces\RequestInterface;
use Chevere\Components\Http\Methods\GetMethod;
use Chevere\Components\Route\PathUri;
use Chevere\Components\Str\Str;
use ReflectionClass;

abstract class EndpointMethod implements EndpointMethodInterface
{
    private RequestInterface $request;

    private string $whereIs;

    private MethodInterface $method;

    private PathInterface $root;

    private array $wildcards = [];

    abstract public function __invoke();

    public function setUp(): void
    {
    }

    public function tearDown(): void
    {
    }

    final public function __construct()
    {
        $this->whereIs = (new ReflectionClass($this))->getFileName();
        $name = basename($this->whereIs, '.php');
        $knownMethods = [
            'Get' => GetMethod::class,
        ];
        $this->method = new $knownMethods[$name];
        // $this->pathUri = new PathUri($pathUri);
        $this->setUp();
        $this->tearDown();
    }

    final public function whereIs(): string
    {
        return $this->whereIs;
    }

    final public function method(): MethodInterface
    {
        return $this->method;
    }

    final public function withRoot(PathInterface $root): EndpointMethodInterface
    {
        $new = clone $this;
        $new->root = $root;
        $pathUri = (string) (new Str(dirname($this->whereIs)))
            ->replaceFirst($root->absolute(), '');
        $new->pathUri = new PathUri($pathUri);

        return $new;
    }

    final public function hasRoot(): bool
    {
        return isset($this->root);
    }

    final public function root(): PathInterface
    {
        return $this->root;
    }

    // final public function wildcards(): array
    // {
    //     return $this->wildcards;
    // }

    final public function withRequest(RequestInterface $request): EndpointMethodInterface
    {
        $new = clone $this;
        $new->request = $request;

        return $new;
    }

    final public function hasRequest(): bool
    {
        return isset($this->request);
    }

    final public function request(): RequestInterface
    {
        return $this->request;
    }
}
