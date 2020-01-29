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
use Chevere\Components\Route\Interfaces\WildcardMatchInterface;

final class WildcardMatch implements WildcardMatchInterface
{
    /** @var string a regular expresion match statement */
    private string $match;

    /**
     * Creates a new instance.
     *
     * @throws RegexException if $match is an invalid regex matcher
     */
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
