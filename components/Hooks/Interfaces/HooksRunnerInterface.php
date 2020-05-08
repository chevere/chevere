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

namespace Chevere\Components\Hooks\Interfaces;

use Chevere\Components\Extend\PluginsQueue;

interface HooksRunnerInterface
{
    public function __construct(PluginsQueue $queue);

    /**
     * Run the registred hooks at the given ancshor.
     *
     * @throws RuntimeException If the $argument type changes.
     */
    public function run(string $anchor, &$argument): void;
}
