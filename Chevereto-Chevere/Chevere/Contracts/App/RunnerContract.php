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

use Chevere\Contracts\Controller\ControllerContract;

interface RunnerContract
{
    public function __construct(AppContract $app);

    public function withControllerName(string $controllerName): RunnerContract;

    /**
     * Run a controller on top of the App.
     *
     * @param string $controller a ControllerContract controller name
     */
    public function run(): ControllerContract;
}
