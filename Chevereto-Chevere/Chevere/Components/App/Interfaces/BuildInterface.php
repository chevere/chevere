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

namespace Chevere\Components\App\Interfaces;

use Chevere\Components\Dir\Interfaces\DirInterface;
use Chevere\Components\File\Interfaces\FileInterface;
use Chevere\Components\Router\Interfaces\RouterMakerInterface;

interface BuildInterface
{
    public function __construct(AppInterface $app);

    /**
     * Return an instance with the specified ServicesContract.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified ServicesContract.
     */
    public function withApp(AppInterface $services): BuildInterface;

    /**
     * Provides access to the ServicesContract instance.
     */
    public function app(): AppInterface;

    /**
     * Return an instance with the specified ParametersContract.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified Container.
     */
    public function withParameters(ParametersInterface $parameters): BuildInterface;

    /**
     * Returns a boolean indicating whether the instance has a ParametersContract.
     */
    public function hasParameters(): bool;

    /**
     * Provides access to the ParametersContract instance.
     */
    public function parameters(): ParametersInterface;

    /**
     * Handles the API and route parameters and makes the application build.
     * Note: Can be only called once.
     */
    public function withRouterMaker(RouterMakerInterface $roterMaker): BuildInterface;

    public function hasRouterMaker(): bool;

    public function routerMaker(): RouterMakerInterface;

    /**
     * Make the application build.
     */
    public function make(): BuildInterface;

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
    public function file(): FileInterface;

    /**
     * Provides access to the DirContract instance.
     */
    public function dir(): DirInterface;

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
    public function checkout(): CheckoutInterface;
}
