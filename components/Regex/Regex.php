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
use Chevere\Exceptions\Core\RuntimeException;
use Chevere\Exceptions\Regex\RegexInvalidException;
use Chevere\Interfaces\Regex\RegexInterface;
use Safe\Exceptions\PcreException;
use function Safe\preg_match;
use function Safe\preg_match_all;

final class Regex implements RegexInterface
{
    private string $string;

    private string $noDelimiters;

    private string $noDelimitersNoAnchors;

    /**
     * @throws RegexInvalidException
     */
    public function __construct(string $string)
    {
        $this->string = $string;
        $this->assertRegex();
        $delimiter = $this->string[0];
        $this->noDelimiters = trim($this->string, $delimiter);
        $this->noDelimitersNoAnchors = (string) preg_replace('#^\^(.*)\$$#', '$1', $this->noDelimiters);
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

    /**
     * @codeCoverageIgnore
     */
    public function match(string $string): array
    {
        try {
            preg_match($this->string, $string, $matches);
        } catch (PcreException $e) {
            throw new RuntimeException(
                (new Message('Unable to %function%'))
                    ->code('%function%', 'preg_match'),
                0,
                $e
            );
        }

        return $matches;
    }

    /**
     * @codeCoverageIgnore
     */
    public function matchAll(string $string): array
    {
        try {
            preg_match_all($this->string, $string, $matches);
        } catch (\Exception $e) {
            throw new RuntimeException(
                (new Message('Unable to %function%'))
                    ->code('%function%', 'preg_match_all'),
                0,
                $e
            );
        }

        return $matches;
    }

    private function assertRegex(): void
    {
        try {
            if (preg_match($this->string, '') === false) {
                throw new Exception(
                    (new Message('Detected %function% error'))
                        ->code('%function%', 'preg_match')
                ); // @codeCoverageIgnore
            }
        } catch (\Exception $e) {
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
