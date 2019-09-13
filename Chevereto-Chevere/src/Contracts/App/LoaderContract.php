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

use Chevere\App\Build;
use Chevere\Contracts\Http\RequestContract;
use Chevere\Runtime\Runtime;

interface LoaderContract
{
    public function __construct();

    public function build(): Build;

    /**
     * @param string $controller a fully-qualified controller name
     */
    public function setController(string $controller): void;

    /**
     * @param array $arguments string arguments to pass to the controller
     */
    public function setArguments(array $arguments): LoaderContract;

    public function setRequest(RequestContract $request): void;

    public static function setDefaultRuntime(Runtime $runtime);

    /**
     * Run the controller.
     */
    public function run(): void;

    /**
     * Retrieve the loaded Runtime.
     */
    public static function runtime(): Runtime;

    /**
     * Retrieve the loaded Request.
     */
    public static function request(): RequestContract;
}
