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

namespace Chevere\Components\Router;

use Chevere\Components\Message\Message;
use Chevere\Components\Regex\Interfaces\RegexInterface;
use Chevere\Components\Router\Exceptions\RouteNotFoundException;
use Chevere\Components\Router\Exceptions\RouterException;
use Chevere\Components\Router\Interfaces\RoutedInterface;
use Chevere\Components\Router\Interfaces\RouterRegexInterface;
use InvalidArgumentException;
use Psr\Http\Message\UriInterface;
use Throwable;

final class RouterRegex implements RouterRegexInterface
{
    private RegexInterface $regex;

    public function __construct(RegexInterface $regex)
    {
        $this->regex = $regex;
        $this->assertFormat();
    }

    public function regex(): RegexInterface
    {
        return $this->regex;
    }

    private function assertFormat(): void
    {
        if (!preg_match(self::MATCHER, $this->regex->toString())) {
            throw new InvalidArgumentException(
                (new Message('Invalid regex pattern %regex% (validated against %matcher%)'))
                    ->code('%regex%', $this->regex->toString())
                    ->code('%matcher%', self::MATCHER)
                    ->toString()
            );
        }
    }
}
