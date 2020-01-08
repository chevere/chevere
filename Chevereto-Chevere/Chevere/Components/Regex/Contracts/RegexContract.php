<?php

/*
 * This file is part of Chevere.
 *
 * (c) Rodolfo Berrios <rodolfo@chevereto.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Chevere\Components\Regex\Contracts;

use Chevere\Components\Regex\Exceptions\RegexException;

interface RegexContract
{
    /**
     * Creates a new instance.
     *
     * @throws RegexException if $regex is not a valid regular expresion
     */
    public function __construct(string $regex);

    /**
     * Provides access to the regex string.
     */
    public function toString(): string;
}
