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

namespace Chevere\Components\Http;

use InvalidArgumentException;
use Chevere\Components\Message\Message;
use Chevere\Components\Http\Interfaces\MethodInterface;

final class Method implements MethodInterface
{
    /** @var string HTTP request method name */
    private string $name;

    /**
     * Creates a new instance.
     *
     * @throws InvalidArgumentException if the $name isn't included in ACCEPT_METHOD_NAMES
     */
    public function __construct(string $name)
    {
        $this->name = $name;
        $this->assertMethod();
    }

    public function toString(): string
    {
        return $this->name;
    }

    private function assertMethod(): void
    {
        if (!in_array($this->name, MethodInterface::ACCEPT_METHOD_NAMES)) {
            throw new InvalidArgumentException(
                (new Message('Unknown HTTP method %method%'))
                    ->code('%method%', $this->name)
                    ->toString()
            );
        }
    }
}
