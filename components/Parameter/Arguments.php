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
use Chevere\Exceptions\Core\InvalidArgumentException;
use Chevere\Exceptions\Core\OutOfBoundsException;
use Chevere\Exceptions\Parameter\ArgumentRegexMatchException;
use Chevere\Exceptions\Parameter\ArgumentRequiredException;
use Chevere\Interfaces\Parameter\ArgumentsInterface;
use Chevere\Interfaces\Parameter\ParameterInterface;
use Chevere\Interfaces\Parameter\ParametersInterface;
use Chevere\Interfaces\Parameter\StringParameterInterface;
use Throwable;

final class Arguments implements ArgumentsInterface
{
    private ParametersInterface $parameters;

    private array $arguments;

    private array $missing;

    public function __construct(ParametersInterface $parameters, array $arguments)
    {
        $this->parameters = $parameters;
        $this->arguments = $arguments;
        $this->missing = [];
        foreach ($this->parameters->getGenerator() as $parameter) {
            $this->handleParameter($parameter);
        }
        if ($this->missing !== []) {
            throw new ArgumentRequiredException(
                (new Message('Missing required argument(s): %message%'))
                    ->code('%message%', implode(', ', $this->missing))
            );
        }

        foreach ($this->arguments as $name => $value) {
            $this->assertArguments($name, $value);
            $this->assertParameter($name, $value);
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

    public function withArgument(string $name, string $value): ArgumentsInterface
    {
        $this->assertParameter($name, $value);
        $new = clone $this;
        $new->arguments[$name] = $value;

        return $new;
    }

    public function has(string $name): bool
    {
        return isset($this->arguments[$name]);
    }

    public function get(string $name): string
    {
        try {
            return $this->arguments[$name];
        } catch (Throwable $e) {
            throw new OutOfBoundsException(
                (new Message('Argument %name% not found'))
                    ->code('%name%', $name)
            );
        }
    }

    private function assertArguments($name, $value): void
    {
        foreach (['name', 'value'] as $argumentName) {
            if (!is_string($$argumentName)) {
                throw new InvalidArgumentException(
                    (new Message('Argument %argumentName% for parameter %parameterName% must be of type string'))
                        ->code('%argumentName%', $argumentName)
                        ->code('%parameterName%', $this->parameters->get($name)->name())
                );
            }
        }
    }

    private function assertParameter(string $name, string $argument): void
    {
        if ($this->parameters->has($name) === false) {
            throw new OutOfBoundsException(
                (new Message('Parameter %parameter% not found'))
                    ->code('%parameter%', $name)
            );
        }
        $parameter = $this->parameters->get($name);
        if ($parameter instanceof StringParameterInterface) {
            /**
             * @var StringParameterInterface $parameter
             */
            $this->assertStringParameter($parameter, $argument);
        }
    }

    private function assertStringParameter(StringParameterInterface $parameter, string $argument): void
    {
        $regexString = $parameter->regex()->toString();
        if (preg_match($regexString, $argument) !== 1) {
            throw new ArgumentRegexMatchException(
                (new Message("Argument %argument% provided for parameter %parameter% doesn't match the regex %regex%"))
                    ->code('%argument%', $argument)
                    ->code('%parameter%', $parameter->name())
                    ->code('%regex%', $regexString)
            );
        }
    }

    private function handleParameter(ParameterInterface $parameter): void
    {
        if (
            !$this->has($parameter->name())
            && $parameter instanceof StringParameterInterface
            && $parameter->default() !== ''
        ) {
            $this->arguments[$parameter->name()] = $parameter->default();
        }
        if ($this->parameters->isOptional($parameter->name())) {
            return;
        }
        if (!$this->has($parameter->name())) {
            $this->missing[] = $parameter->name();
        }
    }
}
