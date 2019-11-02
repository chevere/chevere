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
use Chevere\Components\App\Exceptions\MiddlewareNamesEmptyException;
use Chevere\Components\Message\Message;
use Chevere\Components\Route\MiddlewareName;
use Chevere\Components\Route\MiddlewareNames;
use Chevere\Contracts\App\AppContract;
use Chevere\Contracts\App\MiddlewareRunnerContract;
use Chevere\Contracts\Http\RequestContract;

final class MiddlewareRunner implements MiddlewareRunnerContract
{
    /** @var AppContract */
    private $app;

    /** @var MiddlewareNames */
    private $middlewareNames;

    /** @var bool */
    private $hasRun;

    /** @var array An array containg the middlewares that have ran */
    private $record;

    /**
     * @param MiddlewareNames $middlewareNames An instance containing at least one middleware
     * @param AppContract $app An application container with a RequestContract.
     */
    public function __construct(MiddlewareNames $middlewareNames, AppContract $app)
    {
        $this->app = $app;
        $this->assertAppWithRequest();
        $this->middlewareNames = $middlewareNames;
        $this->assertMiddlewareNamesNotEmpty();
        $this->hasRun = false;
    }

    public function withRun(): MiddlewareRunnerContract
    {
        $new = clone $this;
        $new->doRun();
        $new->hasRun = true;

        return $new;
    }

    public function hasRun(): bool
    {
        return $this->hasRun;
    }

    public function record(): array
    {
        return $this->record;
    }

    private function doRun(): void
    {
        foreach ($this->middlewareNames->toArray() as $middleware) {
            (new $middleware())
                ->handle(
                    $this->app->request()
                );
            $this->record[] = $middleware;
        }
    }

    private function assertMiddlewareNamesNotEmpty(): void
    {
        if (!$this->middlewareNames->hasAny()) {
            throw new MiddlewareNamesEmptyException(
                (new Message("Instance of class %className% doesn't contain any %contract% contract"))
                    ->code('%className%', MiddlewareNames::class)
                    ->code('%contract%', MiddlewareName::class)
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
