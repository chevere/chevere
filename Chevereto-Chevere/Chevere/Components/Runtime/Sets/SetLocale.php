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

class SetLocale implements SetContract
{
    use SetTrait;

    public function set(): void
    {
        if (!setlocale(LC_ALL, $this->value)) {
            throw new RuntimeException(
                (new Message('The locale functionality is not implemented on your platform, the specified locale %locale% does not exist or the category name %category% is invalid'))
                    ->code('%category%', 'LC_ALL')
                    ->code('%locale%', $this->value)
                    ->toString()
            );
        }
    }
}
