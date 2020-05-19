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

namespace Chevere\Components\Plugs\Hooks\Interfaces;

use Chevere\Components\Plugin\Interfaces\PlugInterface;

interface HookInterface extends PlugInterface
{
    public function __invoke(&$argument): void;

    /**
     * @return string Applicable hook anchor.
     */
    public function anchor(): string;

    /**
     * @return string Target class name implementing HookableInterface.
     */
    public function at(): string;

    /**
     * @return string Priority order.
     */
    public function priority(): int;
}
