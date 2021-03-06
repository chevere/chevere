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

namespace Chevere\Components\Common\Traits;

use Chevere\Components\Message\Message;
use Chevere\Exceptions\Core\OutOfBoundsException;
use Chevere\Exceptions\Core\OverflowException;
use Ds\Set;

trait AttributesTrait
{
    private Set $attributes;

    public function withAddedAttribute(string ...$attributes): static
    {
        $new = clone $this;
        foreach ($attributes as $attribute) {
            if ($this->hasAttribute($attribute)) {
                throw new OverflowException(
                    (new Message('Attribute %attribute% has been already added'))
                        ->strong('%attribute%', $attribute)
                );
            }
            $new->attributes->add($attribute);
        }

        return $new;
    }

    public function withoutAttribute(string ...$attributes): static
    {
        $new = clone $this;
        foreach ($attributes as $attribute) {
            if (! $this->hasAttribute($attribute)) {
                throw new OutOfBoundsException(
                    (new Message("Attribute %attribute% doesn't exists"))
                        ->strong('%attribute%', $attribute)
                );
            }

            $new->attributes->remove($attribute);
        }

        return $new;
    }

    public function hasAttribute(string ...$attributes): bool
    {
        return $this->attributes->contains(...$attributes);
    }

    public function attributes(): Set
    {
        return $this->attributes;
    }
}
