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

final class TimeHr
{
    /** @var int */
    private $hrTime;

    public function __construct(int $hrTime)
    {
        $this->hrTime = $hrTime;
    }
    /**
     * @return string Readable time in ms
     */
    public function toReadMs(): string
    {
        return number_format($this->hrTime / 1e+6, 2) . ' ms';
    }
}
