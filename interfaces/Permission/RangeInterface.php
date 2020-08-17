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

namespace Chevere\Interfaces\Permission;

/**
 * Describes the component in charge of defining an integer range.
 */
interface RangeInterface
{
    /**
     * Declares the accepted min value. Use `null` for no limit.
     */
    public function getMin(): ?int;

    /**
     * Declares the accepted max value. Use `null` for no limit.
     */
    public function getMax(): ?int;

    /**
     * Returns the range in `[<int> min, <int> max]` format. It uses `null` when
     * no limit is set.
     *
     * @return int[]
     */
    public function getAccept(): array;

    /**
     * Indicates whether `$int` is in range.
     */
    public function isInRange(?int $int): bool;

    /**
     * Provides access to the instance value.
     */
    public function value(): ?int;
}
