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
    public function __construct(string $template);

    public function template(): string;

    public function trTable(): array;

    public function toString(): string;

    public function strtr(string $search, string $replace): MessageInterface;

    public function implodeTag(string $search, string $tag, array $array): MessageInterface;

    public function em(string $search, string $replace): MessageInterface;

    public function strong(string $search, string $replace): MessageInterface;

    public function code(string $search, string $replace): MessageInterface;
}
