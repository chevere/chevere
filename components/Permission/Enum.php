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

namespace Chevere\Components\Permission;

use Chevere\Components\Message\Message;
use Chevere\Exceptions\Core\InvalidArgumentException;
use Chevere\Exceptions\Core\LogicException;
use Chevere\Interfaces\Permission\EnumInterface;

abstract class Enum implements EnumInterface
{
    private array $accept;

    private string $value;

    public function __construct(string $value)
    {
        $this->accept = $this->getAccept();
        if ($this->accept === []) {
            throw new LogicException(
                new Message('Missing enum definition')
            );
        }
        $this->value = $value;
        $this->assert();
    }

    public function getAccept(): array
    {
        return [];
    }

    final public function value(): string
    {
        return $this->value;
    }

    private function assert(): void
    {
        if (!in_array($this->value, $this->accept)) {
            throw new InvalidArgumentException(
                (new Message('Expecting %expecting%, %provided% provided'))
                    ->code('%expecting%', implode(', ', $this->accept))
                    ->code('%provided%', $this->value)
            );
        }
    }
}
