<?php

/*
 * This file is part of Chevere.
 *
 * (c) Rodolfo Berrios <rodolfo@chevereto.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Chevere\Components\App;

use Chevere\Components\App\Exceptions\AppWithoutRequestException;
use Chevere\Components\Middleware\Exceptions\MiddlewareNamesEmptyException;
use Chevere\Components\Message\Message;
use Chevere\Contracts\App\AppContract;
use Chevere\Contracts\App\MiddlewareRunnerContract;
use Chevere\Components\Http\Contracts\RequestContract;
use Chevere\Contracts\Middleware\MiddlewareNameCollectionContract;
use Chevere\Contracts\Middleware\MiddlewareNameContract;

final class MiddlewareRunner implements MiddlewareRunnerContract
{
    private AppContract $app;

    private MiddlewareNameCollectionContract $middlewareNameCollection;

    /** @var bool */
    private bool $hasRun;

    /** @var array An array containg the middlewares that have ran */
    private array $record;

    public function __construct(MiddlewareNameCollectionContract $middlewareNameCollection, AppContract $app)
    {
        $this->app = $app;
        $this->assertAppWithRequest();
        $this->middlewareNameCollection = $middlewareNameCollection;
        $this->assertMiddlewareNamesNotEmpty();
        $this->hasRun = false;
    }

    /**
     * {@inheritdoc}
     */
    public function withRun(): MiddlewareRunnerContract
    {
        $new = clone $this;
        $new->doRun();
        $new->hasRun = true;

        return $new;
    }

    /**
     * {@inheritdoc}
     */
    public function hasRun(): bool
    {
        return $this->hasRun;
    }

    /**
     * {@inheritdoc}
     */
    public function record(): array
    {
        return $this->record;
    }

    private function doRun(): void
    {
        foreach ($this->middlewareNameCollection->toArray() as $middlewareName) {
            $middleware = $middlewareName->toString();
            (new $middleware())
                ->handle(
                    $this->app->request()
                );
            $this->record[] = $middleware;
        }
    }

    private function assertMiddlewareNamesNotEmpty(): void
    {
        if (!$this->middlewareNameCollection->hasAny()) {
            throw new MiddlewareNamesEmptyException(
                (new Message("Instance of %className% doesn't contain any %contract% contract"))
                    ->code('%className%', MiddlewareNameCollectionContract::class)
                    ->code('%contract%', MiddlewareNameContract::class)
                    ->toString()
            );
        }
    }

    private function assertAppWithRequest(): void
    {
        if (!$this->app->hasRequest()) {
            throw new AppWithoutRequestException(
                (new Message('Instance of %type% must contain a %contract% contract'))
                    ->code('%type%', AppContract::class)
                    ->code('%contract%', RequestContract::class)
                    ->toString()
            );
        }
    }
}
