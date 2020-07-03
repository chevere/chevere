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

namespace Chevere\Interfaces\Controller;

use Chevere\Exceptions\Controller\ControllerInterfaceException;
use Chevere\Exceptions\Controller\ControllerNameException;
use Chevere\Exceptions\Controller\ControllerNotExistsException;
use Chevere\Interfaces\To\ToStringInterface;

interface ControllerNameInterface extends ToStringInterface
{
    /**
     * @throws ControllerNameException
     * @throws ControllerNotExistsException
     * @throws ControllerInterfaceException
     */
    public function __construct(string $name);
}
