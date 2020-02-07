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
use Chevere\Components\Route\Exceptions\RouteInvalidNameException;
use Chevere\Components\Route\Interfaces\RouteNameInterface;

final class RouteName implements RouteNameInterface
{
    /** @var string */
    private string $name;

    /**
     * Creates a new instance.
     *
     * @throws RouteInvalidNameException if $name doesn't match REGEX
     */
    public function __construct(string $name)
    {
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
            throw new RouteInvalidNameException(
                (new Message('Expecting at least one alphanumeric, underscore, hypen or dot character, string %string% provided (regex %regex%)'))
                    ->code('%string%', $this->name === '' ? '(empty)' : $this->name)
                    ->code('%regex%', RouteNameInterface::REGEX)
                    ->toString()
            );
        }
    }
}
