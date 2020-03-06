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

use BadMethodCallException;
use Chevere\Components\Route\Exceptions\RoutePathUnmatchedBracesException;
use Chevere\Components\Message\Message;
use Chevere\Components\Route\Exceptions\RouteWildcardNotFoundException;
use Chevere\Components\Route\Exceptions\RoutePathForwardSlashException;
use Chevere\Components\Route\Exceptions\RoutePathInvalidCharsException;
use Chevere\Components\Route\Exceptions\RoutePathUnmatchedWildcardsException as RoutePathUnmatchedWildcardsCountException;
use Chevere\Components\Route\Exceptions\RouteWildcardRepeatException;
use Chevere\Components\Route\Exceptions\RouteWildcardReservedException;
use Chevere\Components\Route\Interfaces\RoutePathInterface;
use Chevere\Components\Route\Interfaces\RouteWildcardsInterface;
use Chevere\Components\Route\Interfaces\RouteWildcardInterface;
use Chevere\Components\Str\Str;
use Chevere\Components\Str\StrBool;
use LogicException;
use Throwable;

/**
 * Provides interaction for route paths, which may accept wildcards `/api/articles/{id}`
 */
final class RoutePath implements RoutePathInterface
{
    /** @var string Passed on construct */
    private string $path;

    /** @var string Path key set representation ({wildcards} replaced by {n}) */
    private string $key;

    private int $wildcardBracesCount;

    private array $wildcardsMatch;

    private RouteWildcardsInterface $routeWildcards;

    /** @var array string[] */
    private array $wildcards;

    private string $regex;

    /**
     * Creates a new instance.
     *
     * @param string $path a path uri like `/path/{wildcard}`
     *
     * @throws RoutePathForwardSlashException       if $path doesn't start with forward slash
     * @throws RoutePathInvalidCharsException       if $path contains invalid chars
     * @throws RoutePathUnmatchedBracesException    if $path contains unmatched braces (must be paired)
     * @throws RoutePathUnmatchedWildcardsException if $path contains wildcards that don't match the number of braces
     * @throws RouteWildcardReservedException          if $path contains reserved wildcards
     * @throws RouteWildcardRepeatException            if $path contains repeated wildcards
     */
    public function __construct(string $path)
    {
        $this->path = $path;
        $this->assertFormat();
        $this->key = $this->path;
        if ($this->hasHandlebars()) {
            $this->wildcards = [];
            $this->wildcardsMatch = [];
            $this->wildcardBracesCount = 0;
            $this->assertMatchingBraces();
            $this->assertReservedWildcards();
            $this->assertMatchingWildcards();
            $this->handleWildcards();
            $this->handleSetRouteWildcards();
        }
        $this->handleSetRegex();
    }

    public function toString(): string
    {
        return $this->path;
    }

    public function key(): string
    {
        return $this->key;
    }

    public function regex(): string
    {
        return $this->regex;
    }

    public function hasRouteWildcards(): bool
    {
        return isset($this->routeWildcards);
    }

    public function routeWildcards(): RouteWildcardsInterface
    {
        return $this->routeWildcards;
    }

    /**
     * Return an instance with the specified added WildcardInterface.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified added WildcardInterface.
     *
     * @throws RouteWildcardNotFoundException if the wildcard doesn't exists in the instance
     */
    public function withWildcard(RouteWildcardInterface $routeWildcard): RoutePathInterface
    {
        $new = clone $this;
        $routeWildcard->assertRoutePath($new);
        $new->routeWildcards = $new->routeWildcards
            ->withAddedWildcard($routeWildcard);
        $new->handleSetRegex();

        return $new;
    }

    public function matchFor(string $requestUri): array
    {
        try {
            if (preg_match($this->regex, $requestUri, $matches)) {
                array_shift($matches);
                $return = [];
                foreach ($this->wildcards as $pos => $name) {
                    $return[$name] = $matches[$pos];
                }
            }
            // @codeCoverageIgnoreStart
        } catch (Throwable $e) {
            throw new LogicException(
                (new Message('Unable to find matches for %requestUri% by matching %regex%. Error thrown: %thrown%'))
                    ->code('%requestUri%', $requestUri)
                    ->code('%regex%', $this->regex)
                    ->strtr('%thrown%', $e->getMessage())
                    ->toString()
            );
        }
        // @codeCoverageIgnoreEnd

        return $return ?? [];
    }

    public function uriFor(array $wildcards): string
    {
        if (!isset($this->wildcards)) {
            throw new BadMethodCallException(
                (new Message('This method should be called only if the %className% instance contains wildcards'))
                    ->code('%className%', __CLASS__)
                    ->toString()
            );
        }
        $keys = array_keys($wildcards);
        $requiredKeys = $this->wildcards;
        $diff = array_diff($requiredKeys, $keys);
        if ($diff !== []) {
            throw new RoutePathUnmatchedBracesException(
                (new Message("Provided %provided% doesn't strictly map known wildcard names to its corresponding values"))
                    ->code('%provided%', 'array')
                    ->toString()
            );
        }
        $uri = $this->path;
        foreach ($wildcards as $name => $value) {
            $uri = str_replace(
                "{{$name}}",
                (string) $value,
                $uri
            );
        }

        return $uri;
    }

    private function assertFormat(): void
    {
        if ((new StrBool($this->path))->startsWith('/') === false) {
            throw new RoutePathForwardSlashException(
                (new Message('Route path %path% must start with a forward slash'))
                    ->code('%path%', $this->path)
                    ->toString()
            );
        }
        $illegals = $this->getIllegalChars();
        if ($illegals) {
            throw new RoutePathInvalidCharsException(
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
            throw new RoutePathUnmatchedBracesException(
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
        preg_match_all(RoutePathInterface::REGEX_WILDCARD_SEARCH, $this->path, $this->wildcardsMatch);
        $countMatches = count($this->wildcardsMatch[0]);
        if ($this->wildcardBracesCount !== $countMatches) {
            throw new RoutePathUnmatchedWildcardsCountException(
                (new Message('Route path %path% contains invalid wildcard declarations (pattern %pattern% matches %countMatches%)'))
                    ->code('%path%', $this->path)
                    ->strtr('%wildcardsCount%', (string) $this->wildcardBracesCount)
                    ->strtr('%countMatches%', (string) $countMatches)
                    ->code('%pattern%', RoutePathInterface::REGEX_WILDCARD_SEARCH)
                    ->toString()
            );
        }
    }

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
            throw new RouteWildcardReservedException(
                (new Message('Path %path% contain system reserved wildcards %list%'))
                    ->code('%path%', $this->path)
                    ->code('%list%', implode(' ', $matches[0]))
                    ->toString()
            );
        }
    }

    private function handleWildcards(): void
    {
        foreach ($this->wildcardsMatch[0] as $pos => $braced) {
            // Change {wildcard} to {n} (n is the wildcard index)
            if (isset($this->key)) {
                $this->key = (string) (new Str($this->key))->replaceFirst($braced, "{{$pos}}");
            }
            $wildcard = $this->wildcardsMatch[1][$pos];
            if (in_array($wildcard, $this->wildcards)) {
                throw new RouteWildcardRepeatException(
                    (new Message('Duplicated wildcard %wildcard% in path uri %path%'))
                        ->code('%wildcard%', $this->wildcardsMatch[0][$pos])
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

    private function handleSetRouteWildcards(): void
    {
        $this->routeWildcards = new RouteWildcards();
        foreach ($this->wildcards as $wildcardName) {
            $this->routeWildcards = $this->routeWildcards
                ->withAddedWildcard(new RouteWildcard($wildcardName));
        }
    }

    private function handleSetRegex(): void
    {
        $pseudoRegex = str_replace('/', '\/', $this->key);
        $regex = self::REGEX_DELIMITER_CHAR . '^' . $pseudoRegex . '$' . self::REGEX_DELIMITER_CHAR;
        if (isset($this->routeWildcards)) {
            foreach ($this->routeWildcards->toArray() as $pos => $wildcard) {
                $regex = str_replace(
                    "{{$pos}}",
                    '(' . $wildcard->match()->toString() . ')',
                    $regex
                );
            }
        }
        $this->regex = $regex;
    }
}
