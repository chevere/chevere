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

namespace Chevere\Pluggable\Interfaces\Plug\Hook;

use Chevere\Throwable\Exceptions\InvalidArgumentException;

/**
 * Describes the component in charge of running the hooks queue.
 */
interface HooksRunnerInterface
{
    /**
     * Run the registered hooks at the given anchor.
     *
     * @throws InvalidArgumentException If the $argument type changes.
     */
    public function run(string $anchor, &$argument): void;
}
