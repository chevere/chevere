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

use Chevere\Components\Runtime\Runtime;
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

    public function withRequest(RequestContract $request): BuilderContract;

    public function hasRequest(): bool;

    public function request(): RequestContract;

    public function withControllerName(string $controller): BuilderContract;

    public function hasControllerName(): bool;

    public function controllerName(): string;

    public function withControllerArguments(array $controllerArguments): BuilderContract;

    public function hasControllerArguments(): bool;

    public function controllerArguments(): array;

    public static function runtimeInstance(): Runtime;

    public static function setRuntimeInstance(Runtime $runtime);
}
