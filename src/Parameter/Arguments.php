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

    /**
     * @param array<int|string, mixed> $arguments
     */
    public function __construct(
        private ParametersInterface $parameters,
        array $arguments
    ) {
        $this->arguments = [];
        $this->processArguments($arguments);
        $this->assertNoArgumentsOverflow();
        $this->errors = [];
        $this->handleDefaults();
        $this->assertRequired();
        $this->handleParameters();
        if ($this->errors !== []) {
            throw new InvalidArgumentException(
                message(
                    implode(', ', $this->errors)
                )
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
        return array_key_exists($name, $this->arguments);
    }

    public function get(string $name): mixed
    {
        if ($this->has($name)) {
            return $this->arguments[$name];
        }

        throw new OutOfBoundsException(
            message('Argument %name% not found')
                ->withCode('%name%', $name),
        );
    }

    private function assertNoArgumentsOverflow(): void
    {
        $overflow = array_diff(
            array_keys($this->arguments),
            $this->parameters()->keys()
        );
        if ($overflow !== []) {
            throw new ArgumentCountError(
                message('Invalid argument(s) provided: %extra%')
                    ->withCode('%extra%', implode(', ', $overflow))
            );
        }
    }

    private function handleDefaults(): void
    {
        foreach ($this->parameters as $name => $parameter) {
            if (! $this->has($name) && ($parameter->default() !== null)) {
                $this->arguments[$name] = $parameter->default();
            }
        }
    }

    private function assertRequired(): void
    {
        $missing = array_diff(
            $this->parameters->requiredKeys(),
            array_keys($this->arguments),
        );
        if ($missing !== []) {
            throw new ArgumentCountError(
                message('Missing required argument(s): %missing%')
                    ->withCode('%missing%', implode(', ', $missing))
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
            $this->arguments[$name] = assertArgument($parameter, $argument);
        } catch (Throwable $e) {
            throw new InvalidArgumentException(
                message: message('[Property %name%]: %message%')
                    ->withTranslate('%name%', $name)
                    ->withTranslate('%message%', $e->getMessage())
            );
        }
    }

    private function handleParameters(): void
    {
        foreach ($this->parameters->keys() as $name) {
            if ($this->isSkipOptional($name)) {
                continue;
            }

            try {
                $this->assertType($name, $this->get($name));
            } catch (Throwable $e) {
                $this->errors[] = $e->getMessage();
            }
        }
    }

    /**
     * @infection-ignore-all
     */
    private function isSkipOptional(string $name): bool
    {
        return ! $this->has($name) && $this->parameters->isOptional($name);
    }

    /**
     * @param array<int|string, mixed> $arguments
     */
    private function processArguments(array $arguments): void
    {
        $keys = array_keys($arguments);
        foreach ($keys as $name) {
            $name = strval($name);
            $this->arguments[$name] = $arguments[$name];
        }
    }
}
