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

use Throwable;
use Chevere\Components\Serialize\Exceptions\UnserializeException;
use Chevere\Components\Message\Message;
use Chevere\Components\Type\Type;
use Chevere\Components\Serialize\Interfaces\UnserializeInterface;
use Chevere\Components\Type\Interfaces\TypeInterface;
use function ChevereFn\varType;

final class Unserialize implements UnserializeInterface
{
    /** @var mixed */
    private $var;

    private TypeInterface $type;

    /**
     * Creates a new instance.
     *
     * @throws UnserializeException if $serialized can't be unserialized
     */
    public function __construct(string $serialized)
    {
        try {
            $this->var = unserialize($serialized);
        } catch (Throwable $e) {
            throw new UnserializeException(
                (new Message('String provided is unable to unserialize: %message%'))
                    ->code('%message%', $e->getMessage())
                    ->toString()
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
