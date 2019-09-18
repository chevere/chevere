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

namespace Chevere\Time;

abstract class Time
{
    /**
     * @param float $nanotime Nanotime (see hrtime())
     *
     * @return string Readable time in ms
     */
    public static function nanoToRead(float $nanotime): string
    {
        return number_format($nanotime / 1e+6, 2) . ' ms';
    }
}
