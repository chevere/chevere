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
use Ds\Map;

trait AttributesTrait
{
    private Map $attributes;

    public function withAddedAttribute(string ...$attributes): static
    {
        $new = clone $this;
        foreach ($attributes as $name => $attribute) {
            $name = strval($name);
            $new->attributes ??= new Map();
            if ($new->hasAttribute($name)) {
                throw new OverflowException(
                    (new Message('Attribute %attribute% has been already added'))
                        ->strong('%attribute%', $name)
                );
            }
            $new->attributes = $new->attributes->copy();
            $new->attributes->put($name, $attribute);
        }

        return $new;
    }

    public function withoutAttribute(string ...$attributes): static
    {
        $new = clone $this;
        foreach ($attributes as $name => $attribute) {
            $name = strval($name);
            if (! $new->hasAttribute($name)) {
                throw new OutOfBoundsException(
                    (new Message("Attribute %attribute% doesn't exists"))
                        ->strong('%attribute%', $name)
                );
            }

            $new->attributes->remove($name);
        }

        return $new;
    }

    public function hasAttribute(string ...$attributes): bool
    {
        if ($this->attributes->count() === 0) {
            return false;
        }
        foreach ($attributes as $attribute) {
            if ($this->attributes->hasKey($attribute) === false) {
                return false;
            }
        }

        return true;
    }

    public function attributes(): Map
    {
        return $this->attributes ??= new Map();
    }
}
