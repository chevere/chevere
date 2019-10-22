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

    /**
     * Constructs the BuildContract instance.
     *
     * A BuildContract must always have the following properties:
     * - isBuilt
     * - container
     * - path
     */
    public function __construct();

    /**
     * Return an instance with the specified Container.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified Container.
     *
     * This method allows to pass a Container (application services)
     */
    public function withContainer(Container $container): BuildContract;

    public function container(): Container;

    public function withParameters(ParametersContract $parameters): BuildContract;

    public function hasParameters(): bool;

    public function make(): BuildContract;

    /**
     * Destroy the build file and any application cache.
     */
    public function destroy(): void;
    
    /**
     * Returns true if the build has been built.
     */
    public function isBuilt(): bool;

    /**
     * Returns a Path instance for the build file.
     */
    public function path(): Path;


    /**
     * Get the build checksums.
     * Note: This method is available after running BuilderContract::make().
     *
     * @see BuilderContract::make()
     * @see BuilderContract::isBuilt()
     */
    public function checksums(): array;

    /**
     * Get the build checkout.
     * Note: This method is available after running BuilderContract::make().
     *
     * @see BuilderContract::make()
     * @see BuilderContract::isBuilt()
     */
    public function checkout(): CheckoutContract;
}
