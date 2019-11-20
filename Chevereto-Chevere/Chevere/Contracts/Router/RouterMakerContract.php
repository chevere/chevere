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
     * Provides access to the regex string.
     */
    public function regex(): string;

    /**
     * Provides access to the routes array.
     */
    public function routes(): array;

    /**
     * Provides access to the index array.
     */
    public function index(): array;
}
