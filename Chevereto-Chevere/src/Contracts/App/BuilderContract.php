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

use Chevere\Contracts\Http\RequestContract;
use Chevere\Runtime\Runtime;

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

    public function hasControllerString(): bool;
    
    public function hasControllerArguments(): bool;
    
    public function app(): AppContract;
    
    public function parameters(): ParametersContract;

    public function build(): BuildContract;

    public function run(): void;

    public static function runtime(): Runtime;

    public static function requestInstance(): RequestContract;

    public static function setRuntime(Runtime $runtime);
}
