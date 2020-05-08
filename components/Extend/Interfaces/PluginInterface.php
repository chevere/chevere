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

namespace Chevere\Components\Extend\Interfaces;

interface PluginInterface
{
    /**
     * @return string for actor
     */
    public function for(): string;

    /**
     * @return string at className
     */
    public function at(): string;

    /**
     * @return int execution priority
     */
    public function priority(): int;
}
