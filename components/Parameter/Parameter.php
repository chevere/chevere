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

namespace Chevere\Components\Parameter;

use Chevere\Components\Message\Message;
use Chevere\Components\Regex\Regex;
use Chevere\Components\Str\StrAssert;
use Chevere\Exceptions\Core\Exception;
use Chevere\Exceptions\Core\InvalidArgumentException;
use Chevere\Exceptions\Core\OutOfBoundsException;
use Chevere\Exceptions\Core\OverflowException;
use Chevere\Exceptions\Parameter\ParameterNameInvalidException;
use Chevere\Interfaces\Parameter\ParameterInterface;
use Chevere\Interfaces\Regex\RegexInterface;
use Ds\Set;
use function DeepCopy\deep_copy;

abstract class Parameter implements ParameterInterface
{
    protected string $name;

    protected RegexInterface $regex;

    protected string $description = '';

    private string $default = '';

    protected Set $attributes;

    public function __construct(string $name)
    {
        $this->regex = new Regex('/^.*$/');
        $this->name = $name;
        $this->assertName();
        $this->attributes = new Set;
    }

    public function __clone()
    {
        $this->attributes = $this->attributes->copy();
    }

    public function withRegex(RegexInterface $regex): ParameterInterface
    {
        $new = clone $this;
        $new->regex = $regex;

        return $new;
    }

    public function withDescription(string $description): ParameterInterface
    {
        $new = clone $this;
        $new->description = $description;

        return $new;
    }

    public function withDefault(string $default): ParameterInterface
    {
        if ($this->regex->match($default) == []) {
            throw new InvalidArgumentException(
                (new Message('Default value must match the parameter regex %regexString%'))
                    ->code('%regexString%', $this->regex->toString())
            );
        }
        $new = clone $this;
        $new->default = $default;

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
        $new = clone $this;
        $new->attributes->remove($attribute);

        return $new;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function regex(): RegexInterface
    {
        return $this->regex;
    }

    public function description(): string
    {
        return $this->description;
    }

    public function default(): string
    {
        return $this->default;
    }

    public function hasAttribute(string $attribute): bool
    {
        return $this->attributes->contains($attribute);
    }

    public function attributes(): Set
    {
        return deep_copy($this->attributes);
    }

    protected function assertName(): void
    {
        try {
            (new StrAssert($this->name))
                ->notEmpty()
                ->notCtypeSpace()
                ->notContains(' ');
        } catch (Exception $e) {
            throw new ParameterNameInvalidException(
                new Message('Invalid parameter name')
            );
        }
    }
}
