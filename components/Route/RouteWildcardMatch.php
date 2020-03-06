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

namespace Chevere\Components\Route;

use Chevere\Components\Regex\Exceptions\RegexException;
use Chevere\Components\Regex\Regex;
use Chevere\Components\Route\Interfaces\RouteWildcardMatchInterface;

final class RouteWildcardMatch implements RouteWildcardMatchInterface
{
    /** @var string a regular expresion match statement */
    private string $match;

    public function __construct(string $match)
    {
        $this->match = $match;
        (new Regex('#' . $this->match . '#'))
            ->assertNoCapture();
    }

    public function toString(): string
    {
        return $this->match;
    }
}
