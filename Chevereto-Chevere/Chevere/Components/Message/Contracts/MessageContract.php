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

namespace Chevere\Components\Message\Contracts;

use Chevere\Components\Common\Contracts\ToStringContract;

interface MessageContract extends ToStringContract
{
    public function __construct(string $message);

    /**
     * @param string $search  the value being searched for, otherwise known as the needle
     * @param string $replace the replacement value that replaces found search value
     */
    public function translate(string $search, string $replace): MessageContract;

    /**
     * @return string The rich message after the translation table, non-cli aware.
     */
    public function toPlainString(): string;

    /**
     * @return string The rich message after the translation table, cli aware.
     */
    public function toString(): string;
}
