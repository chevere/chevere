<?php

declare(strict_types=1);

/*
 * This file is part of Chevereto\Core.
 *
 * (c) Rodolfo Berrios <rodolfo@chevereto.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Chevereto\Core;

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

    protected function processApis(array $paramApis = null): void
    {
        if (!isset($paramApis)) {
            return;
        }
        $apis = new Apis($this->router);
        if (!$this->isCached) {
            foreach ($paramApis as $apiKey => $apiPath) {
                $apis->register($apiKey, $apiPath);
            }
        }
        $this->setApis($apis);
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
}
