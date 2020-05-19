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
use Chevere\Components\Route\Exceptions\RouteNameInvalidException;
use Chevere\Interfaces\Route\RouteNameInterface;
use Chevere\Components\Str\StrAssert;

final class RouteName implements RouteNameInterface
{
    /** @var string */
    private string $name;

    /**
     * @throws StrAssertException If $name is empty or if it is ctype-space.
     * @throws RouteNameInvalidException if $name doesn't match RouteNameInterface::REGEX
     */
    public function __construct(string $name)
    {
        (new StrAssert($name))->notEmpty()->notCtypeSpace();
        $this->name = $name;
        $this->assertFormat();
    }

    public function toString(): string
    {
        return $this->name;
    }

    public function assertFormat(): void
    {
        if (!preg_match(RouteNameInterface::REGEX, $this->name)) {
            throw new RouteNameInvalidException(
                (new Message('Expecting at least one alphanumeric, underscore, hyphen or dot character, string %string% provided (regex %regex%)'))
                    ->code('%string%', $this->name === '' ? '(empty)' : $this->name)
                    ->code('%regex%', RouteNameInterface::REGEX)
            );
        }
    }
}
