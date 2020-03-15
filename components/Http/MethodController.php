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

use Chevere\Components\Controller\Interfaces\ControllerInterface;
use Chevere\Components\Http\Interfaces\MethodControllerInterface;
use Chevere\Components\Http\Interfaces\MethodInterface;

final class MethodController implements MethodControllerInterface
{
    private MethodInterface $method;

    private ControllerInterface $controller;

    public function __construct(
        MethodInterface $method,
        ControllerInterface $controller
    ) {
        $this->method = $method;
        $this->controller = $controller;
    }

    public function method(): MethodInterface
    {
        return $this->method;
    }

    public function controller(): ControllerInterface
    {
        return $this->controller;
    }
}
