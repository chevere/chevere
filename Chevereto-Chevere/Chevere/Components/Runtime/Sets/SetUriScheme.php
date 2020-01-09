<?php

/*
 * This file is part of Chevere.
 *
 * (c) Rodolfo Berrios <rodolfo@chevereto.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Chevere\Components\Runtime\Sets;

use RuntimeException;
use Chevere\Components\Message\Message;
use Chevere\Components\Runtime\Traits\SetTrait;
use Chevere\Components\Runtime\Contracts\SetContract;

class SetUriScheme implements SetContract
{
    use SetTrait;

    public function set(): void
    {
        $accept = ['http', 'https'];
        if (!in_array($this->value, $accept)) {
            throw new RuntimeException(
                (new Message('Expecting %expecting%, %value% provided'))
                    ->code('%expecting%', implode(', ', $accept))
                    ->code('%value%', $this->value)
                    ->toString()
            );
        }
    }
}
