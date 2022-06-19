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

namespace Chevere\Regex;

use function Chevere\Message\message;
use Chevere\Regex\Interfaces\RegexInterface;
use Chevere\Throwable\Exceptions\InvalidArgumentException;
use Chevere\Throwable\Exceptions\RuntimeException;
use Safe\Exceptions\PcreException;
use function Safe\preg_match;
use function Safe\preg_match_all;

final class Regex implements RegexInterface
{
    private string $noDelimiters;

    private string $noDelimitersNoAnchors;

    /**
     * @throws InvalidArgumentException
     */
    public function __construct(
        private string $pattern
    ) {
        $this->assertRegex();
        $delimiter = $this->pattern[0];
        $this->noDelimiters = trim($this->pattern, $delimiter);
        $this->noDelimitersNoAnchors = strval(preg_replace('#^\^(.*)\$$#', '$1', $this->noDelimiters));
    }

    public function __toString(): string
    {
        return $this->pattern;
    }

    public function toNoDelimiters(): string
    {
        return $this->noDelimiters;
    }

    public function toNoDelimitersNoAnchors(): string
    {
        return $this->noDelimitersNoAnchors;
    }

    /**
     * @codeCoverageIgnore
     * @infection-ignore-all
     */
    public function match(string $string): array
    {
        try {
            $match = preg_match($this->pattern, $string, $matches);
        } catch (PcreException $e) {
            throw new RuntimeException(
                message('Unable to %function%')
                    ->withCode('%function%', 'preg_match'),
            );
        }

        return $match === 0 ? [] : $matches;
    }

    /**
     * @codeCoverageIgnore
     * @infection-ignore-all
     */
    public function matchAll(string $string): array
    {
        try {
            $match = preg_match_all($this->pattern, $string, $matches);
        } catch (\Exception $e) {
            throw new RuntimeException(
                message('Unable to %function%')
                    ->withCode('%function%', 'preg_match_all'),
            );
        }

        return $match === 0 ? [] : $matches;
    }

    private function assertRegex(): void
    {
        try {
            preg_match($this->pattern, '');
        } catch (\Exception $e) {
            throw new InvalidArgumentException(
                previous: $e,
                message: message('Invalid regex string %regex% provided [%preg%]')
                    ->withCode('%regex%', $this->pattern)
                    ->withStrtr('%preg%', static::ERRORS[preg_last_error()]),
            );
        }
    }
}
