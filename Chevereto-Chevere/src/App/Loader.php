<?php

declare(strict_types=1);

/*
 * This file is part of Chevere.
 *
 * (c) Rodolfo Berrios <rodolfo@chevereto.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Chevere\App;

use const Chevere\CLI;
use const Chevere\DEV;

use Chevere\Api\Api;
use Chevere\App\Exceptions\NeedsToBeBuiltException;
use Chevere\Cache\Exceptions\CacheNotFoundException;
use Chevere\Console\Console;
use Chevere\Contracts\App\BuildContract;
use Chevere\Contracts\App\LoaderContract;
use Chevere\Http\Response;
use Chevere\Message\Message;
use Chevere\Router\Router;
use Chevere\Contracts\App\ParametersContract;

final class Loader implements LoaderContract
{
    /** @var Builder */
    private $builder;

    /** @var ParametersContract */
    private $parameters;

    public function __construct()
    {
        $this->builder = new Builder(new App(new Response()));
        $this->handleConsoleBind();
        $this->assert();
        $this->handleParameters();
        $build = $this->getBuild();
        $this->builder = $this->builder
                ->withBuild($build);
        $this->builder = $this->builder
            ->withApp(
                $this->builder->app()
                    ->withRouter($this->builder->build()->container()->router())
            );
    }

    public function run(): void
    {
        $this->builder->run();
    }

    private function handleParameters(): void
    {
        if (DEV || Console::isBuilding()) {
            $this->parameters = Parameters::fromFile();
            $this->builder = $this->builder
                ->withParameters($this->parameters);
        }
    }

    private function getBuild(): BuildContract
    {
        if (DEV) {
            return $this->builder->build()
                ->withParameters($this->parameters);
        } else {
            try {
                if (Console::isBuilding()) {
                    $api = new Api();
                    $router = new Router();
                } else {
                    $api = Api::fromCache();
                    $router = Router::fromCache();
                }
                $container = $this->builder->build()->container()
                    ->withApi($api)
                    ->withRouter($router);
            } catch (CacheNotFoundException $e) {
                throw new NeedsToBeBuiltException(
                    (new Message('The app must be re-build due to missing cache: %message%'))
                        ->code('%message%', $e->getMessage()),
                    $e->getCode(),
                    $e
                );
            }
            return $this->builder->build()
                ->withContainer($container);
        }
    }

    private function handleConsoleBind(): void
    {
        if (CLI) {
            Console::bind($this->builder);
        }
    }

    private function assert(): void
    {
        if (!DEV && !Console::isBuilding() && !$this->builder->build()->exists()) {
            throw new NeedsToBeBuiltException(
                (new Message('The application needs to be built by CLI %command% or calling %method% method.'))
                    ->code('%command%', 'php app/console build')
                    ->code('%method%', __CLASS__ . '::' . 'build')
                    ->toString()
            );
        }
    }
}
