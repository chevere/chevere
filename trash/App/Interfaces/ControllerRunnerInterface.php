<?php

/*
 * This file is part of Chevere.
 *
 * (c) Rodolfo Berrios <rodolfo@chevere.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Chevere\Components\App\Interfaces;

use Chevere\Components\Controller\Interfaces\ControllerInterface;

interface ControllerRunnerInterface
{
    public function __construct(AppInterface $app);

    /**
     * Run a controller on top of the application container.
     *
     * @param string $controllerName A ControllerInterface name.
     */
    public function run(string $controllerName): ControllerInterface;
}
