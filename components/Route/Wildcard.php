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

use Chevere\Components\Str\Exceptions\StrAssertException;
use Chevere\Components\Route\Exceptions\WildcardInvalidCharsException;
use Chevere\Components\Message\Message;
use Chevere\Components\Route\Exceptions\WildcardNotFoundException;
use Chevere\Components\Route\Exceptions\WildcardStartWithNumberException;
use Chevere\Components\Route\Interfaces\WildcardMatchInterface;
use Chevere\Components\Route\Interfaces\RoutePathInterface;
use Chevere\Components\Route\Interfaces\WildcardInterface;
use Chevere\Components\Str\StrAssert;

final class Wildcard implements WildcardInterface
{
    /** @var string */
    private string $name;

    /** @var string */
    private string $string;

    private WildcardMatchInterface $match;

    /**
     * Creates a new instance.
     *
     * @param string $name  The wildcard name
     *
     * @throws WildcardStartWithNumberException if $name starts with a number
     * @throws WildcardInvalidCharsException    if $name contains invalid chars
     */
    public function __construct(string $name)
    {
        $this->name = $name;
        $this->string = "{{$this->name}}";
        $this->assertName();
        $this->match = new WildcardMatch(WildcardInterface::REGEX_MATCH_DEFAULT);
    }

    public function withMatch(WildcardMatchInterface $regexMatch): WildcardInterface
    {
        $new = clone $this;
        $new->match = $regexMatch;

        return $new;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function toString(): string
    {
        return $this->string;
    }

    public function match(): WildcardMatchInterface
    {
        return $this->match;
    }

    public function assertRoutePath(RoutePathInterface $routePath): void
    {
        $noWildcard = false === strpos($routePath->toString(), $this->string);
        if ($noWildcard) {
            throw new WildcardNotFoundException(
                (new Message("Wildcard %wildcard% doesn't exists in route %toString%"))
                    ->code('%wildcard%', $this->string)
                    ->code('%path%', $routePath->toString())
                    ->toString()
            );
        }
    }

    private function assertName(): void
    {
        try {
            (new StrAssert($this->name))->notStartsWithCtypeDigit();
        } catch (StrAssertException $e) {
            throw new WildcardStartWithNumberException(
                (new Message('String %string% must not start with a numeric value'))
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
