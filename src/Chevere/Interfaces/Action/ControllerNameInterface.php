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

namespace Chevere\Interfaces\Action;

use Chevere\Interfaces\Common\ToStringInterface;

/**
 * Describes the component in charge of handling the controller name.
 */
interface ControllerNameInterface extends ToStringInterface
{
    /**
     * @throws InvalidArgumentException
     */
    public function __construct(string $name);
}
