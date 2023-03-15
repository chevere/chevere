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

namespace Chevere\HttpController;

use Chevere\Controller\Controller;
use Chevere\Http\Interfaces\MiddlewaresInterface;
use Chevere\Http\Middlewares;
use Chevere\HttpController\Interfaces\HttpControllerInterface;
use Chevere\HttpController\Traits\StatusInternalServerErrorTrait;
use Chevere\HttpController\Traits\StatusOkTrait;
use Chevere\Parameter\Arguments;
use function Chevere\Parameter\arrayParameter;
use Chevere\Parameter\Interfaces\ArrayParameterInterface;
use Chevere\Parameter\Interfaces\FileParameterInterface;
use Chevere\Parameter\Interfaces\ParametersInterface;
use function Chevere\Parameter\parameters;

abstract class HttpController extends Controller implements HttpControllerInterface
{
    use StatusOkTrait;
    use StatusInternalServerErrorTrait;

    /**
     * @var array<int|string, string>
     */
    protected array $query = [];

    /**
     * @var array<int|string, string>
     */
    protected array $body = [];

    /**
     * @var array<int|string, array<string, int|string>>
     */
    protected array $files = [];

    protected MiddlewaresInterface $middlewares;

    public function acceptQuery(): ParametersInterface
    {
        return parameters();
    }

    public function acceptBody(): ParametersInterface
    {
        return parameters();
    }

    public function acceptFiles(): ArrayParameterInterface
    {
        return arrayParameter();
    }

    final public function withQuery(array $query): static
    {
        $new = clone $this;
        $arguments = new Arguments(
            $new->acceptQuery(),
            $query
        );
        /** @var array<int|string, string> */
        $array = $arguments->toArray();
        $new->query = $array;

        return $new;
    }

    final public function withBody(array $body): static
    {
        $new = clone $this;
        $arguments = new Arguments(
            $new->acceptBody(),
            $body
        );
        /** @var array<int|string, string> */
        $array = $arguments->toArray();
        $new->body = $array;

        return $new;
    }

    final public function withFiles(array $files): static
    {
        $new = clone $this;
        $array = [];
        /** @var FileParameterInterface $parameter */
        foreach ($new->acceptFiles()->parameters() as $key => $parameter) {
            $arguments = new Arguments(
                $parameter->parameters(),
                $files[$key]
            );
            /** @var array<int|string, array<string, int|string>> $array */
            $array[$key] = $arguments->toArray();
        }
        $new->files = $array;

        return $new;
    }

    public function withMiddlewares(MiddlewaresInterface $middleware): static
    {
        $new = clone $this;
        $new->middlewares = $middleware;

        return $new;
    }

    public function middlewares(): MiddlewaresInterface
    {
        return $this->middlewares ??= new Middlewares();
    }

    /**
     * @deprecated
     */
    public function middleware(): MiddlewaresInterface
    {
        return $this->middlewares();
    }

    /**
     * @deprecated
     */
    public function withMiddleware(MiddlewaresInterface $middleware): static
    {
        $new = clone $this;

        return $new->withMiddlewares($middleware);
    }

    final public function get(): array
    {
        return $this->query;
    }

    final public function post(): array
    {
        return $this->body;
    }

    final public function files(): array
    {
        return $this->files;
    }
}
