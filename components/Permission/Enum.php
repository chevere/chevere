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

use Chevere\Components\Description\Traits\DescriptorTrait;
use Chevere\Components\Message\Message;
use Chevere\Components\Permission\Traits\IdentifierTrait;
use Chevere\Exceptions\Core\InvalidArgumentException;
use Chevere\Exceptions\Core\LogicException;
use Chevere\Interfaces\Permission\EnumInterface;

abstract class Enum implements EnumInterface
{
    use DescriptorTrait;
    use IdentifierTrait;

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
        $this->assert($this->getDefault());
        $this->assert($value);
        $this->value = $value;
    }

    abstract public function getDefault(): string;

    public function getAccept(): array
    {
        return [];
    }

    final public function value(): string
    {
        return $this->value;
    }

    private function assert(string $value): void
    {
        if (!in_array($value, $this->accept)) {
            throw new InvalidArgumentException(
                (new Message('Expecting %expecting%, %provided% provided'))
                    ->code('%expecting%', implode(', ', $this->accept))
                    ->code('%provided%', $value)
            );
        }
    }
}
