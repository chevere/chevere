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

namespace Chevere\Components\Http\Interfaces;

use Chevere\Components\Controller\Interfaces\ControllerNameInterface;

interface MethodControllerNameInterface
{
    public function __construct(MethodInterface $method, ControllerNameInterface $controllerName);

    /**
     * Provides access to the MethodInterface instance.
     */
    public function method(): MethodInterface;

    /**
     * Provides access to the controller name.
     */
    public function controllerName(): ControllerNameInterface;
}
