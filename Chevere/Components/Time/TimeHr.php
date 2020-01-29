<?php

/*
 * This file is part of Chevere.
 *
 * (c) Rodolfo Berrios <rodolfo@chevere.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Chevere\Components\Time;

use Chevere\Components\Time\Interfaces\TimeHrInterface;

final class TimeHr implements TimeHrInterface
{
    /** @var int High-resolution time */
    private int $hrTime;

    /** @var string Readable time, in ms with its unit like `100 ms` */
    private string $hrTimeRead;

    /**
     * Creates a new instance.
     *
     * @param int $hrTime High-resolution time.
     */
    public function __construct(int $hrTime)
    {
        $this->hrTime = $hrTime;
        $this->hrTimeRead = number_format($this->hrTime / 1e+6, 2) . ' ms';
    }

    public function toReadMs(): string
    {
        return $this->hrTimeRead;
    }
}
