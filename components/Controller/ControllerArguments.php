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

use Chevere\Components\Controller\Exceptions\ControllerArgumentRegexMatchException;
use Chevere\Components\Controller\Exceptions\ControllerArgumentsRequiredException;
use Chevere\Components\Controller\Interfaces\ControllerArgumentsInterface;
use Chevere\Components\Controller\Interfaces\ControllerParameterInterface;
use Chevere\Components\Message\Message;
use Ds\Map;
use OutOfBoundsException;

final class ControllerArguments implements ControllerArgumentsInterface
{
    private ControllerParameters $parameters;

    private Map $arguments;

    public function __construct(ControllerParameters $parameters, array $arguments)
    {
        $this->parameters = $parameters;
        $this->arguments = new Map($arguments);
        $this->assertRequired();
        foreach ($this->arguments as $name => $value) {
            $this->assertParameter($name, $value);
        }
    }

    public function with(string $name, string $value): ControllerArgumentsInterface
    {
        $this->assertParameter($name, $value);
        $new = clone $this;
        $new->arguments->put($name, $value);

        return $new;
    }

    public function has(string $name): bool
    {
        /** @var \Ds\TKey $key */
        $key = $name;

        return $this->arguments->hasKey($key);
    }

    /**
     * @throws OutOfBoundsException
     */
    public function get(string $name): string
    {
        /**
         * @var \Ds\TKey $name
         * @var string $return
         */
        $return = $this->arguments->get($name);

        return $return;
    }

    private function assertParameter(string $name, string $value): void
    {
        if ($this->parameters->hasName($name) === false) {
            throw new OutOfBoundsException(
                (new Message('Unknown parameter %parameter%'))
                    ->code('%parameter%', $name)
                    ->toString()
            );
        }
        $parameter = $this->parameters->get($name);
        $regexString = $parameter->regex()->toString();
        if (preg_match($regexString, $value) !== 1) {
            throw new ControllerArgumentRegexMatchException(
                (new Message("Argument for parameter %parameter% doesn't match the regex %regex%"))
                    ->code('%parameter%', $name)
                    ->code('%regex%', $regexString)
                    ->toString()
            );
        }
        $this->arguments->put($name, $value);
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
                && $this->arguments->hasKey($name) === false
            ) {
                $failed[] = $name;
            }
        }
        if ($failed !== []) {
            throw new ControllerArgumentsRequiredException(
                (new Message('Missing required argument(s): %message%'))
                    ->implodeTag('%message%', 'code', $failed)
                    ->toString()
            );
        }
    }
}
