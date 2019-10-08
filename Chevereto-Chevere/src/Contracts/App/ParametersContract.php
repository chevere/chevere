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

namespace Chevere\Contracts\App;

use Chevere\ArrayFile\ArrayFile;

interface ParametersContract
{
    /**
     * Used to describe the path where App scans for API HTTP Controllers. Target path must be autoloaded.
     *
     * {@example 'api' => 'src/Api'}
     */
    const API = 'api';

    /**
     * Used to describe the array which lists the route files (relative to app).
     *
     * {@example 'routes' => ['routes:dashboard', 'routes:web',]}
     */
    const ROUTES = 'routes';

    const FILEHANDLE_PARAMETERS = ':parameters';

    public function __construct(ArrayFile $arrayFile);

    /**
     * Builds a ParametersContract object from its file (app/parameters.php).
     */
    public static function fromFile(): ParametersContract;

    /**
     * Get the API string parameter.
     */
    public function api(): string;

    /**
     * Get the routes parameter.
     */
    public function routes(): array;
}
