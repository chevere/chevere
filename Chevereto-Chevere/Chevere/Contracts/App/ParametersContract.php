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

use Chevere\Components\ArrayFile\ArrayFile;
use Chevere\Components\Path\Path;

interface ParametersContract
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

    public function __construct(ArrayFile $arrayFile);

    public function withAddedRoutePaths(Path ...$paths): ParametersContract;

    public function hasParameters(): bool;

    public function hasApi(): bool;

    public function hasRoutes(): bool;

    /**
     * Get the API string parameter.
     */
    public function api(): string;

    /**
     * Get the routes parameter.
     */
    public function routes(): array;
}
