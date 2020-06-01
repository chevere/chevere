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
use Chevere\Exceptions\Controller\ControllerArgumentsRequiredException;
use Chevere\Exceptions\Core\OutOfBoundsException;
use Chevere\Interfaces\Controller\ControllerArgumentsInterface;
use Chevere\Interfaces\Controller\ControllerParameterInterface;
use Chevere\Interfaces\Controller\ControllerParametersInterface;
use Exception;

final class ControllerArguments implements ControllerArgumentsInterface
{
    private ControllerParameters $parameters;

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

    public function arguments(): array
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

    /**
     * @throws OutOfBoundsException
     */
    public function get(string $name): string
    {
        try {
            return $this->arguments[$name];
        } catch (Exception $e) {
            throw new OutOfBoundsException;
        }
    }

    private function assertParameter(string $name, string $value): void
    {
        if ($this->parameters->hasParameterName($name) === false) {
            throw new OutOfBoundsException(
                (new Message('Unknown parameter %parameter%'))
                    ->code('%parameter%', $name)
            );
        }
        $parameter = $this->parameters->get($name);
        $regexString = $parameter->regex()->toString();
        if (preg_match($regexString, $value) !== 1) {
            throw new ControllerArgumentRegexMatchException(
                (new Message("Argument for parameter %parameter% doesn't match the regex %regex%"))
                    ->code('%parameter%', $name)
                    ->code('%regex%', $regexString)
            );
        }
    }

    /**
     * @throws ControllerArgumentsRequiredException
     */
    private function assertRequired(): void
    {
        $failed = [];
        /**
         * @var string $name
         * @var ControllerParameterInterface $parameter
         */
        foreach ($this->parameters->map() as $name => $parameter) {
            if (
                $parameter->isRequired()
                && $this->has($name) === false
            ) {
                $failed[] = $name;
            }
        }
        if ($failed !== []) {
            throw new ControllerArgumentsRequiredException(
                (new Message('Missing required argument(s): %message%'))
                    ->code('%message%', implode(', ', $failed))
            );
        }
    }
}
