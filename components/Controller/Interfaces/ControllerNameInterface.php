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

namespace Chevere\Components\Controller\Interfaces;

use Chevere\Components\Common\Interfaces\ToStringInterface;

interface ControllerNameInterface extends ToStringInterface
{
    /**
     * Creates a new instance.
     */
    public function __construct(string $name);

    /**
     * @return string Controller name.
     */
    public function toString(): string;
}
