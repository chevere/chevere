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

use Chevere\App\Container;
use Chevere\Contracts\App\LoaderContract;
use Chevere\Path\PathHandle;

interface BuildContract
{
    const FILE_INDETIFIER = 'var:build';

    public function __construct();

    public function pathHandle(): PathHandle;

    public function exists(): bool;

    public function withContainer(Container $container): BuildContract;

    public function withParameters(ParametersContract $parameters): BuildContract;

    public function hasContainer(): bool;

    public function hasParameters(): bool;

    public function container(): Container;

    public function parameters(): ParametersContract;

    /**
     * Retrieves the file checksums.
     * Note: This method is available only when building the App.
     */
    public function cacheChecksums(): array;

    public function checkout(): CheckoutContract;

    /**
     * Destroy the build signature and any cache generated.
     */
    public function destroy(): void;
}
