<?php

/*
 * This file is part of Chevere.
 *
 * (c) Rodolfo Berrios <rodolfo@chevere.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Chevere\Components\App\Interfaces;

use Chevere\Components\Api\Interfaces\ApiInterface;
use Chevere\Components\Router\Interfaces\RouterInterface;

interface ServicesInterface
{
    public function __construct();

    /**
     * Return an instance with the specified ApiInterface.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified ApiInterface.
     */
    public function withApi(ApiInterface $api): ServicesInterface;

    /**
     * Returns a boolean indicating whether the instance has an ApiInterface.
     */
    public function hasApi(): bool;

    /**
     * Provides access to the ApiInterface instance.
     */
    public function api(): ApiInterface;

    /**
     * Return an instance with the specified RouterInterface.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified RouterInterface.
     */
    public function withRouter(RouterInterface $router): ServicesInterface;

    /**
     * Returns a boolean indicating whether the instance has a RouterInterface.
     */
    public function hasRouter(): bool;

    /**
     * Provides access to the RouterInterface instance.
     */
    public function router(): RouterInterface;
}
