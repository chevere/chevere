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

namespace Chevere\Contracts\Router;

use Chevere\Contracts\Cache\CacheContract;
use Chevere\Contracts\Route\RouteContract;

interface RouterMakerContract
{
    const REGEX_TEPLATE = '#^(?%s)$#x';

    /**
     * Creates a new instance.
     */
    public function __construct();

    /**
     * Return an instance with the specified added RouteContract.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified added RouteContract.
     */
    public function withAddedRoute(RouteContract $route, string $group): RouterMakerContract;

    /**
     * Return an instance with the specified added route files.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified X.
     */
    public function withAddedRouteFiles(...$routeIdentifiers): RouterMakerContract;

    public function regex(): string;

    public function routes(): array;

    public function index(): array;

    public function withCache(CacheContract $cache): RouterMakerContract;

    public function cache(): CacheContract;
}
