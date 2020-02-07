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

namespace Chevere\Components\Regex\Interfaces;

use Chevere\Components\Common\Interfaces\ToStringInterface;

interface RegexInterface extends ToStringInterface
{
    public function __construct(string $regex);

    /**
     * @return string Regex
     */
    public function toString(): string;
}
