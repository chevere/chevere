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
use Chevere\Exceptions\Core\InvalidArgumentException;
use Chevere\Exceptions\Core\RuntimeException;
use Chevere\Interfaces\Regex\RegexInterface;
use Safe\Exceptions\PcreException;
use function Safe\preg_match;
use function Safe\preg_match_all;

final class Regex implements RegexInterface
{
    private string $pattern;

    private string $noDelimiters;

    private string $noDelimitersNoAnchors;

    /**
     * @throws InvalidArgumentException
     */
    public function __construct(string $pattern)
    {
        $this->pattern = $pattern;
        $this->assertRegex();
        $delimiter = $this->pattern[0];
        $this->noDelimiters = trim($this->pattern, $delimiter);
        $this->noDelimitersNoAnchors = (string) preg_replace('#^\^(.*)\$$#', '$1', $this->noDelimiters);
    }

    public function toString(): string
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
     */
    public function match(string $string): array
    {
        try {
            $match = preg_match($this->pattern, $string, $matches);
        } catch (PcreException $e) {
            throw new RuntimeException(
                (new Message('Unable to %function%'))
                    ->code('%function%', 'preg_match'),
            );
        }

        /** @var array $matches */
        return $match === 0 ? [] : $matches;
    }

    /**
     * @codeCoverageIgnore
     */
    public function matchAll(string $string): array
    {
        try {
            $match = preg_match_all($this->pattern, $string, $matches);
        } catch (\Exception $e) {
            throw new RuntimeException(
                (new Message('Unable to %function%'))
                    ->code('%function%', 'preg_match_all'),
            );
        }

        /** @var array $matches */
        return $match === 0 ? [] : $matches;
    }

    private function assertRegex(): void
    {
        try {
            preg_match($this->pattern, '');
        } catch (\Exception $e) {
            throw new InvalidArgumentException(
                previous: $e,
                message: (new Message('Invalid regex string %regex% provided %error% [%preg%]'))
                    ->code('%regex%', $this->pattern)
                    ->code('%error%', $e->getMessage())
                    ->strtr('%preg%', static::ERRORS[preg_last_error()]),
            );
        }
    }
}
