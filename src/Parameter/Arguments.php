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

use function Chevere\Message\message;
use Chevere\Parameter\Interfaces\ArgumentsInterface;
use Chevere\Parameter\Interfaces\ParameterInterface;
use Chevere\Parameter\Interfaces\ParametersInterface;
use Chevere\Parameter\Interfaces\StringParameterInterface;
use Chevere\Throwable\Errors\ArgumentCountError;
use Chevere\Throwable\Errors\TypeError;
use Chevere\Throwable\Exceptions\InvalidArgumentException;
use Chevere\Throwable\Exceptions\OutOfBoundsException;
use Throwable;

final class Arguments implements ArgumentsInterface
{
    /**
     * @var array<string, mixed>
     */
    public array $arguments;

    /**
     * @var string[]
     */
    private array $errors;

    public function __construct(
        private ParametersInterface $parameters,
        mixed ...$namedArguments
    ) {
        /** @var array<string, mixed> $namedArguments */
        $this->arguments = $namedArguments;
        $this->assertCount();
        $this->errors = [];
        foreach ($this->parameters->getIterator() as $name => $parameter) {
            $this->handleParameter($name, $parameter);
        }
        if ($this->errors !== []) {
            throw new InvalidArgumentException(
                message(implode(', ', $this->errors))
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

    /**
     *
     * @throws OutOfBoundsException
     * @throws TypeError
     * @throws InvalidArgumentException
     */
    public function withArgument(mixed ...$nameValue): ArgumentsInterface
    {
        $new = clone $this;
        foreach ($nameValue as $name => $value) {
            $name = strval($name);
            $new->assertType($name, $value);
            $new->arguments[$name] = $value;
        }

        return $new;
    }

    public function has(string $name): bool
    {
        return isset($this->arguments[$name]);
    }

    public function get(string $name): mixed
    {
        if (array_key_exists($name, $this->arguments)) {
            return $this->arguments[$name];
        }

        throw new OutOfBoundsException(
            message('Argument %name% not found')
                ->withCode('%name%', $name),
        );
    }

    public function getBoolean(string $name): bool
    {
        /** @var bool */
        return $this->get($name);
    }

    public function getString(string $name): string
    {
        /** @var string */
        return $this->get($name);
    }

    public function getInteger(string $name): int
    {
        /** @var int */
        return $this->get($name);
    }

    public function getFloat(string $name): float
    {
        /** @var float */
        return $this->get($name);
    }

    public function getArray(string $name): array
    {
        /** @var array<mixed, mixed> */
        return $this->get($name);
    }

    private function assertCount(): void
    {
        $argumentsKeys = array_keys($this->arguments);
        $diffRequired = array_diff(
            $this->parameters->required(),
            $argumentsKeys
        );
        if ($diffRequired !== []) {
            throw new ArgumentCountError(
                message('Missing required argument(s): %missing%')
                    ->withCode('%missing%', implode(', ', $diffRequired))
            );
        }
        $diffExtra = array_diff(
            $argumentsKeys,
            $this->parameters->keys()
        );
        if ($diffExtra !== []) {
            throw new ArgumentCountError(
                message('Passing extra arguments: %extra%')
                    ->withCode('%extra%', implode(', ', $diffExtra))
            );
        }
    }

    private function assertType(string $name, mixed $value): void
    {
        $parameter = $this->parameters->get($name);
        $type = $parameter->type();
        if (!$type->validate($value)) {
            throw new TypeError(
                message: message('Parameter %name%: Expecting value of type %expected%, %provided% provided')
                    ->withStrong('%name%', $name)
                    ->withStrong('%expected%', $type->typeHinting())
                    ->withCode('%provided%', get_debug_type($value))
            );
        }
        if ($parameter instanceof StringParameterInterface) {
            /**
             * @var StringParameterInterface $parameter
             * @var string $value
             */
            $this->assertStringArgument($name, $parameter, $value);
        }
    }

    /**
     * @throws InvalidArgumentException
     */
    private function assertStringArgument(string $name, StringParameterInterface $parameter, string $argument): void
    {
        $regexString = $parameter->regex()->__toString();
        if (preg_match($regexString, $argument) !== 1) {
            throw new InvalidArgumentException(
                message("Parameter [%name%]: Argument value provided doesn't match the regex %regex%")
                    ->withStrong('%name%', $name)
                    ->withCode('%parameter%', $name)
                    ->withCode('%regex%', $regexString)
            );
        }
    }

    private function handleParameter(string $name, ParameterInterface $parameter): void
    {
        $this->handleParameterDefault($name, $parameter);
        if ($this->parameters->isOptional($name)) {
            return;
        }

        try {
            $this->assertType($name, $this->get($name));
        } catch (Throwable $e) {
            $this->errors[] = $e->getMessage();
        }
    }

    private function handleParameterDefault(string $name, ParameterInterface $parameter): void
    {
        if (!$this->has($name)) {
            $this->arguments[$name] = $parameter->default();
        }
    }
}
