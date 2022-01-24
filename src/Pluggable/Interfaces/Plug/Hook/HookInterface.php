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

use Chevere\Pluggable\Interfaces\PlugInterface;

/**
 * Describes the component in charge of defining a hook plug.
 */
interface HookInterface extends PlugInterface
{
    /**
     * Executes the hook listener.
     *
     * @param mixed $argument The hooked argument.
     */
    public function __invoke(&$argument): void;
}
