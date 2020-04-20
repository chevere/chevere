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

use Chevere\Components\Message\Message;
use Chevere\Components\Regex\Regex;
use Chevere\Components\Route\Interfaces\RouteWildcardMatchInterface;
use Chevere\Components\Str\StrAssert;
use Chevere\Components\Str\StrBool;
use LogicException;

final class RouteWildcardMatch implements RouteWildcardMatchInterface
{
    /** @var string a regular expresion match statement */
    private string $string;

    public function __construct(string $string)
    {
        $this->string = $string;
        $this->assertFormat();
        (new Regex('#' . $this->string . '#'))->assertNoCapture();
    }

    public function toString(): string
    {
        return $this->string;
    }

    public function pattern(): string
    {
        return '^' . $this->string . '$';
    }

    private function assertFormat(): void
    {
        if ((new StrBool($this->string))->startsWith('^')) {
            throw new LogicException(
                (new Message('String %string% must omit the starting anchor %char%'))
                    ->code('%string%', $this->string)
                    ->code('%char%', '^')
                    ->toString()
            );
        }
        if ((new StrBool($this->string))->endsWith('$')) {
            throw new LogicException(
                (new Message('String %string% must omit the ending anchor %char%'))
                    ->code('%string%', $this->string)
                    ->code('%char%', '$')
                    ->toString()
            );
        }
    }
}
