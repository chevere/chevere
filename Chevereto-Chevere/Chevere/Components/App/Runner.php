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

use LogicException;

use Chevere\Components\Http\Request\RequestException;
use Chevere\Components\Http\Request;
use Chevere\Components\Message\Message;
use Chevere\Components\Router\Exception\RouteNotFoundException;
use Chevere\Contracts\App\BuilderContract;
use Chevere\Contracts\App\RunContract;

use function console;
use function GuzzleHttp\Psr7\stream_for;

use const Chevere\CLI;

/**
 * Application builder runner.
 */
final class Runner implements RunContract
{
    /** @var BuilderContract */
    private $builder;

    /** @var bool A boolean indicating if the run() method was called */
    private $ran;

    /** @var bool A boolean indicating if the console has looped */
    private $consoleLoop;

    /** @var bool A boolean indicating if no route was found */
    private $routeNotFound;

    /** @var string A string representing a ControllerContract name */
    private $controllerName;

    /** @var array */
    private $controllerArguments;

    /**
     * {@inheritdoc}
     */
    public function __construct(BuilderContract $builder)
    {
        $this->builder = $builder;
    }

    public function builder(): BuilderContract
    {
        return $this->builder;
    }

    /**
     * {@inheritdoc}
     */
    public function withConsoleLoop(): RunContract
    {
        $new = clone $this;
        $new->consoleLoop = true;

        return $new;
    }

    public function hasConsoleLoop(): bool
    {
        return isset($this->consoleLoop);
    }

    public function hasRouteNotFound(): bool
    {
        return isset($this->routeNotFound);
    }

    /**
     * {@inheritdoc}
     */
    public function withRun(): RunContract
    {
        $new = clone $this;
        $new->handleConsole();
        $new->handleRequest();
        $new->handeRan();
        $new->ran = true;
        if (!$new->builder->hasControllerName()) {
            try {
                $new->handleResolver();
            } catch (RouteNotFoundException $e) {
                $new->routeNotFound = true;

                return $new;
            }
        }
        $new->controllerName = $new->builder->controllerName();
        $new->controllerArguments = $new->builder->controllerArguments();

        return $new->runApp();
    }

    /**
     * Must run before handleRequest(), otherwise CLI won't be capable of injecting the RequestContract instance.
     */
    private function handleConsole(): void
    {
        if (CLI && !isset($this->consoleLoop)) {
            console()->bind($this->builder);
            console()->run();
        }
    }

    private function handleRequest(): void
    {
        if (!$this->builder->build()->app()->hasRequest()) {
            $this->builder = $this->builder
                ->withBuild(
                    $this->builder->build()
                        ->withApp(
                            $this->builder->build()->app()
                                ->withRequest(Request::fromGlobals())
                        )
                );
        }
    }

    private function handeRan(): void
    {
        if (isset($this->ran)) {
            throw new LogicException(
                (new Message('The method %method% can be called just once'))
                    ->code('%method%', 'run')
                    ->toString()
            );
        }
    }

    private function handleResolver(): void
    {
        try {
            $this->builder = (new Resolver($this->builder))
                ->builder();
        } catch (RouteNotFoundException $e) {
            $app = $this->builder->build()->app();
            $response = $app->response();
            $guzzle = $response->guzzle()
                ->withStatus(404)
                ->withBody(stream_for('Not found.'));
            $response = $response
                ->withGuzzle($guzzle);
            $app = $app
                ->withResponse($response);
            $this->builder = $this->builder
                ->withBuild(
                    $this->builder->build()
                        ->withApp($app)
                );
            throw new RouteNotFoundException();
        }
    }

    private function runApp(): Runner
    {
        $app = $this->builder->build()->app();
        $app = $app
            ->withArguments($this->controllerArguments);
        $response = $app->response();
        $guzzle = $response->guzzle();
        try {
            $runner = new ControllerRunner($app);
            $controller = $runner->run($this->controllerName);
            $content = $controller->content();
        } catch (RequestException $e) {
            $content = $e->getMessage();
            $guzzle = $guzzle
                ->withStatus($e->getCode());
        }
        $contentStream = stream_for($content);
        $response = $response->withGuzzle(
            1 > 2
                // $controller instanceof JsonApiContract
                ? $guzzle->withJsonApi($contentStream)
                : $guzzle->withBody($contentStream)
        );
        $response = $response->withGuzzle(
            $guzzle
                ->withBody($contentStream)
        );
        $app = $app
            ->withResponse($response);
        $this->builder = $this->builder
            ->withBuild(
                $this->builder->build()
                    ->withApp($app)
            );

        return $this;
    }
}
