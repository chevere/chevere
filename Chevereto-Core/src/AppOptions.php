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

use Exception;

class AppOptions
{
    protected $configFiles;
    protected $apis;
    protected $routes;

    public function __construct()
    {
        $this->configFiles = [];
        $this->apis = [];
        $this->routes = [];
    }

    public function toArray(): array
    {
        return [
            'configFiles' => $this->configFiles,
            'apis' => $this->apis,
            'routes' => $this->routes,
        ];
    }

    public function addConfigFile(string $fileHandle): self
    {
        $this->configFiles[] = $fileHandle;

        return $this;
    }

    public function addApi(string $apiKey, string $pathIdentifier): self
    {
        $this->apis[$apiKey] = $pathIdentifier;

        return $this;
    }

    public function addRoute(string $fileHandle, string $context = null): self
    {
        $this->routes[$fileHandle] = $context;

        return $this;
    }

    public function getConfigFiles(): array
    {
        return $this->configFiles;
    }

    public function getApis(): array
    {
        return $this->apis;
    }

    public function getRoutes(): array
    {
        return $this->routes;
    }

    public static function createFromFile(string $fileHandle): self
    {
        $filepath = Path::fromHandle($fileHandle);
        try {
            $return = Load::php($filepath);
        } catch (Exception $e) {
            throw new \InvalidArgumentException(
                (string) (new Message('Unable to locate file specefied by %s (resolved as %f).'))
                    ->code('%s', $fileHandle)
                    ->code('%f', $filepath)
            );
        }

        return $return;
    }
}
