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

use Chevere\Contracts\Api\ApiContract;
use Chevere\Contracts\App\ContainerContract;
use Chevere\Contracts\Router\RouterContract;

final class Container implements ContainerContract
{
    private $api;

    private $router;

    public function withApi(ApiContract $api): ContainerContract
    {
        $new = clone $this;
        $new->api = $api;

        return $new;
    }

    public function withRouter(RouterContract $router): ContainerContract
    {
        $new = clone $this;
        $new->router = $router;

        return $new;
    }

    public function hasApi(): bool
    {
        return isset($this->api);
    }

    public function hasRouter(): bool
    {
        return isset($this->router);
    }

    public function api(): ApiContract
    {
        return $this->api;
    }

    public function router(): RouterContract
    {
        return $this->router;
    }
}
