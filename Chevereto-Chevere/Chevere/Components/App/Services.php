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
use Chevere\Contracts\App\ServicesContract;
use Chevere\Contracts\Router\RouterContract;

/**
 * A container for the application base services (Router & API).
 */
final class Services implements ServicesContract
{
    use RouterAccessTrait;
    use ApiAccessTrait;

    /**
     * {@inheritdoc}
     */
    public function __construct()
    {
    }

    /**
     * {@inheritdoc}
     */
    public function withApi(ApiContract $api): ServicesContract
    {
        $new = clone $this;
        $new->api = $api;
        
        return $new;
    }

    /**
     * {@inheritdoc}
     */
    public function withRouter(RouterContract $router): ServicesContract
    {
        $new = clone $this;
        $new->router = $router;

        return $new;
    }
}
