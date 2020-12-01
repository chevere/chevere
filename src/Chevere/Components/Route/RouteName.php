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
use Chevere\Components\Str\StrAssert;
use Chevere\Exceptions\Route\RouteNameInvalidException;
use Chevere\Interfaces\Route\RouteNameInterface;

final class RouteName implements RouteNameInterface
{
    private string $name;

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
