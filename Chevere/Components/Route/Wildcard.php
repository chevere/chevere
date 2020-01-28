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

use Chevere\Components\Route\Exceptions\WildcardInvalidCharsException;
use Chevere\Components\Message\Message;
use Chevere\Components\Regex\RegexMatch;
use Chevere\Components\Route\Exceptions\WildcardNotFoundException;
use Chevere\Components\Route\Exceptions\WildcardStartWithNumberException;
use Chevere\Components\Regex\Interfaces\RegexMatchInterface;
use Chevere\Components\Route\Interfaces\PathUriInterface;
use Chevere\Components\Route\Interfaces\WildcardInterface;
use function ChevereFn\stringStartsWithNumeric;

final class Wildcard implements WildcardInterface
{
    /** @var string */
    private string $name;

    /** @var string */
    private string $wildcard;

    private RegexMatchInterface $regexMatch;

    /**
     * Creates a new instance.
     *
     * @param string $name  The wildcard name
     * @param string $regex The regex patter, without delimeters
     *
     * @throws WildcardStartWithNumberException if $name starts with a number
     * @throws WildcardInvalidCharsException    if $name contains invalid chars
     */
    public function __construct(string $name)
    {
        $this->name = $name;
        $this->wildcard = "{{$this->name}}";
        $this->assertName();
        $this->regexMatch = new RegexMatch(WildcardInterface::REGEX_MATCH_DEFAULT);
    }

    /**
     * {@inheritdoc}
     */
    public function withRegexMatch(RegexMatchInterface $regexMatch): WildcardInterface
    {
        $new = clone $this;
        $new->regexMatch = $regexMatch;

        return $new;
    }

    /**
     * {@inheritdoc}
     */
    public function name(): string
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function toString(): string
    {
        return $this->wildcard;
    }

    /**
     * {@inheritdoc}
     */
    public function regexMatch(): RegexMatchInterface
    {
        return $this->regexMatch;
    }

    /**
     * {@inheritdoc}
     */
    public function assertPathUri(PathUriInterface $pathUri): void
    {
        $noWildcard = false === strpos($pathUri->toString(), $this->wildcard);
        if ($noWildcard) {
            throw new WildcardNotFoundException(
                (new Message("Wildcard %wildcard% doesn't exists in route %toString%"))
                    ->code('%wildcard%', $this->wildcard)
                    ->code('%path%', $pathUri->toString())
                    ->toString()
            );
        }
    }

    private function assertName(): void
    {
        if (stringStartsWithNumeric($this->name)) {
            throw new WildcardStartWithNumberException(
                (new Message("String %string% shouldn't start with a numeric value"))
                    ->code('%string%', $this->name)
                    ->toString()
            );
        }
        if (!preg_match(WildcardInterface::ACCEPT_CHARS_REGEX, $this->name)) {
            throw new WildcardInvalidCharsException(
                (new Message('String %string% must contain only alphanumeric and underscore characters'))
                    ->code('%string%', $this->name)
                    ->toString()
            );
        }
    }
}