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

interface LoaderContract
{
    public function __construct();

    public function withBuild(BuildContract $build): LoaderContract;

    public function withRequest(RequestContract $request): LoaderContract;

    public function withControllerArguments(array $arguments): LoaderContract;

    /**
     * @param string $controller a fully-qualified controller name
     */
    public function withController(string $controller): LoaderContract;

    public function hasRequest(): bool;

    public function hasControllerArguments(): bool;

    public function hasController(): bool;

    public function app(): AppContract;

    public function build(): BuildContract;

    public static function request(): RequestContract;

    public function run(): void;

    public function parameters(): ParametersContract;

    public static function setDefaultRuntime(Runtime $runtime);

    public static function runtime(): Runtime;
}
