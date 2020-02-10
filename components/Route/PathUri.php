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

use Chevere\Components\Route\Exceptions\PathUriUnmatchedBracesException;
use Chevere\Components\Message\Message;
use Chevere\Components\Route\Exceptions\PathUriForwardSlashException;
use Chevere\Components\Route\Exceptions\PathUriInvalidCharsException;
use Chevere\Components\Route\Exceptions\PathUriUnmatchedWildcardsException;
use Chevere\Components\Route\Exceptions\WildcardRepeatException;
use Chevere\Components\Route\Exceptions\WildcardReservedException;
use Chevere\Components\Route\Interfaces\PathUriInterface;
use Chevere\Components\Str\Str;
use Chevere\Components\Str\StrBool;

final class PathUri implements PathUriInterface
{
    /** @var string */
    private string $path;

    /** @var string Path key set representation ({wildcards} replaced by {n}) */
    private string $key;

    /** @var int */
    private int $wildcardBracesCount;

    /** @var array */
    private array $wildcardsMatch;

    /** @var array string[] */
    private array $wildcards;

    /**
     * Creates a new instance.
     *
     * @param string $path a path uri like `/path/{wildcard}`
     *
     * @throws PathUriForwardSlashException       if $path doesn't start with forward slash
     * @throws PathUriInvalidCharsException       if $path contains invalid chars
     * @throws PathUriUnmatchedBracesException    if $path contains unmatched braces (must be paired)
     * @throws PathUriUnmatchedWildcardsException if $path contains wildcards that don't match the number of braces
     * @throws WildcardReservedException          if $path contains reserved wildcards
     * @throws WildcardRepeatException            if $path contains repeated wildcards
     */
    public function __construct(string $path)
    {
        $this->path = $path;
        $this->assertFormat();
        $this->key = $this->path;
        if ($this->hasHandlebars()) {
            $this->wildcardsMatch = [];
            $this->wildcardBracesCount = 0;
            $this->assertWildcards();
            $this->handleWildcards();
        }
    }

    public function toString(): string
    {
        return $this->path;
    }

    public function key(): string
    {
        return $this->key;
    }

    public function hasWildcards(): bool
    {
        return isset($this->wildcards);
    }

    public function wildcards(): array
    {
        return $this->wildcards;
    }

    private function assertFormat(): void
    {
        if ((new StrBool($this->path))->startsWith('/') === false) {
            throw new PathUriForwardSlashException(
                (new Message('Route path %path% must start with a forward slash'))
                    ->code('%path%', $this->path)
                    ->toString()
            );
        }
        $illegals = $this->getIllegalChars();
        if ($illegals) {
            throw new PathUriInvalidCharsException(
                (new Message('Route path %path% must not contain illegal characters (' . implode(' ', $illegals) . ')'))
                    ->code('%path%', $this->path)
                    ->toString()
            );
        }
    }

    private function assertMatchingBraces(): void
    {
        $countOpen = substr_count($this->path, '{');
        $countClose = substr_count($this->path, '}');
        if ($countOpen !== $countClose) {
            throw new PathUriUnmatchedBracesException(
                (new Message('Route path %path% contains unmatched wildcard braces (%countOpen% open, %countClose% close)'))
                    ->code('%path%', $this->path)
                    ->strtr('%countOpen%', (string) $countOpen)
                    ->strtr('%countClose%', (string) $countClose)
                    ->toString()
            );
        }
        $this->wildcardBracesCount = $countOpen;
    }

    private function assertMatchingWildcards(): void
    {
        preg_match_all(PathUriInterface::REGEX_WILDCARD_SEARCH, $this->path, $this->wildcardsMatch);
        $countMatches = count($this->wildcardsMatch[0]);
        if ($this->wildcardBracesCount !== $countMatches) {
            throw new PathUriUnmatchedWildcardsException(
                (new Message('Route path %path% contains invalid wildcard declarations (pattern %pattern% matches %countMatches%)'))
                    ->code('%path%', $this->path)
                    ->strtr('%wildcardsCount%', (string) $this->wildcardBracesCount)
                    ->strtr('%countMatches%', (string) $countMatches)
                    ->code('%pattern%', PathUriInterface::REGEX_WILDCARD_SEARCH)
                    ->toString()
            );
        }
    }

    /**
     * @return array [n => '<code>character</code> name]
     */
    private function getIllegalChars(): array
    {
        $illegalChars = [
            '//' => 'extra-slashes',
            '\\' => 'backslash',
            '{{' => 'double-braces',
            '}}' => 'double-braces',
            ' ' => 'whitespace',
        ];
        $illegals = [];
        foreach ($illegalChars as $character => $name) {
            if (false !== strpos($this->path, $character)) {
                $illegals[] = (new Message('%character% %name%'))
                    ->code('%character%', $character)
                    ->strtr('%name%', $name)
                    ->toString();
            }
        }

        return $illegals;
    }

    private function assertReservedWildcards(): void
    {
        if (!(0 === preg_match_all('/{([0-9]+)}/', $this->path, $matches))) {
            throw new WildcardReservedException(
                (new Message('Path %path% contain system reserved wildcards %list%'))
                    ->code('%path%', $this->path)
                    ->code('%list%', implode(' ', $matches[0]))
                    ->toString()
            );
        }
    }

    private function handleWildcards(): void
    {
        foreach ($this->wildcardsMatch[0] as $key => $val) {
            // Change {wildcard} to {n} (n is the wildcard index)
            if (isset($this->key)) {
                $this->key = (string) (new Str($this->key))->replaceFirst($val, "{{$key}}");
            }
            $wildcard = $this->wildcardsMatch[1][$key];
            if (in_array($wildcard, $this->wildcards ?? [])) {
                throw new WildcardRepeatException(
                    (new Message('Duplicated wildcard %wildcard% in path uri %path%'))
                        ->code('%wildcard%', $this->wildcardsMatch[0][$key])
                        ->code('%path%', $this->path)
                        ->toString()
                );
            }
            $this->wildcards[] = $wildcard;
        }
    }

    private function hasHandlebars(): bool
    {
        return false !== strpos($this->path, '{') || false !== strpos($this->path, '}');
    }

    /**
     * @throws PathUriUnmatchedBracesException
     * @throws PathUriUnmatchedWildcardsException
     * @throws WildcardReservedException
     */
    private function assertWildcards(): void
    {
        $this->assertMatchingBraces();
        $this->assertReservedWildcards();
        $this->assertMatchingWildcards();
    }
}
