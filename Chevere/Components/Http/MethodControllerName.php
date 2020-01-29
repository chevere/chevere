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

namespace Chevere\Components\Http;

use Chevere\Components\Controller\Interfaces\ControllerNameInterface;
use Chevere\Components\Http\Interfaces\MethodInterface;
use Chevere\Components\Http\Interfaces\MethodControllerNameInterface;

final class MethodControllerName implements MethodControllerNameInterface
{
    private MethodInterface $method;

    private ControllerNameInterface $controllerName;

    /**
     * Creates a new instance.
     */
    public function __construct(MethodInterface $method, ControllerNameInterface $controllerName)
    {
        $this->method = $method;
        $this->controllerName = $controllerName;
    }

    public function method(): MethodInterface
    {
        return $this->method;
    }

    public function controllerName(): ControllerNameInterface
    {
        return $this->controllerName;
    }
}
