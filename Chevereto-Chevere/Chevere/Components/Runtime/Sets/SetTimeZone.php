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

use InvalidArgumentException;
use RuntimeException;
use Chevere\Components\Message\Message;
use Chevere\Components\Runtime\Traits\SetTrait;
use Chevere\Components\Runtime\Contracts\SetContract;

class SetTimeZone implements SetContract
{
    use SetTrait;

    public function set(): void
    {
        if (date_default_timezone_get() == $this->value) {
            return;
        }
        if ('UTC' != $this->value && !$this->validateTimezone()) {
            throw new InvalidArgumentException(
                (new Message('Invalid timezone %timezone%'))
                    ->code('%timezone%', $this->value)
                    ->toString()
            );
        }
        if (!@date_default_timezone_set($this->value)) {
            throw new RuntimeException(
                (new Message('False return on %s(%v)'))
                    ->code('%s', 'date_default_timezone_set')
                    ->code('%v', $this->value)
                    ->toString()
            );
        }
    }

    private function validateTimezone(): bool
    {
        $return = false;
        $list = timezone_abbreviations_list();
        foreach ($list as $zone) {
            foreach ($zone as $item) {
                $tz = $item['timezone_id'] ?? null;
                if (isset($tz) && $this->value == $tz) {
                    $return = true;
                    break 2;
                }
            }
        }

        return $return;
    }
}
