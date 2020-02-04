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

namespace Chevere\Components\Message\Interfaces;

use Chevere\Components\Common\Interfaces\ToStringInterface;

interface MessageInterface extends ToStringInterface
{
    public function __construct(string $message);

    /**
     * @param string $search  the value being searched for, otherwise known as the needle
     * @param string $replace the replacement value that replaces found search value
     */
    public function strtr(string $search, string $replace): MessageInterface;

    /**
     * @param string $search  the value being searched for, otherwise known as the needle
     * @param string $replace the replacement value that replaces found search value
     */
    public function code(string $search, string $replace): MessageInterface;

    /**
     * @param string $search  the value being searched for, otherwise known as the needle
     * @param string $replace the replacement value that replaces found search value
     */
    public function strong(string $search, string $replace): MessageInterface;

    /**
     * @return string The message after the translation table, cli aware.
     */
    public function toString(): string;
}
