<?php

/*
 * This file is part of Chevere.
 *
 * (c) Rodolfo Berrios <rodolfo@chevereto.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Chevere\Components\Controller;

use Chevere\Components\Message\Message;
use Chevere\Contracts\Controller\ControllerContract;
use Chevere\Contracts\Controller\ControllerNameContract;
use InvalidArgumentException;

final class ControllerName implements ControllerNameContract
{
    /** @var string */
    private $name;

    /**
     * {@inheritdoc}
     *
     * @throws InvalidArgumentException if $name doesn't exists or if it doesn't implement a ControllerContract
     */
    public function __construct(string $name)
    {
        $this->name = $name;
        $this->assertControllerName();
    }

    /**
     * {@inheritdoc}
     */
    public function toString(): string
    {
        return $this->name;
    }

    private function assertControllerName(): void
    {
        if (!class_exists($this->name)) {
            throw new InvalidArgumentException(
                (new Message("Controller %controller% doesn't exists"))
                    ->code('%controller%', $this->name)
                    ->toString()
            );
        }
        if (!is_subclass_of($this->name, ControllerContract::class)) {
            throw new InvalidArgumentException(
                (new Message('Controller %controller% must implement the %contract% interface'))
                    ->code('%controller%', $this->name)
                    ->code('%contract%', ControllerContract::class)
                    ->toString()
            );
        }
    }
}
