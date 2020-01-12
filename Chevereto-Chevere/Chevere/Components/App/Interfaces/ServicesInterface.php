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

namespace Chevere\Components\App\Interfaces;

use Chevere\Components\Api\Interfaces\ApiInterface;
use Chevere\Components\Router\Interfaces\RouterInterface;

interface ServicesInterface
{
    public function __construct();

    /**
     * Return an instance with the specified ApiContract.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified ApiContract.
     */
    public function withApi(ApiInterface $api): ServicesInterface;

    /**
     * Returns a boolean indicating whether the instance has an ApiContract.
     */
    public function hasApi(): bool;

    /**
     * Provides access to the ApiContract instance.
     */
    public function api(): ApiInterface;

    /**
     * Return an instance with the specified RouterContract.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified RouterContract.
     */
    public function withRouter(RouterInterface $router): ServicesInterface;

    /**
     * Returns a boolean indicating whether the instance has a RouterContract.
     */
    public function hasRouter(): bool;

    /**
     * Provides access to the RouterContract instance.
     */
    public function router(): RouterInterface;
}
