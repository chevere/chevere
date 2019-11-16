<?php

/*
 * This file is part of Chevere.
 *
 * (c) Rodolfo Berrios <rodolfo@chevereto.com>
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
use Chevere\Components\Route\Exceptions\WildcardReservedException;
use Chevere\Contracts\Route\PathUriContract;
use Chevere\Contracts\Route\PathUriWildcardsContract;
use function ChevereFn\stringStartsWith;

final class PathUri implements PathUriContract
{
    /** @var string */
    private $path;

    /** @var bool */
    private $hasWildcards;

    /** @var int */
    private $wildcardsCount;

    /** @var array */
    private $wildcardsMatch;

    /**
     * {@inheritdoc}
     */
    public function __construct(string $path)
    {
        $this->path = $path;
        $this->wildcardsCount = 0;
        $this->wildcardsMatch = [];
        $this->setHasWildcards();
        $this->assertFormat();
        if ($this->hasWildcards) {
            $this->assertMatchingBraces();
            $this->assertMatchingWildcards();
            $this->assertReservedWildcards();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function path(): string
    {
        return $this->path;
    }

    /**
     * {@inheritdoc}
     */
    public function hasWildcards(): bool
    {
        return $this->hasWildcards;
    }

    /**
     * {@inheritdoc}
     */
    public function wildcardsMatch(): array
    {
        return $this->wildcardsMatch;
    }

    private function assertFormat(): void
    {
        if (!stringStartsWith('/', $this->path)) {
            throw new PathUriForwardSlashException(
                (new Message('Route path %path% must start with a forward slash'))
                    ->code('%path%', $this->path)
                    ->toString()
            );
        }
        $illegals = $this->getIllegalChars();
        if (!empty($illegals)) {
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
        $this->wildcardsCount = $countOpen;
    }

    private function assertMatchingWildcards(): void
    {
        preg_match_all(PathUriWildcardsContract::REGEX_WILDCARD_SEARCH, $this->path, $this->wildcardsMatch);
        $countMatches = count($this->wildcardsMatch[0]);
        if ($this->wildcardsCount !== $countMatches) {
            throw new PathUriUnmatchedWildcardsException(
                (new Message('Route path %path% contains invalid wildcard declarations (pattern %pattern% matches %countMatches%)'))
                    ->code('%path%', $this->path)
                    ->strtr('%wildcardsCount%', (string) $this->wildcardsCount)
                    ->strtr('%countMatches%', (string) $countMatches)
                    ->code('%pattern%', PathUriWildcardsContract::REGEX_WILDCARD_SEARCH)
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
        if (!(0 === preg_match_all('/{([0-9]+)}/', $this->path))) {
            throw new WildcardReservedException(
                (new Message('Wildcards in the form of %form% are reserved'))
                    ->code('%form%', '/{n}')
                    ->toString()
            );
        }
    }

    private function setHasWildcards(): void
    {
        $this->hasWildcards = false !== strpos($this->path, '{') || false !== strpos($this->path, '}');
    }
}
