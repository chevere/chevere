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

use Chevere\Components\Path\Path;

interface BuildContract
{
    /**
     * Constructs the BuildContract instance.
     *
     * A BuildContract instance allows to interact with the application build, which refers to the base
     * application service layer which consists of API and Router services.
     */
    public function __construct(ServicesContract $services, Path $path);

    /**
     * Return an instance with the specified ServicesContract.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified ServicesContract.
     */
    public function withServices(ServicesContract $services): BuildContract;

    /**
     * Provides access to the ServicesContract instance.
     */
    public function services(): ServicesContract;

    /**
     * Return an instance with the specified ParametersContract.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified Container.
     */
    public function withParameters(ParametersContract $parameters): BuildContract;

    /**
     * Returns a boolean indicating whether the instance has a ParametersContract.
     */
    public function hasParameters(): bool;

    /**
     * Handles the API and route parameters and makes the application build.
     * Note: Can be only called once.
     */
    public function make(): BuildContract;

    /**
     * Destroy the application build (file plus any application cache).
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
     * Provides access to the build checksums.
     * Note: This method is available if the application build has been built.
     *
     * @see BuilderContract::isBuilt()
     */
    public function checksums(): array;

    /**
     * Provides access to the CheckoutContract instance.
     * Note: This method is available if the application build has been built.
     *
     * @see BuilderContract::isBuilt()
     */
    public function checkout(): CheckoutContract;
}
