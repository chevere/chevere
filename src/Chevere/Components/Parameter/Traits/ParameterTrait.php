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

namespace Chevere\Components\Parameter\Traits;

use Chevere\Components\Description\Traits\DescriptionTrait;
use Chevere\Components\Message\Message;
use Chevere\Components\Str\StrAssert;
use Chevere\Exceptions\Core\Exception;
use Chevere\Exceptions\Core\OutOfBoundsException;
use Chevere\Exceptions\Core\OverflowException;
use Chevere\Exceptions\Parameter\ParameterNameInvalidException;
use Chevere\Interfaces\Parameter\ParameterInterface;
use Chevere\Interfaces\Type\TypeInterface;
use Ds\Set;

trait ParameterTrait
{
    use DescriptionTrait;
    
    private TypeInterface $type;

    private Set $attributes;

    public function __clone()
    {
        $this->attributes = new Set($this->attributes->toArray());
    }

    public function withDescription(string $description): ParameterInterface
    {
        /**
         * @var ParameterInterface $new
         */
        $new = clone $this;
        $new->description = $description;

        return $new;
    }

    public function withAddedAttribute(string $attribute): ParameterInterface
    {
        if ($this->hasAttribute($attribute)) {
            throw new OverflowException(
                (new Message('Attribute %attribute% has been already added'))
                    ->strong('%attribute%', $attribute)
            );
        }
        /**
         * @var ParameterInterface $new
         */
        $new = clone $this;
        $new->attributes->add($attribute);

        return $new;
    }

    public function withRemovedAttribute(string $attribute): ParameterInterface
    {
        if (!$this->hasAttribute($attribute)) {
            throw new OutOfBoundsException(
                (new Message("Attribute %attribute% doesn't exists"))
                    ->strong('%attribute%', $attribute)
            );
        }
        /**
         * @var ParameterInterface $new
         */
        $new = clone $this;
        $new->attributes->remove($attribute);

        return $new;
    }

    public function type(): TypeInterface
    {
        return $this->type;
    }

    public function description(): string
    {
        return $this->description;
    }

    public function hasAttribute(string $attribute): bool
    {
        return $this->attributes->contains($attribute);
    }

    public function attributes(): Set
    {
        return $this->attributes;
    }
}
