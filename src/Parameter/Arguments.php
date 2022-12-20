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
use Chevere\Parameter\Interfaces\IntegerParameterInterface;
use Chevere\Parameter\Interfaces\ParameterInterface;
use Chevere\Parameter\Interfaces\ParametersInterface;
use Chevere\Parameter\Interfaces\StringParameterInterface;
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
     * @var array<string, mixed>
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
        $storeArguments = [];
        foreach (array_keys($argument) as $name) {
            $name = strval($name);
            if (! $this->parameters()->has($name)) {
                unset($argument[$name]);

                continue;
            }
            $storeArguments[$name] = $argument[$name];
        }
        $this->arguments = $storeArguments;
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
                message: message('Parameter %name%: Expecting value of type %expected%, %provided% provided')
                    ->withStrong('%name%', $name)
                    ->withStrong('%expected%', $type->typeHinting())
                    ->withCode('%provided%', get_debug_type($argument))
            );
        }
        if ($parameter instanceof StringParameterInterface) {
            /**
             * @var StringParameterInterface $parameter
             * @var string $argument
             */
            $this->assertStringArgument($name, $parameter, $argument);
        }
        if ($parameter instanceof IntegerParameterInterface) {
            /**
             * @var IntegerParameterInterface $parameter
             * @var int $argument
             */
            $this->assertIntegerArgument($name, $parameter, $argument);
        }
    }

    /**
     * @throws InvalidArgumentException
     */
    private function assertStringArgument(
        string $name,
        StringParameterInterface $parameter,
        string $argument
    ): void {
        $regex = $parameter->regex();
        if ($regex->match($argument) === []) {
            throw new InvalidArgumentException(
                message("Parameter [%name%]: Argument value provided %provided% doesn't match the regex %regex%")
                    ->withStrong('%name%', $name)
                    ->withCode('%provided%', $argument)
                    ->withCode('%regex%', strval($regex))
            );
        }
    }

    private function assertIntegerArgument(
        string $name,
        IntegerParameterInterface $parameter,
        int $argument
    ): void {
        $value = $parameter->accept();
        if ($value !== []) {
            if (in_array($argument, $value, true)) {
                return;
            }

            throw new InvalidArgumentException(
                message('Parameter [%name%]: Argument value provided %provided% is not an accepted value %value%')
                    ->withStrong('%name%', $name)
                    ->withCode('%provided%', strval($argument))
                    ->withCode('%value%', implode(',', $value))
            );
        }
        $this->assertIntegerMinimum(
            $name,
            $argument,
            $parameter->minimum() ?? PHP_INT_MIN
        );
        $this->assertIntegerMaximum(
            $name,
            $argument,
            $parameter->maximum() ?? PHP_INT_MAX
        );
    }

    private function assertIntegerMinimum(string $name, int $argument, int $minimum): void
    {
        if ($argument < $minimum) {
            throw new InvalidArgumentException(
                message('Parameter [%name%]: Argument value provided %provided% is less than %minimum%')
                    ->withStrong('%name%', $name)
                    ->withCode('%provided%', strval($argument))
                    ->withCode('%minimum%', strval($minimum))
            );
        }
    }

    private function assertIntegerMaximum(string $name, int $argument, int $maximum): void
    {
        if ($argument > $maximum) {
            throw new InvalidArgumentException(
                message('Parameter [%name%]: Argument value provided %provided% is greater than %maximum%')
                    ->withStrong('%name%', $name)
                    ->withCode('%provided%', strval($argument))
                    ->withCode('%maximum%', strval($maximum))
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
        if (! $this->has($name)) {
            $this->arguments[$name] = $parameter->default();
        }
    }
}
