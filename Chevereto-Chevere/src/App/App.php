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
use Chevere\File;
use Chevere\Message;
use Chevere\Contracts\App\AppContract;
use Chevere\Controller\ArgumentsWrap as ControllerArgumentsWrap;
use Chevere\HttpFoundation\Response;
use Chevere\Contracts\Controller\ControllerContract;
use Chevere\Contracts\Route\RouteContract;
use Chevere\Route\Route;

/**
 * The app container.
 */
final class App implements AppContract
{
    const BUILD_FILEPATH = ROOT_PATH.AppPath.'build';
    const NAMESPACES = ['App', 'Chevere'];
    const APP = 'app';
    const FILEHANDLE_CONFIG = ':config';
    const FILEHANDLE_PARAMETERS = ':parameters';
    const FILEHANDLE_HACKS = ':hacks';

    /** @var array String arguments (from request uri, cli) */
    public $arguments;

    /** @var Response */
    public $response;

    /** @var RouteContract */
    public $route;

    /**
     * {@inheritdoc}
     */
    // public function getBuildTime(): ?string
    // {
    //     return File::exists(self::BUILD_FILEPATH) ? (string) file_get_contents(self::BUILD_FILEPATH) : null;
    // }

    /**
     * {@inheritdoc}
     */
    public function run(string $controller): ControllerContract
    {
        if (!is_subclass_of($controller, ControllerContract::class)) {
            throw new LogicException(
                (new Message('Callable %s must represent a class implementing the %i interface.'))
                    ->code('%s', $controller)
                    ->code('%i', ControllerContract::class)
                    ->toString()
            );
        }
        $controller = new $controller($this);

        // if ($this->route instanceof RouteContract) {
        //     $middlewares = $this->route->middlewares;
        //     if (!empty($middlewares)) {
        //         $handler = new Handler($middlewares);
        //         $handler->runner($this);
        //     }
        // }

        if (isset($this->arguments)) {
            $wrap = new ControllerArgumentsWrap($controller, $this->arguments);
            $controllerArguments = $wrap->arguments();
        } else {
            $controllerArguments = [];
        }

        $controller(...$controllerArguments);

        return $controller;
    }
}
