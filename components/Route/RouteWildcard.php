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
use Chevere\Components\Str\StrBool;
use Chevere\Exceptions\Route\RouteWildcardInvalidCharsException;
use Chevere\Exceptions\Route\RouteWildcardStartWithNumberException;
use Chevere\Interfaces\Route\RouteWildcardInterface;
use Chevere\Interfaces\Route\RouteWildcardMatchInterface;

final class RouteWildcard implements RouteWildcardInterface
{
    /** @var string */
    private string $name;

    private RouteWildcardMatchInterface $match;

    /**
     * @param string $name  The wildcard name
     *
     * @throws RouteWildcardStartWithNumberException if $name starts with a number
     * @throws RouteWildcardInvalidCharsException    if $name contains invalid chars
     */
    public function __construct(string $name, RouteWildcardMatchInterface $match)
    {
        $this->name = $name;
        $this->assertName();
        $this->match = $match;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function match(): RouteWildcardMatchInterface
    {
        return $this->match;
    }

    private function assertName(): void
    {
        if ((new StrBool($this->name))->startsWithCtypeDigit()) {
            throw new RouteWildcardStartWithNumberException(
                (new Message('String %string% must not start with a numeric value'))
                    ->code('%string%', $this->name)
            );
        }
        if (!preg_match(RouteWildcardInterface::ACCEPT_CHARS_REGEX, $this->name)) {
            throw new RouteWildcardInvalidCharsException(
                (new Message('String %string% must contain only alphanumeric and underscore characters'))
                    ->code('%string%', $this->name)
            );
        }
    }
}
