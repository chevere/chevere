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
use Chevere\Exceptions\Core\ArgumentCountException;
use Chevere\Exceptions\Core\InvalidArgumentException;
use Chevere\Exceptions\Core\OutOfBoundsException;
use Chevere\Exceptions\Parameter\ArgumentRegexMatchException;
use Chevere\Exceptions\Parameter\ArgumentRequiredException;
use Chevere\Interfaces\Parameter\ArgumentsInterface;
use Chevere\Interfaces\Parameter\ParameterInterface;
use Chevere\Interfaces\Parameter\ParametersInterface;
use Chevere\Interfaces\Parameter\StringParameterInterface;
use Throwable;
use function Chevere\Components\Type\varType;

final class Arguments implements ArgumentsInterface
{
    private ParametersInterface $parameters;

    public array $arguments;

    private array $errors;

    public function __construct(ParametersInterface $parameters, mixed ...$namedArguments)
    {
        $this->parameters = $parameters;
        $this->arguments = $namedArguments;
        $this->assertCount();
        $this->errors = [];
        foreach ($this->parameters->getGenerator() as $parameter) {
            $this->handleParameter($parameter);
        }
        if ($this->errors !== []) {
            throw new InvalidArgumentException(
                (new Message(implode(', ', $this->errors)))
            );
        }
    }

    private function assertCount(): void
    {
        $numRequired = $this->parameters->required()->count();
        $numArguments = count($this->arguments);
        if (($numRequired == 0 && $numArguments > 0) || $numRequired > $numArguments) {
            throw new ArgumentCountException(
                (new Message('Expecting %numParameters% (%expected%) required arguments, %numArguments% provided'))
                    ->code('%numParameters%', (string) $numRequired)
                    ->code('%expected%', implode(', ', $this->parameters->required()->toArray()))
                    ->code('%numArguments%', (string) $numArguments)
            );
        }
    }

    public function parameters(): ParametersInterface
    {
        return $this->parameters;
    }

    public function toArray(): array
    {
        return $this->arguments;
    }

    public function withArgument(string $name, $value): ArgumentsInterface
    {
        $this->assertHasParameter($name);
        $this->assertType($name, $value);
        $new = clone $this;
        $new->arguments[$name] = $value;

        return $new;
    }

    public function has(string $name): bool
    {
        return isset($this->arguments[$name]);
    }

    public function get(string $name)
    {
        try {
            return $this->arguments[$name];
        } catch (Throwable $e) {
            throw new OutOfBoundsException(
                (new Message('Argument %name% not found'))
                    ->code('%name%', $name),
            );
        }
    }

    public function getBoolean(string $boolean): bool
    {
        return $this->get($boolean);
    }

    public function getString(string $string): string
    {
        return $this->get($string);
    }

    public function getInteger(string $integer): int
    {
        return $this->get($integer);
    }

    public function getFloat(string $float): float
    {
        return $this->get($float);
    }

    public function getArray(string $array): array
    {
        return $this->get($array);
    }

    private function assertType(string $name, $value): void
    {
        $parameter = $this->parameters->get($name);
        $type = $parameter->type();
        if (!$type->validate($value)) {
            throw new InvalidArgumentException(
                (new Message('Parameter %name%: Expecting value of type %expected%, %provided% provided'))
                    ->strong('%name%', $name)
                    ->strong('%expected%', $type->typeHinting())
                    ->code('%provided%', varType($value))
            );
        }
        if ($parameter instanceof StringParameterInterface) {
            /**
             * @var StringParameterInterface $parameter
             */
            $this->assertStringArgument($parameter, $value);
        }
    }

    /**
     * @throws ArgumentRequiredException
     */
    private function assertHasParameter(string $name): void
    {
        if ($this->parameters->has($name) === false) {
            throw new ArgumentRequiredException(
                (new Message('Parameter %parameter% not found'))
                    ->code('%parameter%', $name)
            );
        }
    }

    /**
     * @throws ArgumentRegexMatchException
     */
    private function assertStringArgument(StringParameterInterface $parameter, string $argument): void
    {
        $regexString = $parameter->regex()->toString();
        if (preg_match($regexString, $argument) !== 1) {
            throw new ArgumentRegexMatchException(
                (new Message("Parameter %name%: Argument provided doesn't match the regex %regex%"))
                    ->strong('%name%', $parameter->name())
                    ->code('%parameter%', $parameter->name())
                    ->code('%regex%', $regexString)
            );
        }
    }

    private function handleParameter(ParameterInterface $parameter): void
    {
        $this->handleParameterDefault($parameter);
        if ($this->parameters->isOptional($parameter->name())) {
            return;
        }
        if (!$this->has($parameter->name())) {
            $this->errors[] = (new Message('Parameter %name%: Missing required argument of type %type%'))
                ->code('%type%', $parameter->type()->typeHinting())
                ->code('%name%', $parameter->name());

            return;
        }
        try {
            $this->assertType($parameter->name(), $this->get($parameter->name()));
        } catch (Throwable $e) {
            $this->errors[] = $e->getMessage();
        }
    }

    private function handleParameterDefault(ParameterInterface $parameter): void
    {
        if (!$this->has($parameter->name())
            && $parameter instanceof StringParameterInterface
            && $parameter->default() !== ''
        ) {
            $this->arguments[$parameter->name()] = $parameter->default();
        }
    }
}
