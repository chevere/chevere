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
use Chevere\Components\Cache\Exceptions\CacheNotFoundException;
use Chevere\Components\File\File;
use Chevere\Components\File\FilePhp;
use Chevere\Components\Http\Response;
use Chevere\Components\Message\Message;
use Chevere\Components\Path\Path;
use Chevere\Components\Router\RouterMaker;
use Chevere\Components\Router\Router;
use Chevere\Contracts\App\AppContract;
use Chevere\Contracts\App\BuildContract;
use Chevere\Contracts\App\BuilderContract;
use Chevere\Contracts\App\ServicesContract;
use Chevere\Contracts\App\LoaderContract;
use Chevere\Contracts\App\ParametersContract;
use function console;
use const Chevere\CONSOLE;
use const Chevere\DEV;

/**
 * Loads the application, by handling its builder.
 */
final class Loader implements LoaderContract
{
    private BuilderContract $builder;

    private ParametersContract $parameters;

    public function __construct()
    {
        $app = new App(new Services(), new Response());
        $build = new Build($app, new Path('build'));
        $this->builder = new Builder($build);
        $this->assertNeedsToBeBuilt();
        $this->parameters =
            new Parameters(
                new ArrayFile(
                    new FilePhp(
                        new File(
                            new Path(AppContract::FILE_PARAMETERS)
                        )
                    )
                )
            );
        $this->handleParameters();
        $this->builder = $this->builder
            ->withBuild($this->getBuild());
    }

    public function run(): void
    {
        $runner = new Runner($this->builder);
        $this->builder = $runner->withRun()->builder();
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
        if (DEV || (CONSOLE && console()->isBuilding())) {
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
                ->withRouterMaker(new RouterMaker())
                ->make();
        }
        $build = $this->builder->build();
        $app = $build->app()
            ->withServices($this->getServices());
        $build = $build
            ->withApp($app);

        return $build;
    }

    /**
     * Return a ServicesContract containing the services required to provide the application.
     */
    private function getServices(): ServicesContract
    {
        if (CONSOLE && console()->isBuilding()) {
            return (new Services())
                ->withApi(new Api())
                ->withRouter(new Router());
        }
        try {
            return (new ServicesBuilder($this->builder->build(), $this->parameters))
                ->services();
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
            && !(CONSOLE && console()->isBuilding())
            && !$this->builder->build()->file()->exists()
        ) {
            $command = 'php ' . $_SERVER['SCRIPT_FILENAME'] . ' build';
            $message = (new Message('The application needs to be built by running %command%'))
                ->code('%command%', $command)
                ->toString();
            if (CONSOLE) {
                console()->style()->block($message, 'CANNOT EXECUTE');
                die(126);
            }
            throw new BuildNeededException($message);
        }
    }
}
