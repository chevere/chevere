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
use Chevere\HttpFoundation\Response;
use Chevere\File;
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

    /** @var Chevere */
    private $chevere;

    /** @var array String arguments (from request uri, cli) */
    private $arguments;

    /** @var array Ready to pass controller arguments */
    private $controllerArguments;

    /** @var Response */
    public $response;

    /** @var Route */
    public $route;

    public function __construct(Chevere $chevere)
    {
        $this->chevere = $chevere;
        $this->response = new Response();
    }

    public function getBuildTime(): ?string
    {
        return File::exists(self::BUILD_FILEPATH) ? (string) file_get_contents(self::BUILD_FILEPATH) : null;
    }

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

    public function getHash(): string
    {
        return $this->getBuildTime();
    }
}
