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

namespace Chevere\Contracts\App;

use Chevere\Contracts\Api\ApiContract;
use Chevere\Contracts\Router\RouterContract;

interface ContainerContract
{
    public function withApi(ApiContract $api): ContainerContract;

    public function withRouter(RouterContract $router): ContainerContract;

    public function hasApi(): bool;

    public function hasRouter(): bool;

    public function api(): ApiContract;

    public function router(): RouterContract;
}
