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

use Chevere\Components\Api\Interfaces\ApiInterface;
use Chevere\Components\App\Interfaces\ServicesInterface;
use Chevere\Components\Router\Interfaces\RouterInterface;

/**
 * A container for the application base services (Router & API).
 */
final class Services implements ServicesInterface
{
    private ApiInterface $api;

    private RouterInterface $router;

    /**
     * Creates a new instance.
     */
    public function __construct()
    {
    }

    /**
     * {@inheritdoc}
     */
    public function withApi(ApiInterface $api): ServicesInterface
    {
        $new = clone $this;
        $new->api = $api;

        return $new;
    }

    /**
     * {@inheritdoc}
     */
    public function hasApi(): bool
    {
        return isset($this->api);
    }

    /**
     * {@inheritdoc}
     */
    public function api(): ApiInterface
    {
        return $this->api;
    }

    /**
     * {@inheritdoc}
     */
    public function withRouter(RouterInterface $router): ServicesInterface
    {
        $new = clone $this;
        $new->router = $router;

        return $new;
    }

    /**
     * {@inheritdoc}
     */
    public function hasRouter(): bool
    {
        return isset($this->router);
    }

    /**
     * {@inheritdoc}
     */
    public function router(): RouterInterface
    {
        return $this->router;
    }
}
