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

use Chevere\Components\Api\Traits\ApiAccessTrait;
use Chevere\Components\Router\Traits\RouterAccessTrait;
use Chevere\Contracts\Api\ApiContract;
use Chevere\Contracts\App\ContainerContract;
use Chevere\Contracts\Router\RouterContract;

final class Container implements ContainerContract
{
    use RouterAccessTrait;
    use ApiAccessTrait;

    public function withRouter(RouterContract $router): ContainerContract
    {
        $new = clone $this;
        $new->router = $router;

        return $new;
    }

    public function withApi(ApiContract $api): ContainerContract
    {
        $new = clone $this;
        $new->api = $api;

        return $new;
    }
}
