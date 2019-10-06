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

class SetLocale implements SetContract
{
    use RuntimeSet;

    public function set(): void
    {
        if (!setlocale(LC_ALL, $this->value)) {
            throw new RuntimeException(
                (new Message('The locale functionality is not implemented on your platform, the specified locale %locale% does not exist or the category name %category% is invalid.'))
                    ->code('%category%', 'LC_ALL')
                    ->code('%locale%', $this->value)
                    ->toString()
            );
        }
    }
}
