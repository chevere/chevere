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

namespace Chevere\Components\App\Contracts;

use Chevere\Components\Dir\Contracts\DirContract;
use Chevere\Components\File\Contracts\FileContract;
use Chevere\Contracts\Path\PathContract;
use Chevere\Contracts\Router\RouterMakerContract;
use Chevere\Components\Path\Exceptions\PathIsNotDirectoryException;

interface BuildContract
{
    /**
     * Constructs the BuildContract instance.
     *
     * A BuildContract instance allows to interact with the application build, which refers to the base
     * application service layer which consists of API and Router services.
     *
     * @param AppContract  $app  The application container
     *
     * @throws PathIsNotDirectoryException if the $path doesn't exists and unable to create
     */
    public function __construct(AppContract $app);

    /**
     * Return an instance with the specified ServicesContract.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified ServicesContract.
     */
    public function withApp(AppContract $services): BuildContract;

    /**
     * Provides access to the ServicesContract instance.
     */
    public function app(): AppContract;

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
     * Provides access to the ParametersContract instance.
     */
    public function parameters(): ParametersContract;

    /**
     * Handles the API and route parameters and makes the application build.
     * Note: Can be only called once.
     */
    public function withRouterMaker(RouterMakerContract $roterMaker): BuildContract;

    public function hasRouterMaker(): bool;

    public function routerMaker(): RouterMakerContract;

    /**
     * Make the application build.
     */
    public function make(): BuildContract;

    /**
     * Returns true if the build has been just maked.
     */
    public function isMaked(): bool;

    /**
     * Destroy the application build (file plus any application cache).
     */
    public function destroy(): void;

    /**
     * Provides access to the FileContract contained in the FilePhpContract instance.
     */
    public function file(): FileContract;

    /**
     * Provides access to the DirContract instance.
     */
    public function dir(): DirContract;

    /**
     * Provides access to the build checksums.
     * Note: This method is available if the application build has been built.
     *
     * @see BuilderContract::isMaked()
     */
    public function checksums(): array;

    /**
     * Provides access to the CheckoutContract instance.
     * Note: This method is available if the application build has been built.
     *
     * @see BuilderContract::isMaked()
     */
    public function checkout(): CheckoutContract;
}
