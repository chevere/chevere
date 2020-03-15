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

use Chevere\Components\Controller\Interfaces\ControllerInterface;
use Chevere\Components\Controller\Interfaces\ControllerNameInterface;

interface MethodControllerInterface
{
    public function __construct(MethodInterface $method, ControllerInterface $controller);

    public function method(): MethodInterface;

    public function controller(): ControllerInterface;
}
