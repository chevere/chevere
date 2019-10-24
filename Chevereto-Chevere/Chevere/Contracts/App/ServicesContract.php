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

interface ServicesContract
{

    /**
     * Creates a new application base service container.
     */
    public function __construct();

    /**
     * Return an instance with the specified ApiContract.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified ApiContract.
     */
    public function withApi(ApiContract $api): ServicesContract;

    /**
     * Returns a boolean indicating whether the instance has an ApiContract.
     */
    public function hasApi(): bool;

    /**
     * Provides access to the ApiContract instance.
     */
    public function api(): ApiContract;

    /**
     * Return an instance with the specified RouterContract.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified RouterContract.
     */
    public function withRouter(RouterContract $router): ServicesContract;

    /**
     * Returns a boolean indicating whether the instance has a RouterContract.
     */
    public function hasRouter(): bool;

    /**
     * Provides access to the RouterContract instance.
     */
    public function router(): RouterContract;
}
