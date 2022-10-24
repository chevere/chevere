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

namespace Chevere\Controller;

use Chevere\Controller\Interfaces\HttpControllerInterface;
use Chevere\Controller\Interfaces\HttpMiddlewareInterface;
use Chevere\Parameter\Arguments;
use function Chevere\Parameter\fileParameters;
use Chevere\Parameter\Interfaces\ParametersInterface;
use function Chevere\Parameter\parameters;

abstract class HttpController extends Controller implements HttpControllerInterface
{
    /**
     * @var array<int|string, string>
     */
    protected array $get = [];

    /**
     * @var array<int|string, string>
     */
    protected array $post = [];

    /**
     * @var array<int|string, array<string, int|string>>
     */
    protected array $files = [];

    protected HttpMiddlewareInterface $middleware;

    public function acceptGet(): ParametersInterface
    {
        return parameters();
    }

    public function acceptPost(): ParametersInterface
    {
        return parameters();
    }

    public function acceptFiles(): ParametersInterface
    {
        return parameters();
    }

    final public function withGet(array $get): static
    {
        $new = clone $this;
        $arguments = new Arguments(
            $new->acceptGet(),
            ...$get
        );
        /** @var array<int|string, string> */
        $array = $arguments->toArray();
        $new->get = $array;

        return $new;
    }

    final public function withPost(array $post): static
    {
        $new = clone $this;
        $arguments = new Arguments(
            $new->acceptPost(),
            ...$post
        );
        /** @var array<int|string, string> */
        $array = $arguments->toArray();
        $new->post = $array;

        return $new;
    }

    final public function withFiles(array $files): static
    {
        $new = clone $this;
        $arguments = new Arguments(
            $new->acceptFiles(),
            ...$files
        );
        /** @var array<int|string, array<string, int|string>> */
        $array = $arguments->toArray();
        $required = fileParameters();
        foreach ($array as $file) {
            $arguments = new Arguments($required, ...$file);
        }
        $new->files = $array;

        return $new;
    }

    public function withMiddleware(HttpMiddlewareInterface $middleware): static
    {
        $new = clone $this;
        $new->middleware = $middleware;

        return $new;
    }

    public function middleware(): HttpMiddlewareInterface
    {
        return $this->middleware ??= new HttpMiddleware();
    }

    final public function get(): array
    {
        return $this->get;
    }

    final public function post(): array
    {
        return $this->post;
    }

    final public function files(): array
    {
        return $this->files;
    }
}
