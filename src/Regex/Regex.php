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

use Chevere\Regex\Exceptions\NoMatchException;
use Chevere\Regex\Interfaces\RegexInterface;
use Chevere\Throwable\Exceptions\InvalidArgumentException;
use Chevere\Throwable\Exceptions\RuntimeException;
use Safe\Exceptions\PcreException;
use Throwable;
use function Chevere\Message\message;
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

    public function noDelimiters(): string
    {
        return $this->noDelimiters;
    }

    public function noDelimitersNoAnchors(): string
    {
        return $this->noDelimitersNoAnchors;
    }

    public function match(string $string): array
    {
        try {
            $match = preg_match($this->pattern, $string, $matches);
        }
        // @codeCoverageIgnoreStart
        catch (PcreException $e) {
            throw new RuntimeException(
                message('Unable to %function%')
                    ->withCode('%function%', 'preg_match'),
            );
        }
        // @codeCoverageIgnoreEnd

        return $match === 1 ? $matches : [];
    }

    public function assertMatch(string $string): void
    {
        if (! $this->match($string)) {
            throw new NoMatchException(
                message('String %string% does not match regex %pattern%')
                    ->withCode('%pattern%', $this->pattern)
                    ->withCode('%string% ', $string),
                100,
            );
        }
    }

    public function matchAll(string $string): array
    {
        try {
            $match = preg_match_all($this->pattern, $string, $matches);
        }
        // @codeCoverageIgnoreStart
        catch (PcreException $e) {
            throw new RuntimeException(
                message('Unable to %function%')
                    ->withCode('%function%', 'preg_match_all'),
            );
        }
        // @codeCoverageIgnoreEnd

        return $match === 1 ? $matches : [];
    }

    public function assertMatchAll(string $string): void
    {
        if (! $this->matchAll($string)) {
            throw new NoMatchException(
                message('String %string% does not match all regex %pattern%')
                    ->withCode('%pattern%', $this->pattern)
                    ->withCode('%string% ', $string),
                110
            );
        }
    }

    private function assertRegex(): void
    {
        try {
            preg_match($this->pattern, '');
        } catch (Throwable $e) {
            throw new InvalidArgumentException(
                previous: $e,
                message: message('Invalid regex string %regex% provided [%preg%]')
                    ->withCode('%regex%', $this->pattern)
                    ->withTranslate('%preg%', static::ERRORS[preg_last_error()]),
            );
        }
    }
}
