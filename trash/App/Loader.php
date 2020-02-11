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

use Chevere\Components\Api\Api;
use Chevere\Components\App\Exceptions\BuildNeededException;
use Chevere\Components\Instances\BootstrapInstance;
use Chevere\Components\ArrayFile\ArrayFile;
use Chevere\Components\Cache\Exceptions\CacheNotFoundException;
use Chevere\Components\Filesystem\File;
use Chevere\Components\Filesystem\PhpFile;
use Chevere\Components\Http\Response;
use Chevere\Components\Message\Message;
use Chevere\Components\Filesystem\AppPath;
use Chevere\Components\Router\RouterMaker;
use Chevere\Components\Router\Router;
use Chevere\Components\App\Interfaces\AppInterface;
use Chevere\Components\App\Interfaces\BuildInterface;
use Chevere\Components\App\Interfaces\BuilderInterface;
use Chevere\Components\App\Interfaces\ServicesInterface;
use Chevere\Components\App\Interfaces\LoaderInterface;
use Chevere\Components\App\Interfaces\ParametersInterface;
use Chevere\Components\Router\RouterCache;

/**
 * Loads the application, by handling its builder.
 */
final class Loader implements LoaderInterface
{
    private BuilderInterface $builder;

    private ParametersInterface $parameters;

    public function __construct()
    {
        $app = new App(new Services(), new Response());
        $build = new Build($app);
        $this->builder = new Builder($build);
        $this->assertNeedsToBeBuilt();
        $this->parameters =
            new Parameters(
                new ArrayFile(
                    new PhpFile(
                        new File(
                            new AppPath(AppInterface::FILE_PARAMETERS)
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
        if (
            BootstrapInstance::get()->isDev() ||
            (BootstrapInstance::get()->hasConsole() && BootstrapInstance::get()->console()->isBuilding())
        ) {
            $path = new AppPath('plugins/local/HelloWorld/routes/web.php');
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
     * While in dev mode, call make() on top of build() so the application is re-built in every request.
     * On production, it returns a Build with a Container instance.
     */
    private function getBuild(): BuildInterface
    {
        if (BootstrapInstance::get()->isDev()) {
            return $this->builder->build()
                ->make();
        }
        $build = $this->builder->build();
        $app = $build->app()
            ->withServices($this->getServices());

        return $build
            ->withApp($app);
    }

    /**
     * Return a ServicesInterface containing the services required to provide the application.
     */
    private function getServices(): ServicesInterface
    {
        if (BootstrapInstance::get()->hasConsole() && BootstrapInstance::get()->console()->isBuilding()) {
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
            !BootstrapInstance::get()->isDev()
            && !(BootstrapInstance::get()->hasConsole() && BootstrapInstance::get()->console()->isBuilding())
            && !$this->builder->build()->file()->exists()
        ) {
            $command = 'php ' . $_SERVER['SCRIPT_FILENAME'] . ' build';
            $message = (new Message('The application needs to be built by running %command%'))
                ->code('%command%', $command)
                ->toString();
            if (BootstrapInstance::get()->hasConsole()) {
                BootstrapInstance::get()->console()->style()->block($message, 'CANNOT EXECUTE');
                die(126);
            }
            throw new BuildNeededException($message);
        }
    }
}
