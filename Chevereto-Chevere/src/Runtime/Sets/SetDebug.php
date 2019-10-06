<?php

declare(strict_types=1);

/*
 * This file is part of Chevere.
 *
 * (c) Rodolfo Berrios <rodolfo@chevereto.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Chevere\Runtime\Sets;

use RuntimeException;
use Chevere\Message\Message;
use Chevere\Contracts\Runtime\SetContract;
use Chevere\Runtime\Traits\RuntimeSet;

class SetDebug implements SetContract
{
    use RuntimeSet;

    const ACCEPT = [0, 1];

    public function set(): void
    {
        if (!in_array($this->value, static::ACCEPT)) {
            throw new RuntimeException(
                (new Message('Expecting %expecting%, %value% provided.'))
                    ->code('%expecting%', implode(', ', static::ACCEPT))
                    ->code('%value%', $this->value)
                    ->toString()
            );
        }
    }
}
