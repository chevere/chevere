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

namespace Chevere\Components\Http\Contracts;

use Chevere\Components\Controller\Contracts\ControllerNameContract;

interface MethodControllerNameContract
{
    /**
     * Creates a new instance.
     */
    public function __construct(MethodContract $method, ControllerNameContract $controllerName);

    /**
     * Provides access to the MethodContract instance.
     */
    public function method(): MethodContract;

    /**
     * Provides access to the controller name.
     */
    public function controllerName(): ControllerNameContract;
}
