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
use Chevere\Runtime\Traits\RuntimeSet;

class RuntimeSetLocale implements RuntimeSetContract
{
    use RuntimeSet;

    const ID = 'locale';

    public function set()
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
