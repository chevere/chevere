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

namespace Chevere\Components\Runtime\Interfaces;

interface SetInterface
{
    public function __construct(string $value);

    /**
     * @return string The value passed in construct.
     */
    public function value(): string;

    /**
     * @return string The name of the set, i.e: "debug" or "locale".
     */
    public function name(): string;
}
