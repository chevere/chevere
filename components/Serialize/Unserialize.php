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

namespace Chevere\Components\Serialize;

use Chevere\Components\Message\Message;
use Chevere\Components\Type\Type;
use Chevere\Exceptions\Serialize\UnserializeException;
use Chevere\Interfaces\Serialize\UnserializeInterface;
use Chevere\Interfaces\Type\TypeInterface;
use function Chevere\Components\Type\varType;

final class Unserialize implements UnserializeInterface
{
    private $var;

    private TypeInterface $type;

    public function __construct(string $serialized)
    {
        $this->var = @unserialize($serialized);
        if ($this->var === false) {
            throw new UnserializeException(
                new Message('Unable to unserialize provided string.')
            );
        }
        $type = is_object($this->var) ? get_class($this->var) : varType($this->var);
        $this->type = new Type($type);
    }

    public function var()
    {
        return $this->var;
    }

    public function type(): TypeInterface
    {
        return $this->type;
    }
}
