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

use Chevere\Components\App\Interfaces\ParametersInterface;
use Chevere\Components\App\Interfaces\ServicesInterface;
use Chevere\Components\Cache\Cache;
use Chevere\Components\Api\Api;
use Chevere\Components\Router\Router;
use Chevere\Components\Router\RouterCache;
use Chevere\Components\Api\Interfaces\ApiInterface;
use Chevere\Components\App\Interfaces\BuildInterface;
use Chevere\Components\App\Interfaces\ServicesBuilderInterface;
use Chevere\Components\Router\Interfaces\RouterInterface;

final class ServicesBuilder implements ServicesBuilderInterface
{
    private ServicesInterface $services;

    /**
     * Creates a new instance.
     *
     * @param BuildInterface      $build      The build containg AppInterface services (if any)
     * @param ParametersInterface $parameters The application parameters which alter this services builder
     */
    public function __construct(BuildInterface $build, ParametersInterface $parameters)
    {
        $this->services = $build->app()->services();
        $this->prepareServices();
        if ($parameters->hasRoutes()) {
            $routerCache =
                new RouterCache(
                    new Cache(
                        $build->dir()->getChild(RouterInterface::CACHE_ID)
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
                                $build->dir()->getChild(ApiInterface::CACHE_ID)
                            )
                        )
                );
        }
    }

    /**
     * {@inheritdoc}
     */
    public function services(): ServicesInterface
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
