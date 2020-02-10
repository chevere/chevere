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

namespace Chevere\Components\Str\Interfaces;

interface StrAssertInterface
{
    public function __construct(string $string);

    public function notEmpty(): StrAssertInterface;

    public function notCtypeSpace(): StrAssertInterface;
}
