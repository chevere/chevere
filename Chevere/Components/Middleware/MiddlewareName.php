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

namespace Chevere\Components\Middleware;

use InvalidArgumentException;
use Chevere\Components\Middleware\Exceptions\MiddlewareInterfaceException;
use Chevere\Components\Message\Message;
use Chevere\Components\Middleware\Interfaces\MiddlewareInterface;
use Chevere\Components\Middleware\Interfaces\MiddlewareNameInterface;

final class MiddlewareName implements MiddlewareNameInterface
{
    private string $name;

    /**
     * Creates a new instance.
     *
     * @param string $name A middleware name implementing the MiddlewareInterface
     *
     * @throws InvalidArgumentException    if $name represents non existent class
     * @throws MiddlewareInterfaceException if the $name doesn't represent a class implementing the MiddlewareInterface
     */
    public function __construct(string $name)
    {
        $this->name = $name;
        $this->assertMiddlewareInterface();
    }

    public function toString(): string
    {
        return $this->name;
    }

    private function assertMiddlewareInterface(): void
    {
        if (!class_exists($this->name)) {
            throw new InvalidArgumentException(
                (new Message("Middleware %middleware% doesn't exists"))
                    ->code('%middleware%', $this->name)
                    ->toString()
            );
        }
        $interfaces = class_implements($this->name);
        if (false === $interfaces || !in_array(MiddlewareInterface::class, $interfaces)) {
            throw new MiddlewareInterfaceException(
                (new Message('Middleware %middleware% must implement the %contract% contract'))
                    ->code('%middleware%', $this->name)
                    ->code('%contract%', MiddlewareInterface::class)
                    ->toString()
            );
        }
    }
}
