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

namespace Chevere\Components\Serialize;

use Chevere\Components\Type\Type;
use Chevere\Contracts\Serialize\UnserializeContract;
use Chevere\Contracts\Type\TypeContract;
use TypeError;

final class Unserialize implements UnserializeContract
{
    /** @var mixed */
    private $var;

    /** @var TypeContract */
    private $type;

    /**
     * {@inheritdoc}
     */
    public function __construct(string $serialized)
    {
        try {
            $this->var = unserialize($serialized);
        } catch (TypeError $e) {
            throw new InvalidArgumentException(
              (new Message('String provided is unable to unserialize: %message%'))
                  ->code('%message%', $e->getMessage())
                  ->toString()
            );
        }
        $type = is_object($this->var) ? get_class($this->var) : gettype($this->var);
        $this->type = new Type($type);
    }

    /**
     * {@inheritdoc}
     */
    public function var()
    {
        return $this->var;
    }

    /**
     * {@inheritdoc}
     */
    public function type(): TypeContract
    {
        return $this->type;
    }
}
