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
use Chevere\Message;
use Chevere\Contracts\Runtime\RuntimeSetContract;
use Chevere\Runtime\Traits\RuntimeSet;

class RuntimeSetUriScheme implements RuntimeSetContract
{
    use RuntimeSet;

    private $id = 'uriScheme';

    public function set(): void
    {
        $accept = ['http', 'https'];
        if (!in_array($this->value, $accept)) {
            throw new RuntimeException(
                (new Message('Expecting %expecting%, %value% provided.'))
                    ->code('%expecting%', implode(', ', $accept))
                    ->code('%value%', $this->value)
                    ->toString()
            );
        }
    }
}
