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

use LogicException;
use const Chevere\ROOT_PATH;
use const Chevere\App\PATH as AppPath;
use Monolog\Logger;
use Chevere\Runtime\Runtime;
use Chevere\Router\Router;
use Chevere\HttpFoundation\Response;
use Chevere\App\src\Checkout;
use Chevere\ArrayFile;
use Chevere\File;
use Chevere\Path;
use Chevere\Interfaces\ControllerInterface;
use Chevere\Route\Route;
use Chevere\Controller\ArgumentsWrap as ControllerArgumentsWrap;
use Chevere\Message;
use Chevere\Chevere;
use Chevere\Traits\StaticTrait;

/**
 * App contains the whole thing.
 */
final class App
{
    use StaticTrait;

    const BUILD_FILEPATH = ROOT_PATH.AppPath.'build';
    const NAMESPACES = ['App', 'Chevere'];
    const APP = 'app';
    const FILEHANDLE_CONFIG = ':config';
    const FILEHANDLE_PARAMETERS = ':parameters';
    const FILEHANDLE_HACKS = ':hacks';

    /** @var bool */
    private $isCached;

    /** @var array|null An array containing string arguments (from request uri, cli) */
    private $arguments;

    /** @var array|null An array containing the prepared controller arguments (object injection) */
    private $controllerArguments;

    /** @var Runtime */
    private $runtime;

    /** @var Logger */
    // private $logger;

    /** @var Router */
    private $router;

    /** @var Response */
    private $response;

    /** @var Route */
    private $route;

    /** @var string */
    // private $cache;

    /** @var string */
    // private $db;

    /** @var string */
    private $callable;

    /** @var App */
    private static $instance;

    /** @var Runtime */
    private static $defaultRuntime;

    /** @var Chevere */
    private $chevere;

    /*
    * (A) Router cache : The array which is used to resolve /req -> route (routing)
    * (B) Routes cache : The array of serialized Routes ['id' => Route serialized]
    * (C) Apis cache : The array containing the exposed API
    * ...
    * CHECK IF APP IS CACHED UNDER THE PROVIDED APIS+ROUTES
    * ...
    * new App:
    * 1. setParams (Runtime, [apis], [routes])
    * 2. isCached
    *      ? Router && API from Cache
    *      : Router && API on-the-fly
    * 3. Resolve controller
    *      - Router -> maps route id -> get Route -> return callable
    *
    * - Provide route access with some helper like Route::get('homepage@routes:web') which gets name=homepage from routes/web.php
    * - app/console dump:routes route:web -> Shows the return (generated objects) of this file
    * - App autoinjects a "Router", which could be Router::fromCache(...) or new Router() and provides access to Routes (cached or new)
    * - RouteCollection contains the array of mapped routes (objects or serialized arrays (cached))
    */
    public function __construct(Chevere $chevere)
    {
        $this->response = new Response();
        if (false === stream_resolve_include_path(self::BUILD_FILEPATH)) {
            new Checkout(self::BUILD_FILEPATH);
        }

        // Load::php(self::FILEHANDLE_HACKS);
        $pathHandle = Path::handle(self::FILEHANDLE_PARAMETERS);
        $arrayFile = new ArrayFile($pathHandle);
        $parameters = new Parameters($arrayFile);

        $chevere->applyParameters($parameters);
    }

    public function getBuildTime(): ?string
    {
        return File::exists(self::BUILD_FILEPATH) ? (string) file_get_contents(self::BUILD_FILEPATH) : null;
    }

    /**
     * Runs a explicit provided callable string.
     *
     * @param string $controller controller name
     */
    public function getControllerObject(string $controller)
    {
        // FIXME: Unified validation (Controller validator)
        if (!is_subclass_of($controller, ControllerInterface::class)) {
            throw new LogicException(
                (new Message('Callable %s must represent a class implementing the %i interface.'))
                    ->code('%s', $controller)
                    ->code('%i', ControllerInterface::class)
                    ->toString()
            );
        }
        $controller = new $controller($this);

        // if ($this->route instanceof Route) {
        //     $middlewares = $this->route->middlewares;
        //     if (!empty($middlewares)) {
        //         $handler = new Handler($middlewares);
        //         $handler->runner($this);
        //     }
        // }

        if (!empty($this->arguments)) {
            $wrap = new ControllerArgumentsWrap($controller, $this->arguments);
            $this->controllerArguments = $wrap->getArguments();
        } else {
            $this->controllerArguments = [];
        }

        $controller(...$this->controllerArguments);

        return $controller;
    }

    // public function getHash(): string
    // {
    //     return ($this->getConstant('App\VERSION') ?: null).$this->getBuildTime();
    // }

    public function route(): Route
    {
        return $this->route;
    }

    public function response(): Response
    {
        return $this->response;
    }

    // private function processConfigFiles(array $configFiles = null): void
    // {
    //     if (!isset($configFiles)) {
    //         return;
    //     }
    //     if (isset($this->runtime)) {
    //         $this->runtime->runConfig(
    //             (new Config())
    //                 ->processFromFiles($configFiles)
    //         );
    //     }
    // }
}
