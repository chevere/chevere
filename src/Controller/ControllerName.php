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

namespace Chevere\Controller;

use Chevere\Controller\Interfaces\ControllerInterface;
use Chevere\Controller\Interfaces\ControllerNameInterface;
use function Chevere\Common\assertClassName;

final class ControllerName implements ControllerNameInterface
{
    public function __construct(
        private string $name
    ) {
        assertClassName(ControllerInterface::class, $this->name);
    }

    public function __toString(): string
    {
        return $this->name;
    }
}
