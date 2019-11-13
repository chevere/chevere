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

use Chevere\Contracts\App\ParametersContract;
use Chevere\Contracts\App\ServicesContract;
use Chevere\Components\Cache\Cache;
use Chevere\Components\Api\Api;
use Chevere\Components\Router\Router;
use Chevere\Contracts\App\BuildContract;
use Chevere\Contracts\App\ServicesBuilderContract;
use function console;
use const Chevere\CONSOLE;

final class ServicesBuilder implements ServicesBuilderContract
{
    public function __construct(BuildContract $build, ParametersContract $parameters)
    {
        $api = new Api();
        $router = new Router();
        $services = $build->app()->services();
        if (!(CONSOLE && console()->isBuilding())) {
            $dir = $build->cacheDir();
            $cache = new Cache($dir);
            if ($parameters->hasApi()) {
                $api = $api
                  ->withCache($cache);
            }
            $router = $router
              ->withCache($cache);
        }
        if ($parameters->hasApi()) {
            $services = $services->withApi($api);
        }

        $this->services = $services
          ->withRouter($router);
    }

    public function services(): ServicesContract
    {
        return $this->services;
    }
}
