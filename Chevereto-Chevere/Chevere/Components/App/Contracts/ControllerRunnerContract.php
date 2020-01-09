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

use Chevere\Components\Controller\Contracts\ControllerContract;

interface ControllerRunnerContract
{
    public function __construct(AppContract $app);

    /**
     * Run a controller on top of the application container.
     *
     * @param string $controllerName A ControllerContract name.
     */
    public function run(string $controllerName): ControllerContract;
}
