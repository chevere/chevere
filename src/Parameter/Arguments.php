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
use Chevere\Parameter\Interfaces\GenericInterface;
use Chevere\Parameter\Interfaces\ParameterInterface;
use Chevere\Parameter\Interfaces\ParametersInterface;
use Chevere\Parameter\Traits\ArgumentsGetTypedTrait;
use Chevere\Throwable\Errors\ArgumentCountError;
use Chevere\Throwable\Errors\TypeError;
use Chevere\Throwable\Exceptions\InvalidArgumentException;
use Chevere\Throwable\Exceptions\OutOfBoundsException;
use Throwable;

final class Arguments implements ArgumentsInterface
{
    use ArgumentsGetTypedTrait;

    /**
     * @var array<int|string, mixed>
     */
    private array $arguments;

    /**
     * @var string[]
     */
    private array $errors;

    public function __construct(
        private ParametersInterface $parameters,
        mixed ...$argument
    ) {
        $this->arguments = [];
        if ($parameters instanceof GenericInterface) {
            try {
                assertGenericArgument($parameters->parameter(), $argument);
            } catch(Throwable $e) {
                $message = strstr($e->getMessage(), ':', false) ?: '';
                $message = substr($message, 2);

                throw new InvalidArgumentException(
                    message($message)
                );
            }
            $this->arguments = $argument;

            return;
        }
        $this->processArguments($argument);
        $this->assertRequired();
        $this->errors = [];
        foreach ($this->parameters as $name => $parameter) {
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
     * @throws OutOfBoundsException
     * @throws TypeError
     * @throws InvalidArgumentException
     */
    public function withPut(mixed ...$value): ArgumentsInterface
    {
        $new = clone $this;
        foreach ($value as $name => $item) {
            $name = strval($name);
            $new->assertType($name, $item);
            $new->arguments[$name] = $item;
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

    private function assertRequired(): void
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
    }

    private function assertType(string $name, mixed $argument): void
    {
        $parameter = $this->parameters->get($name);
        $type = $parameter->type();
        if (! $type->validate($argument)) {
            throw new TypeError(
                message: message('[Property %name%]: Expecting value of type %expected%, %provided% provided')
                    ->withTranslate('%name%', $name)
                    ->withStrong('%expected%', $type->typeHinting())
                    ->withCode('%provided%', get_debug_type($argument))
            );
        }

        try {
            assertParameter($parameter, $argument);
        } catch(Throwable $e) {
            throw new InvalidArgumentException(
                message: message('[Property %name%]: %message%')
                    ->withTranslate('%name%', $name)
                    ->withTranslate('%message%', $e->getMessage())
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
            $this->assertType(
                $name,
                $this->get($name)
            );
        } catch (Throwable $e) {
            $this->errors[] = $e->getMessage();
        }
    }

    private function handleParameterDefault(string $name, ParameterInterface $parameter): void
    {
        if (! $this->has($name)) {
            $this->arguments[$name] = $parameter->default();
        }
    }

    /**
     * @param array<int|string, mixed> $argument
     */
    private function processArguments(array $argument): void
    {
        foreach (array_keys($argument) as $name) {
            $name = strval($name);

            if (! $this->parameters()->has($name)) {
                unset($argument[$name]);

                continue;
            }
            $this->arguments[$name] = $argument[$name];
        }
    }
}
