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

namespace Chevere\Interfaces\Serialize;

use Chevere\Interfaces\To\ToStringInterface;

/**
 * Describes the component in charge of handling `serialize()`.
 */
interface SerializeInterface extends ToStringInterface
{
    public function __construct(mixed $var);
}
