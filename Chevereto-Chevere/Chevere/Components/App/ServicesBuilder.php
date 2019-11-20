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
use Chevere\Contracts\Api\ApiContract;
use Chevere\Contracts\App\BuildContract;
use Chevere\Contracts\App\ServicesBuilderContract;
use Chevere\Contracts\Router\RouterContract;

final class ServicesBuilder implements ServicesBuilderContract
{
    /** @var ServicesContract */
    private $services;

    /**
     * {@inheritdoc}
     */
    public function __construct(BuildContract $build, ParametersContract $parameters)
    {
        $this->services = $build->app()->services();
        $this->prepareServices();
        if ($parameters->hasRoutes()) {
            $this->services = $this->services
                ->withRouter(
                    $this->services()->router()
                        ->withCache(
                            new Cache(
                                $build->cacheDir()->getChild(RouterContract::CACHE_ID)
                            )
                        )
                );
        }
        if ($parameters->hasApi()) {
            $this->services = $this->services
                ->withApi(
                    $this->services->api()
                        ->withCache(
                            new Cache(
                                $build->cacheDir()->getChild(ApiContract::CACHE_ID)
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
