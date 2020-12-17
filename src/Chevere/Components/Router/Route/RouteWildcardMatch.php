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

namespace Chevere\Components\Router\Route;

use Chevere\Components\Message\Message;
use Chevere\Components\Regex\Regex;
use Chevere\Components\Str\StrBool;
use Chevere\Exceptions\Core\InvalidArgumentException;
use Chevere\Exceptions\Regex\RegexException;
use Chevere\Interfaces\Router\Route\RouteWildcardMatchInterface;

final class RouteWildcardMatch implements RouteWildcardMatchInterface
{
    private string $string;

    public function __construct(string $string)
    {
        $this->string = $string;
        $this->assertFormat();
        $this->assertRegexNoCapture();
    }

    public function toString(): string
    {
        return $this->string;
    }

    public function toAnchored(): string
    {
        return '^' . $this->string . '$';
    }

    public function assertRegexNoCapture(): void
    {
        $regex = new Regex('#' . $this->string . '#');
        $string = $regex->toString();
        $regex = str_replace(['\(', '\)'], '', $string);
        if (strpos($regex, '(') !== false || strpos($regex, ')') !== false) {
            throw new RegexException(
                (new Message('Provided expression %match% contains capture groups'))
                    ->code('%match%', $string)
            );
        }
    }

    private function assertFormat(): void
    {
        if ((new StrBool($this->string))->startsWith('^')) {
            throw new InvalidArgumentException(
                (new Message('String %string% must omit the starting anchor %char%'))
                    ->code('%string%', $this->string)
                    ->code('%char%', '^')
            );
        }
        if ((new StrBool($this->string))->endsWith('$')) {
            throw new InvalidArgumentException(
                (new Message('String %string% must omit the ending anchor %char%'))
                    ->code('%string%', $this->string)
                    ->code('%char%', '$')
            );
        }
    }
}
