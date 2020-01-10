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

namespace Chevere\Components\Time;

use Chevere\Components\TimeHr\Contracts\TimeHrContract;

final class TimeHr implements TimeHrContract
{
    /** @var int High-resolution time */
    private int $hrTime;

    /** @var int Readable time, in ms with its unit like `100 ms` */
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

    /**
     * {@inheritdoc}
     */
    public function toReadMs(): string
    {
        return $this->hrTimeRead;
    }
}
