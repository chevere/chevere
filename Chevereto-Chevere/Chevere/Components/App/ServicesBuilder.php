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

use Chevere\Components\App\Contracts\ParametersContract;
use Chevere\Components\App\Contracts\ServicesContract;
use Chevere\Components\Cache\Cache;
use Chevere\Components\Api\Api;
use Chevere\Components\Router\Router;
use Chevere\Components\Router\RouterCache;
use Chevere\Components\Api\Contracts\ApiContract;
use Chevere\Components\App\Contracts\BuildContract;
use Chevere\Components\App\Contracts\ServicesBuilderContract;
use Chevere\Contracts\Router\RouterContract;

final class ServicesBuilder implements ServicesBuilderContract
{
    private ServicesContract $services;

    /**
     * {@inheritdoc}
     */
    public function __construct(BuildContract $build, ParametersContract $parameters)
    {
        $this->services = $build->app()->services();
        $this->prepareServices();
        if ($parameters->hasRoutes()) {
            $routerCache =
                new RouterCache(
                    new Cache(
                        $build->dir()->getChild(RouterContract::CACHE_ID)
                    )
                );
            $this->services = $this->services
                ->withRouter(
                    $this->services()->router()
                        ->withProperties($routerCache->getProperties())
                );
        }
        if ($parameters->hasApi()) {
            $this->services = $this->services
                ->withApi(
                    $this->services->api()
                        ->withCache(
                            new Cache(
                                $build->dir()->getChild(ApiContract::CACHE_ID)
                            )
                        )
                );
        }
    }

    /**
     * {@inheritdoc}
     */
    public function services(): ServicesContract
    {
        return $this->services;
    }

    private function prepareServices(): void
    {
        if (!$this->services->hasRouter()) {
            $this->services = $this->services
                ->withRouter(new Router());
        }
        if (!$this->services->hasApi()) {
            $this->services = $this->services
                ->withApi(new Api());
        }
    }
}
