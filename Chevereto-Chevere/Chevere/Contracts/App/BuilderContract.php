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

use Chevere\Contracts\Http\RequestContract;

interface BuilderContract
{
    /**
     * Creates a new BuilderContact instance.
     */
    public function __construct(AppContract $app, BuildContract $build);

    /**
     * Return an instance with the specified AppContract.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified AppContract.
     */
    public function withApp(AppContract $app): BuilderContract;
    
    /**
     * Provides access to the AppContract instance.
     */
    public function app(): AppContract;

    /**
     * Return an instance with the specified BuildContract.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified BuildContract.
     */
    public function withBuild(BuildContract $build): BuilderContract;

    /**
     * Provides access to the BuildContract instance.
     */
    public function build(): BuildContract;

    /**
     * Return an instance with the specified RequestContract.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified RequestContract.
     */
    public function withRequest(RequestContract $request): BuilderContract;

    /**
     * Returns a boolean indicating whether the instance has a RequestContract.
     */
    public function hasRequest(): bool;

    /**
     * Provides access to the RequestContract instance.
     */
    public function request(): RequestContract;

    /**
     * Return an instance with the specified controller.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified controller.
     */
    public function withControllerName(string $controller): BuilderContract;

    /**
     * Returns a boolean indicating whether the instance has a controller name.
     */
    public function hasControllerName(): bool;

    /**
     * Provides access to the controller name.
     */
    public function controllerName(): string;

    /**
     * Return an instance with the specified controller arguments.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified controller arguments.
     */
    public function withControllerArguments(array $controllerArguments): BuilderContract;

    /**
     * Returns a boolean indicating whether the instance has controller arguments.
     */
    public function hasControllerArguments(): bool;

    /**
     * Provides access to the controller arguments.
     */
    public function controllerArguments(): array;
}
