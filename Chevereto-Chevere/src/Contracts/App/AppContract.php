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

use Chevere\Contracts\Controller\ControllerContract;

interface AppContract
{
    /**
     * Retrieves the application buildtime.
     */
    // public function getBuildTime(): ?string;

    /**
     * Run a controller on top of the App.
     *
     * @param string $controller a ControllerContract controller name
     */
    public function run(string $controller): ControllerContract;
}
