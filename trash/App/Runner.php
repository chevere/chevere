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

namespace Chevere\Components\App;

use LogicException;
use Chevere\Components\App\Exceptions\ResolverException;
use Chevere\Components\Instances\BootstrapInstance;
use Chevere\Components\Http\Request\RequestException;
use Chevere\Components\Http\Request;
use Chevere\Components\Message\Message;
use Chevere\Components\App\Interfaces\BuilderInterface;
use Chevere\Components\App\Interfaces\RunnerInterface;
use function GuzzleHttp\Psr7\stream_for;

/**
 * Application builder runner.
 */
final class Runner implements RunnerInterface
{
    private BuilderInterface $builder;

    /** @var bool A boolean indicating if the run() method was called */
    private bool $ran;

    /** @var string A string representing a ControllerInterface name */
    private string $controllerName;

    private array $controllerArguments;

    private ?bool $consoleLoop;

    /**
     * Creates a new instance.
     *
     * @param BuilderInterface $builder The builder to run.
     */
    public function __construct(BuilderInterface $builder)
    {
        $this->builder = $builder;
    }

    public function builder(): BuilderInterface
    {
        return $this->builder;
    }

    public function withConsoleLoop(): RunnerInterface
    {
        $new = clone $this;
        $new->consoleLoop = true;

        return $new;
    }

    public function hasConsoleLoop(): bool
    {
        return isset($this->consoleLoop);
    }

    public function withRun(): RunnerInterface
    {
        $new = clone $this;
        $new->handleConsole();
        $new->handleRequest();
        $new->handeRan();
        $new->ran = true;
        if (!$new->builder->hasControllerName()) {
            try {
                $new->handleResolver();
            } catch (ResolverException $e) {
                return $new;
            }
        }
        $new->controllerName = $new->builder->controllerName();
        $new->controllerArguments = $new->builder->controllerArguments();

        return $new->runApp();
    }

    /**
     * Must run before handleRequest(), otherwise CLI won't be capable of injecting the RequestInterface instance.
     */
    private function handleConsole(): void
    {
        if (BootstrapInstance::get()->hasConsole() && !isset($this->consoleLoop)) {
            BootstrapInstance::get()->console()->bind($this->builder);
            BootstrapInstance::get()->console()->run();
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
            $resolvable = new Resolvable($this->builder);
            $this->builder = (new Resolver($resolvable))
                ->builder();
        } catch (ResolverException $e) {
            $build = $this->builder->build();
            $response = $build->app()->response();
            $guzzle = $response->guzzle()
                ->withStatus($e->getCode());
            // ->withBody(stream_for('Not found.'));
            $response = $response
                ->withGuzzle($guzzle);
            $app = $build->app()
                ->withResponse($response);
            $this->builder = $this->builder
                ->withBuild(
                    $build->withApp($app)
                );
            throw new ResolverException($e->getMessage(), $e->getCode());
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
                // $controller instanceof JsonApiInterface
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
