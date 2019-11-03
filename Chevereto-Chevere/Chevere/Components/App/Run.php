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

use Chevere\Components\App\Exceptions\RequestContractRequiredException;
use Chevere\Components\Http\Request\RequestException;
use LogicException;
use RuntimeException;

use Chevere\Components\Http\Request;
use Chevere\Components\Message\Message;
use Chevere\Components\Router\Exception\RouteNotFoundException;
use Chevere\Contracts\App\BuilderContract;
use Chevere\Contracts\App\RunContract;
use Chevere\Contracts\Http\RequestContract;

use function console;
use function GuzzleHttp\Psr7\stream_for;

use const Chevere\CLI;

/**
 * Application runner.
 */
final class Run implements RunContract
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

    /**
     * {@inheritdoc}
     */
    public function run(): void
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
        $path = $this->builder->build()->app()->request()->getUri()->getPath();
        if ($this->builder->hasControllerName()) {
            $this->controllerName = $this->builder->controllerName();
            $this->controllerArguments = $this->builder->controllerArguments();
        } else {
            $this->resolveCallable($path);
        }
        $this->assertControllerName();
        $this->runApp();
    }

    private function assertBuilderAppServicesRouter(): void
    {
        if (!$this->builder->build()->app()->services()->hasRouter()) {
            throw new RequestContractRequiredException(
                (new Message('Instance of class %className% must contain a %contract% contract'))
                    ->code('%className%', get_class($this->builder->build()->app()))
                    ->code('%contract%', RequestContract::class)
                    ->toString()
            );
        }
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

    private function resolveCallable(string $pathInfo): void
    {
        $this->assertBuilderAppServicesRouter();
        $app = $this->builder->build()->app();
        try {
            $route = $app->services()->router()->resolve($pathInfo);
            $app = $app
                ->withRoute($route);
            $this->controllerName = $app->route()
                ->getController($app->request()->getMethod());
            $this->controllerArguments = $app->services()->router()->arguments();
        } catch (RouteNotFoundException $e) {
            $response = $app->response();
            $guzzle = $response->guzzle()
                ->withStatus(404)
                ->withBody(stream_for('Not found.'));
            $response = $response
                ->withGuzzle($guzzle);
            $app = $app
                ->withResponse($response);
        }
        $this->builder = $this->builder
            ->withBuild(
                $this->builder->build()
                    ->withApp($app)
            );

        if (isset($response)) {
            if (CLI) {
                throw new RouteNotFoundException();
            }
            $response = $this->builder->build()->app()->response();
            if (!headers_sent()) {
                $response
                    ->sendHeaders();
            }
            $response
                ->sendBody();
            die();
        }
    }

    private function assertControllerName(): void
    {
        if (!isset($this->controllerName)) {
            throw new RuntimeException('DESCONTROL');
        }
    }

    private function runApp(): void
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
        if (CLI) {
            throw new RouteNotFoundException();
        } else {
            $this->builder->build()->app()->response()
                ->sendHeaders()
                ->sendBody();
        }
    }
}
