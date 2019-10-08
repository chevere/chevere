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
use Chevere\Contracts\App\LoaderContract;
use Chevere\Http\Response;
use Chevere\Message\Message;
use Chevere\Router\Router;

final class Loader implements LoaderContract
{
    /** @var Builder */
    private $builder;

    /** @var Build */
    private $build;

    public function __construct()
    {
        $this->builder = new Builder(new App(new Response()));

        if (CLI) {
            Console::bind($this->builder);
        }

        $this->build = new Build();
        $this->assert();

        if (DEV) {
            $parameters = Parameters::fromFile();
            $this->builder = $this->builder
                ->withParameters($parameters);
            $this->build = $this->build
                ->withParameters($parameters);
        } else {
            try {
                if (Console::isBuilding()) {
                    $api = new Api();
                    $router = new Router();
                } else {
                    $api = Api::fromCache();
                    $router = Router::fromCache();
                }
                $container = $this->build->container()
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
            $this->build = $this->build
                ->withContainer($container);
        }
        $app = $this->builder->app()
            ->withRouter($this->build->container()->router());
        $this->builder = $this->builder
            ->withApp($app);
    }

    private function assert(): void
    {
        if (!DEV && !Console::isBuilding() && !$this->build->exists()) {
            throw new NeedsToBeBuiltException(
                (new Message('The application needs to be built by CLI %command% or calling %method% method.'))
                    ->code('%command%', 'php app/console build')
                    ->code('%method%', __CLASS__ . '::' . 'build')
                    ->toString()
            );
        }
    }

    public function run(): void
    {
        $this->builder->run();
    }
}
