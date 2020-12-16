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
use Chevere\Components\Regex\Regex;
use Chevere\Exceptions\Route\RouteNameInvalidException;
use Chevere\Interfaces\Route\RouteNameInterface;

final class RouteName implements RouteNameInterface
{
    private string $name;

    private string $repository;

    private string $path;

    public function __construct(string $name)
    {
        $match = (new Regex(RouteNameInterface::REGEX))->match($name);
        if ($match === []) {
            throw new RouteNameInvalidException(
                (new Message('Name must match %regex%'))
                    ->code('%regex%', RouteNameInterface::REGEX)
            );
        }
        $this->name = $name;
        $this->repository = $match[1];
        $this->path = $match[2];
    }

    public function toString(): string
    {
        return $this->name;
    }

    public function repository(): string
    {
        return $this->repository;
    }

    public function path(): string
    {
        return $this->path;
    }
}
