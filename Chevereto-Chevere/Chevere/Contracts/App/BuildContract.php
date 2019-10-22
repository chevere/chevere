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

use Chevere\Components\App\Container;
use Chevere\Components\Path\Path;

interface BuildContract
{
    const FILE_PATH = 'build/build.php';

    public function __construct();

    public function withContainer(Container $container): BuildContract;

    public function withParameters(ParametersContract $parameters): BuildContract;

    public function make(): BuildContract;

    public function path(): Path;

    public function container(): Container;

    /**
     * Retrieves the build checksums.
     * Note: This method is available only when building the App.
     */
    public function checksums(): array;

    public function checkout(): CheckoutContract;

    /**
     * Destroy the build signature and any cache generated.
     */
    public function destroy(): void;
}
