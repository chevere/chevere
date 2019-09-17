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

use Chevere\Contracts\Runtime\RuntimeSetContract;
use RuntimeException;
use Chevere\Message\Message;
use Chevere\Runtime\Traits\RuntimeSet;

class RuntimeSetPrecision implements RuntimeSetContract
{
    use RuntimeSet;

    public function set(): void
    {
        if (!@ini_set('precision', $this->value)) {
            throw new RuntimeException(
                (new Message('Unable to set %s %v.'))
                    ->code('%s', 'default_charset')
                    ->code('%v', $this->value)
                    ->toString()
            );
        }
    }
}
