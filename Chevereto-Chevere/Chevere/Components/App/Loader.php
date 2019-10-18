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
use Chevere\Components\App\Exceptions\NeedsToBeBuiltException;
use Chevere\Components\ArrayFile\ArrayFile;
use Chevere\Components\Cache\Cache;
use Chevere\Components\Cache\Exceptions\CacheNotFoundException;
use Chevere\Components\Console\Console;
use Chevere\Components\Http\Response;
use Chevere\Components\Message\Message;
use Chevere\Components\Path\Path;
use Chevere\Components\Router\Router;
use Chevere\Contracts\App\BuildContract;
use Chevere\Contracts\App\BuilderContract;
use Chevere\Contracts\App\ContainerContract;
use Chevere\Contracts\App\LoaderContract;
use Chevere\Contracts\App\ParametersContract;
use LogicException;

use function console;

use const Chevere\CLI;
use const Chevere\DEV;

/**
 * Loader is responsible of booting up the application.
 */
final class Loader implements LoaderContract
{
    /** @var BuilderContract */
    private $builder;

    /** @var ParametersContract */
    private $parameters;

    public function __construct()
    {
        $app = (new App())
            ->withResponse(new Response());
        $this->builder = new Builder($app);
        $this->assertIsBuilt();
        $this->parameters = new Parameters(
            new ArrayFile(
                new Path(App::FILE_PARAMETERS)
            )
        );
        $this->handleParameters();
        $this->builder = $this->builder
            ->withBuild($this->getBuild());
        $app = $this->builder->app()
            ->withRouter(
                $this->builder->build()->container()->router()
            );
        $this->builder = $this->builder
            ->withApp($app);
    }

    public function run(): void
    {
        $this->builder->run();
    }

    private function handleParameters(): void
    {
        if (DEV || (CLI && console()->isBuilding())) {
            $path = new Path('/home/rodolfo/git/chevere/app/plugins/local/HelloWorld/routes/web.php');
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

    private function getBuild(): BuildContract
    {
        if (DEV) {
            return $this->builder->build()
                ->withParameters($this->parameters)
                ->withBuilt();
        }
        return $this->builder->build()
            ->withContainer($this->getContainer());
    }

    private function getContainer(): ContainerContract
    {
        $api = new Api();
        $router = new Router();
        try {
            if (!(CLI && console()->isBuilding())) {
                $path = new Path('build');
                if ($this->parameters->hasApi()) {
                    $api = $api->withCache(new Cache('api', $path));
                }
                $router = $router->withCache(new Cache('router', $path));
            }
            $container = $this->builder->build()->container()
                ->withRouter($router);
            if ($this->parameters->hasApi()) {
                $container = $container->withApi($api);
            }
            return $container;
        } catch (CacheNotFoundException $e) {
            $message = (new Message('The app must be re-build due to missing cache: %message%'))
                ->strtr('%message%', $e->getMessage())
                ->toString();
            throw new NeedsToBeBuiltException($message, $e->getCode(), $e);
        }
    }

    private function assertIsBuilt(): void
    {
        if (
            !DEV
            && !(CLI && console()->isBuilding())
            && !$this->builder->build()->path()->exists()
        ) {
            throw new NeedsToBeBuiltException(
                (new Message('The application needs to be built by CLI %command% or calling %method% method'))
                    ->code('%command%', 'php app/console build')
                    ->code('%method%', __CLASS__ . '::build')
                    ->toString()
            );
        }
    }
}
