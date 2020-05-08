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

use Chevere\Components\Plugs\Interfaces\PlugInterface;

interface HookInterface extends PlugInterface
{
    /**
     * @return string Applicable hook anchor.
     */
    public function for(): string;

    /**
     * @return string Target hookable class name.
     */
    public function at(): string;

    /**
     * @return string Priority order.
     */
    public function priority(): int;

    public function __invoke(&$argument): void;
}
