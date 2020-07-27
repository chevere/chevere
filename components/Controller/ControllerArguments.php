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

namespace Chevere\Components\Controller;

use Chevere\Components\Message\Message;
use Chevere\Exceptions\Controller\ControllerArgumentRegexMatchException;
use Chevere\Exceptions\Controller\ControllerArgumentRequiredException;
use Chevere\Exceptions\Core\OutOfBoundsException;
use Chevere\Interfaces\Controller\ControllerArgumentsInterface;
use Chevere\Interfaces\Controller\ControllerParameterInterface;
use Chevere\Interfaces\Controller\ControllerParameterOptionalInterface;
use Chevere\Interfaces\Controller\ControllerParametersInterface;
use Throwable;

final class ControllerArguments implements ControllerArgumentsInterface
{
    private ControllerParametersInterface $parameters;

    private array $arguments;

    public function __construct(ControllerParametersInterface $parameters, array $arguments)
    {
        $this->parameters = $parameters;
        $this->arguments = $arguments;
        $this->assertRequired();
        foreach ($this->arguments as $name => $value) {
            $this->assertParameter($name, $value);
        }
    }

    public function toArray(): array
    {
        return $this->arguments;
    }

    public function withArgument(string $name, string $value): ControllerArgumentsInterface
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
                (new Message('Name %name% not found'))
                    ->code('%name%', $name)
            );
        }
    }

    private function assertParameter(string $name, string $argument): void
    {
        if ($this->parameters->hasParameterName($name) === false) {
            throw new OutOfBoundsException(
                (new Message('Parameter %parameter% not found'))
                    ->code('%parameter%', $name)
            );
        }
        $parameter = $this->parameters->get($name);
        $regexString = $parameter->regex()->toString();
        if (preg_match($regexString, $argument) !== 1) {
            throw new ControllerArgumentRegexMatchException(
                (new Message("Argument %argument% provided for parameter %parameter% doesn't match the regex %regex%"))
                    ->code('%argument%', $argument)
                    ->code('%parameter%', $name)
                    ->code('%regex%', $regexString)
            );
        }
    }

    private function assertRequired(): void
    {
        $failed = [];
        foreach ($this->parameters->getGenerator() as $name => $parameter) {
            if (!($parameter instanceof ControllerParameterOptionalInterface) && !$this->has($name)) {
                $failed[] = $name;
            }
        }
        if ($failed !== []) {
            throw new ControllerArgumentRequiredException(
                (new Message('Missing required argument(s): %message%'))
                    ->code('%message%', implode(', ', $failed))
            );
        }
    }
}
