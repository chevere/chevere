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

namespace Chevereto\Chevere;

use Throwable;

abstract class AppProcessor extends AppStatic
{
    protected function processCheckout(): void
    {
        if (false === stream_resolve_include_path($this->getBuildFilePath())) {
            $this->checkout();
        }
    }

    protected function processConfigFiles(array $configFiles = null): void
    {
        if (!isset($configFiles)) {
            return;
        }
        if (isset($this->runtime)) {
            $this->runtime->runConfig(
                (new RuntimeConfig())
                    ->processFromFiles($configFiles)
            );
        }
    }

    protected function processApi(string $pathIdentifier = null): void
    {
        if (!isset($pathIdentifier)) {
            return;
        }
        $api = new Api($this->router);
        if (!$this->isCached) {
            $api->register($pathIdentifier);
        }
        $this->setApi($api);
    }

    protected function processParamRoutes(array $paramRoutes = null): void
    {
        if (!isset($paramRoutes)) {
            return;
        }
        // ['handle' => [Routes,]]
        foreach ($paramRoutes as $fileHandle) {
            foreach ((new Routes($fileHandle))->getArrayFile()->toArray() as $k => $route) {
                $this->router->addRoute($route, $fileHandle);
            }
        }
    }

    protected function processSapi(): void
    {
        if (Console::bind($this)) {
            Console::run(); // Note: Console::run() always exit.
        } else {
            $this->setHttpRequest(HttpRequest::createFromGlobals());
        }
    }

    protected function processCallable(string $callable): void
    {
        $controller = $this->getControllerObject($callable);
        if ($controller instanceof Interfaces\RenderableInterface) {
            echo $controller->render();
        } else {
            $controller->getResponse()->send();
        }
    }

    protected function routerResolve(string $pathInfo): void
    {
        try {
            $route = $this->router->resolve($pathInfo);
            if (isset($route)) {
                $this->setRoute($route);
                $this->callable = $route->getCallable(
                    $this->httpRequest->getMethod()
                );
                $routerArgs = $this->router->getArguments();
                if (isset($routerArgs)) {
                    $this->setArguments($routerArgs);
                }
            } else {
                echo 'NO ROUTE FOUND';

                return;
            }
        } catch (Throwable $e) {
            echo 'Exception at App: ' . $e->getCode();

            return;
        }
    }
}
