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

namespace Chevere\Interfaces\Plugin;

interface PlugInterface
{
    /**
     * @return string Plugs at anchor
     */
    public function anchor(): string;

    /**
     * @return string Plugs at className
     */
    public function at(): string;

    /**
     * @return int Plug execution priority
     */
    public function priority(): int;
}
