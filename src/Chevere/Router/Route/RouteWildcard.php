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

namespace Chevere\Router\Route;

use Chevere\Message\Message;
use Chevere\Router\Exceptions\Route\RouteWildcardInvalidException;
use Chevere\Router\Interfaces\Route\RouteWildcardInterface;
use Chevere\Router\Interfaces\Route\RouteWildcardMatchInterface;
use Chevere\Str\StrBool;

final class RouteWildcard implements RouteWildcardInterface
{
    public function __construct(
        private string $name,
        private  RouteWildcardMatchInterface $match
    ) {
        $this->assertName();
    }

    public function __toString(): string
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
            throw new RouteWildcardInvalidException(
                (new Message('String %string% must not start with a numeric value'))
                    ->code('%string%', $this->name)
            );
        }
        if (!preg_match(RouteWildcardInterface::ACCEPT_CHARS_REGEX, $this->name)) {
            throw new RouteWildcardInvalidException(
                (new Message('String %string% must contain only alphanumeric and underscore characters'))
                    ->code('%string%', $this->name)
            );
        }
    }
}
