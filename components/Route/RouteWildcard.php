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

use Chevere\Components\Message\Message;
use Chevere\Components\Route\Exceptions\RouteWildcardInvalidCharsException;
use Chevere\Components\Route\Exceptions\RouteWildcardNotFoundException;
use Chevere\Components\Route\Exceptions\RouteWildcardStartWithNumberException;
use Chevere\Components\Route\Interfaces\RoutePathInterface;
use Chevere\Components\Route\Interfaces\RouteWildcardInterface;
use Chevere\Components\Route\Interfaces\RouteWildcardMatchInterface;
use Chevere\Components\Str\Exceptions\StrAssertException;
use Chevere\Components\Str\StrAssert;
use Chevere\Components\Str\StrBool;

final class RouteWildcard implements RouteWildcardInterface
{
    /** @var string */
    private string $name;

    /** @var string */
    private string $string;

    private RouteWildcardMatchInterface $match;

    /**
     * Creates a new instance.
     *
     * @param string $name  The wildcard name
     *
     * @throws RouteWildcardStartWithNumberException if $name starts with a number
     * @throws RouteWildcardInvalidCharsException    if $name contains invalid chars
     */
    public function __construct(string $name)
    {
        $this->name = $name;
        $this->string = "{{$this->name}}";
        $this->assertName();
        $this->match = new RouteWildcardMatch(RouteWildcardInterface::REGEX_MATCH_DEFAULT);
    }

    public function withMatch(RouteWildcardMatchInterface $wildcardMatch): RouteWildcardInterface
    {
        $new = clone $this;
        $new->match = $wildcardMatch;

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

    public function match(): RouteWildcardMatchInterface
    {
        return $this->match;
    }

    public function assertRoutePath(RoutePathInterface $routePath): void
    {
        $noWildcard = false === strpos($routePath->toString(), $this->string);
        if ($noWildcard) {
            throw new RouteWildcardNotFoundException(
                (new Message("Wildcard %wildcard% doesn't exists in route %toString%"))
                    ->code('%wildcard%', $this->string)
                    ->code('%path%', $routePath->toString())
                    ->toString()
            );
        }
    }

    private function assertName(): void
    {
        if ((new StrBool($this->name))->startsWithCtypeDigit()) {
            throw new RouteWildcardStartWithNumberException(
                (new Message('String %string% must not start with a numeric value'))
                    ->code('%string%', $this->name)
                    ->toString()
            );
        }
        if (!preg_match(RouteWildcardInterface::ACCEPT_CHARS_REGEX, $this->name)) {
            throw new RouteWildcardInvalidCharsException(
                (new Message('String %string% must contain only alphanumeric and underscore characters'))
                    ->code('%string%', $this->name)
                    ->toString()
            );
        }
    }
}
