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

use Chevere\Components\ArrayFile\Interfaces\ArrayFileInterface;
use Chevere\Components\Filesystem\Interfaces\Path\AppPathInterface;

interface ParametersInterface
{
    /**
     * Used to name the API key the path where App scans for API HTTP Controllers. Target path must be autoloaded.
     *
     * {@example 'api' => 'src/Api'}
     */
    const KEY_API = 'api';

    /**
     * Used to name the Routes key the array which lists the route files (relative to app).
     *
     * {@example 'routes' => ['routes:dashboard', 'routes:web',]}
     */
    const KEY_ROUTES = 'routes';

    public function __construct(ArrayFileInterface $arrayFile);

    /**
     * Return an instance with the specified Path instances.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified Path instances.
     */
    public function withAddedRoutePaths(AppPathInterface ...$paths): ParametersInterface;

    /**
     * Returns a boolean indicating whether the instance has any parameter.
     */
    public function hasParameters(): bool;

    /**
     * Returns a boolean indicating whether the instance has API parameter.
     */
    public function hasApi(): bool;

    /**
     * Get the API parameter.
     */
    public function api(): string;

    /**
     * Returns a boolean indicating whether the instance has route parameters.
     */
    public function hasRoutes(): bool;

    /**
     * Get the routes parameter.
     */
    public function routes(): array;
}
