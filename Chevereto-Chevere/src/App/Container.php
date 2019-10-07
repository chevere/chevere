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
use Chevere\Contracts\Router\RouterContract;

final class Container
{
    private $api;

    private $router;

    public function __construct()
    { }

    public function withApi(ApiContract $api): Container
    {
        $new = clone $this;
        $new->api = $api;

        return $new;
    }

    public function withRouter(RouterContract $router): Container
    {
        $new = clone $this;
        $new->router = $router;

        return $new;
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
