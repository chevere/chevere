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
use Chevere\Components\Parameter\Traits\ParameterTrait;
use Chevere\Components\Regex\Regex;
use Chevere\Components\Type\Type;
use Chevere\Exceptions\Core\InvalidArgumentException;
use Chevere\Interfaces\Parameter\StringParameterInterface;
use Chevere\Interfaces\Regex\RegexInterface;
use Ds\Map;

/**
 * @method StringParameterInterface withDescription(string $description)
 * @method StringParameterInterface withAddedAttribute(string ...$attributes)
 */
final class StringParameter implements StringParameterInterface
{
    use ParameterTrait;

    private RegexInterface $regex;

    private string $default = '';

    public function __construct()
    {
        $this->type = new Type(Type::STRING);
        $this->attributes = new Map();
        $this->regex = new Regex('/^.*$/');
    }

    public function withRegex(RegexInterface $regex): StringParameterInterface
    {
        $new = clone $this;
        $new->regex = $regex;

        return $new;
    }

    public function withDefault(string $value): StringParameterInterface
    {
        if ($this->regex->match($value) === []) {
            throw new InvalidArgumentException(
                (new Message('Default value must match the parameter regex %regexString%'))
                    ->code('%regexString%', $this->regex->toString())
            );
        }
        $new = clone $this;
        $new->default = $value;

        return $new;
    }

    public function regex(): RegexInterface
    {
        return $this->regex;
    }

    public function default(): string
    {
        return $this->default;
    }
}
