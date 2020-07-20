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
use Chevere\Exceptions\Core\Exception;
use Chevere\Exceptions\Regex\RegexException;
use Chevere\Exceptions\Regex\RegexInvalidException;
use Chevere\Interfaces\Regex\RegexInterface;

final class Regex implements RegexInterface
{
    private string $string;

    private string $noDelimiters;

    private string $noDelimitersNoAnchors;

    public function __construct(string $string)
    {
        $this->string = $string;
        $this->assertRegex();
        $delimiter = $this->string[0];
        $this->noDelimiters = trim($this->string, $delimiter);
        $this->noDelimitersNoAnchors = (string) preg_replace('#^\^(.*)\$$#', '$1', $this->noDelimiters);
    }

    public function assertNoCapture(): void
    {
        $regex = str_replace(['\(', '\)'], null, $this->string);
        if (false !== strpos($regex, '(') || false !== strpos($regex, ')')) {
            throw new RegexException(
                (new Message('Provided expression %match% contains capture groups'))
                    ->code('%match%', $this->string)
            );
        }
    }

    public function toString(): string
    {
        return $this->string;
    }

    public function toNoDelimiters(): string
    {
        return $this->noDelimiters;
    }

    public function toNoDelimitersNoAnchors(): string
    {
        return $this->noDelimitersNoAnchors;
    }

    private function assertRegex(): void
    {
        try {
            if (@preg_match($this->string, '') === false) {
                throw new Exception(
                    (new Message('Detected %function% error'))
                        ->code('%function%', 'preg_match')
                ); // @codeCoverageIgnore
            }
        } catch (Exception $e) {
            throw new RegexInvalidException(
                (new Message('Invalid regex string %regex% provided %error% [%preg%]'))
                    ->code('%regex%', $this->string)
                    ->code('%error%', $e->getMessage())
                    ->strtr('%preg%', static::ERRORS[preg_last_error()]),
                0,
                $e
            );
        }
    }
}
