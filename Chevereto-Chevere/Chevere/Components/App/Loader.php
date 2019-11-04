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

use Chevere\Components\Api\Api;
use Chevere\Components\App\Exceptions\BuildNeededException;
use Chevere\Components\ArrayFile\ArrayFile;
use Chevere\Components\Cache\Cache;
use Chevere\Components\Cache\Exceptions\CacheNotFoundException;
use Chevere\Components\Console\Console;
use Chevere\Components\Dir\Dir;
use Chevere\Components\Http\Response;
use Chevere\Components\Message\Message;
use Chevere\Components\Path\Path;
use Chevere\Components\Router\Maker;
use Chevere\Components\Router\Router;
use Chevere\Contracts\App\AppContract;
use Chevere\Contracts\App\BuildContract;
use Chevere\Contracts\App\BuilderContract;
use Chevere\Contracts\App\ServicesContract;
use Chevere\Contracts\App\LoaderContract;
use Chevere\Contracts\App\ParametersContract;

use function console;

use const Chevere\CLI;
use const Chevere\DEV;

/**
 * Loads the application, by handling its builder.
 */
final class Loader implements LoaderContract
{
    /** @var BuilderContract */
    private $builder;

    /** @var ParametersContract */
    private $parameters;

    public function __construct()
    {
        $app = new App(new Services(), new Response());
        $build = new Build($app);
        $this->builder = new Builder($build);
        $this->assertNeedsToBeBuilt();
        $this->parameters = new Parameters(
            new ArrayFile(
                new Path(AppContract::FILE_PARAMETERS)
            )
        );
        $this->handleParameters();
        $this->builder = $this->builder
            ->withBuild($this->getBuild());
        $app = $this->builder->build()->app()
            ->withServices(
                $this->builder->build()->app()->services()
            );
        $this->builder = $this->builder
            ->withBuild(
                $this->builder->build()
                    ->withApp($app)
            );
    }

    public function run(): void
    {
        $runner = new Runner($this->builder);
        $runner = $runner->withRun();
        $this->builder = $runner->builder();
        $this->handleResponse();
    }

    private function handleResponse(): void
    {
        if (!headers_sent()) {
            $this->builder->build()->app()->response()
                ->sendHeaders();
        }
        $this->builder->build()->app()->response()
            ->sendBody();
    }

    private function handleParameters(): void
    {
        if (DEV || (CLI && console()->isBuilding())) {
            $path = new Path('plugins/local/HelloWorld/routes/web.php');
            $pluginRoutes = [$path];
            $this->parameters = $this->parameters
                ->withAddedRoutePaths(...$pluginRoutes);
            if ($this->parameters->hasParameters()) {
                $this->builder = $this->builder
                    ->withBuild(
                        $this->builder->build()
                            ->withParameters($this->parameters)
                    );
            }
        }
    }

    /**
     * While in DEV mode, it calls make() on top of build() so the application is re-built in every request.
     * On production, it returns a BuildContrar with a Container instance.
     */
    private function getBuild(): BuildContract
    {
        if (DEV) {
            return $this->builder->build()
                ->withRouterMaker(new Maker())
                ->make();
        }
        $build = $this->builder->build();
        $app = $build->app()
            ->withServices($this->getContainer());
        $build = $build
            ->withApp($app);

        return $build;
    }

    /**
     * Return a ServicesContract containing the services required to provide the application.
     */
    private function getContainer(): ServicesContract
    {
        $api = new Api();
        $router = new Router();
        $services = $this->builder->build()->app()->services();

        try {
            if (!(CLI && console()->isBuilding())) {
                $dir = $this->builder->build()->cacheDir();
                if ($this->parameters->hasApi()) {
                    $api = $api->withCache(new Cache('api', $dir));
                }
                $router = $router->withCache(new Cache('router', $dir));
            }
            if ($this->parameters->hasApi()) {
                $services = $services->withApi($api);
            }

            return $services
                ->withRouter($router);
        } catch (CacheNotFoundException $e) {
            $message = (new Message('The app must be re-build due to missing cache: %message%'))
                ->strtr('%message%', $e->getMessage())
                ->toString();
            throw new BuildNeededException($message, $e->getCode(), $e);
        }
    }

    private function assertNeedsToBeBuilt(): void
    {
        if (
            !DEV
            && !(CLI && console()->isBuilding())
            && !$this->builder->build()->file()->exists()
        ) {
            throw new BuildNeededException(
                (new Message('The application needs to be built by CLI %command% or calling %method% method'))
                    ->code('%command%', 'php app/console build')
                    ->code('%method%', __CLASS__ . '::build')
                    ->toString()
            );
        }
    }
}
