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

namespace Chevere\Parameter;

use Chevere\Parameter\Interfaces\StringParameterInterface;
use Chevere\Parameter\Traits\ParameterTrait;
use Chevere\Regex\Interfaces\RegexInterface;
use Chevere\Regex\Regex;
use Chevere\Throwable\Exceptions\InvalidArgumentException;
use Chevere\Type\Interfaces\TypeInterface;
use function Chevere\Message\message;
use function Chevere\Type\typeString;

final class StringParameter implements StringParameterInterface
{
    use ParameterTrait;

    private RegexInterface $regex;

    private ?string $default = null;

    public function setUp(): void
    {
        $this->regex = new Regex(self::REGEX_DEFAULT);
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
                message('Default value `%value%` must match the parameter regex %regexString%')
                    ->withCode('%value%', $value)
                    ->withCode('%regexString%', $this->regex->__toString())
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

    public function default(): ?string
    {
        return $this->default;
    }

    public function schema(): array
    {
        return [
            'type' => $this->type()->primitive(),
            'description' => $this->description(),
            'default' => $this->default(),
            'regex' => $this->regex()->noDelimiters(),
        ];
    }

    public function assertCompatible(StringParameterInterface $parameter): void
    {
        if ($this->regex->__toString() === $parameter->regex()->__toString()) {
            return;
        }

        throw new InvalidArgumentException(
            message('Expected regex %expected%, provided %provided%')
                ->withCode('%expected%', $this->regex->__toString())
                ->withCode('%provided%', $parameter->regex()->__toString())
        );
    }

    private function getType(): TypeInterface
    {
        return typeString();
    }
}
