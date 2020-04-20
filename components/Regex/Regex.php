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

namespace Chevere\Components\Regex;

use Chevere\Components\Message\Message;
use Chevere\Components\Regex\Exceptions\RegexException;
use Chevere\Components\Regex\Interfaces\RegexInterface;
use Throwable;

final class Regex implements RegexInterface
{
    private string $string;

    /**
     * @throws RegexException if $regex is not a valid regular expresion
     */
    public function __construct(string $string)
    {
        $this->string = $string;
        $this->assertRegex();
    }

    public function assertNoCapture(): void
    {
        $regex = str_replace(['\(', '\)'], null, $this->string);
        if (false !== strpos($regex, '(') || false !== strpos($regex, ')')) {
            throw new RegexException(
                (new Message('Provided expresion %match% contains capture groups (remove any capture group)'))
                    ->code('%match%', $this->string)
                    ->toString()
            );
        }
    }

    public function toString(): string
    {
        return $this->string;
    }

    private function assertRegex(): void
    {
        try {
            preg_match($this->string, '');
        } catch (Throwable $e) {
            throw new RegexException(
                (new Message('Invalid regex string %regex% provided %error% [%preg%]'))
                    ->code('%regex%', $this->string)
                    ->code('%error%', $e->getMessage())
                    ->strtr('%preg%', static::ERRORS[preg_last_error()])
                    ->toString(),
                0,
                $e
            );
        }
    }
}
