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

namespace Chevere\Components\Http;

use Chevere\Components\Message\Message;
use Chevere\Contracts\Http\MethodContract;
use InvalidArgumentException;

final class Method implements MethodContract
{
    /** @var string HTTP request method name */
    private string $name;

    /**
     * {@inheritdoc}
     */
    public function __construct(string $name)
    {
        $this->name = $name;
        $this->assertMethod();
    }

    /**
     * {@inheritdoc}
     */
    public function toString(): string
    {
        return $this->name;
    }

    private function assertMethod(): void
    {
        if (!in_array($this->name, MethodContract::ACCEPT_METHOD_NAMES)) {
            throw new InvalidArgumentException(
                (new Message('Unknown HTTP method %method%'))
                    ->code('%method%', $this->name)
                    ->toString()
            );
        }
    }
}
