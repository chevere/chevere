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
use RuntimeException;

use Chevere\Components\Http\ServerRequest;
use Chevere\Components\Message\Message;
use Chevere\Components\Router\Exception\RouteNotFoundException;
use Chevere\Contracts\App\BuilderContract;
use Chevere\Contracts\Controller\JsonApiContract;

use function console;
use function GuzzleHttp\Psr7\stream_for;

use const Chevere\CLI;

/**
 * Application runner.
 */
final class Run
{
    /** @var BuilderContract */
    private $builder;

    /** @var bool A boolean indicating if the run() method was called */
    private $ran;

    /** @var bool A boolean indicating if the console has looped */
    private $consoleLoop;

    /** @var string A string representing a ControllerContract name */
    private $controllerName;

    /** @var array */
    private $controllerArguments;

    public function __construct(BuilderContract $builder)
    {
        $this->builder = $builder;
    }

    public function withConsoleLoop(): Run
    {
        $new = clone $this;
        $new->consoleLoop = true;

        return $new;
    }

    /**
     * This method runs the application and when the context is CLI, it injects services from console commands.
     */
    public function run()
    {
        $this->handleConsole();
        $this->handleRequest();
        if (isset($this->ran)) {
            throw new LogicException(
                (new Message('The method %method% can be called just once'))
                    ->code('%method%', __METHOD__)
                    ->toString()
            );
        }
        $this->ran = true;
        $path = $this->builder->request()->getUri()->getPath();
        if ($this->builder->hasControllerName()) {
            $this->controllerName = $this->builder->controllerName();
            $this->controllerArguments = $this->builder->controllerArguments();
        } else {
            $this->resolveCallable($path);
        }
        $this->assertControllerName();
        $this->runApp();
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
        if (!$this->builder->hasRequest()) {
            $this->builder =  $this->builder
                ->withRequest(ServerRequest::fromGlobals());
        }
    }

    private function resolveCallable(string $pathInfo): void
    {
        $app = $this->builder->app();
        $request = $this->builder->request();
        try {
            $route = $app->router()->resolve($pathInfo);
            $app = $app
                ->withRoute($route);
        } catch (RouteNotFoundException $e) {
            $response = $app->response();
            $guzzle = $response->guzzle()
                ->withStatus(404)
                ->withBody(stream_for('Not found.'));
            $response = $response->withGuzzle($guzzle);
            $app = $app
                ->withResponse($response);
            if (CLI) {
                throw new RouteNotFoundException($e->getMessage());
            }
            $app->response()
                ->sendHeaders()
                ->sendBody();
            die();
        }
        $this->builder = $this->builder
            ->withApp($app);
        $this->controllerName = $app->route()
            ->getController($request->getMethod());
        $this->controllerArguments = $app->router()->arguments();
    }

    private function assertControllerName(): void
    {
        if (!isset($this->controllerName)) {
            throw new RuntimeException('DESCONTROL');
        }
    }

    private function runApp(): void
    {
        $app = $this->builder->app();
        $app = $app
            ->withArguments($this->controllerArguments);

        $runner = new ControllerRunner($app);
        $controller = $runner->run($this->controllerName);
        $contentStream = stream_for($controller->content());
        $response = $app->response();
        $guzzle = $response->guzzle();
        $response = $response->withGuzzle(
            $controller instanceof JsonApiContract
                ? $guzzle->withJsonApi($contentStream)
                : $guzzle->withBody($contentStream)
        );
        $app = $app
            ->withResponse($response);
        if (!CLI) {
            $app->response()
                ->sendHeaders()
                ->sendBody();
        }
    }
}
