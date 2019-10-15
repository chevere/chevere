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
     * A BuilderContract always have app and build properties.
     */
    public function __construct(AppContract $app);

    public function withApp(AppContract $app): BuilderContract;

    public function withParameters(ParametersContract $parameters): BuilderContract;

    public function withBuild(BuildContract $build): BuilderContract;

    public function withRequest(RequestContract $request): BuilderContract;

    public function withController(string $controller): BuilderContract;

    public function withControllerArguments(array $controllerArguments): BuilderContract;

    public function hasParameters(): bool;

    public function hasRequest(): bool;

    public function hasController(): bool;

    public function hasControllerArguments(): bool;

    public function app(): AppContract;

    public function parameters(): ParametersContract;

    public function build(): BuildContract;

    public function run(): void;

    public static function runtimeInstance(): Runtime;

    public static function requestInstance(): RequestContract;

    public static function setRuntimeInstance(Runtime $runtime);
}
