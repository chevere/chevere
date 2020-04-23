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
use Chevere\Components\Controller\Interfaces\ControllerParametersInterface;
use Chevere\Components\Message\Message;
use Ds\Set;
use LogicException;
use OutOfBoundsException;

final class ControllerArgumentsMaker
{
    private ControllerParametersInterface $parameters;

    private ControllerArgumentsInterface $arguments;

    private Set $required;

    /**
     * @param array $array [<string>name => <string>value,]
     * @throws ControllerArgumentRegexMatchException
     * @throws LogicException if $array doesn't meet the expected types (key=>value)
     */
    public function __construct(ControllerParametersInterface $parameters)
    {
        $this->parameters = $parameters;
        $this->arguments = new ControllerArguments($this->parameters);
        $this->required = new Set;
        /**
         * @var ControllerParameterInterface $parameter
         */
        foreach ($this->parameters->map() as $parameter) {
            if ($parameter->isRequired()) {
                $this->required->add($parameter->name());
            }
        }
    }

    public function withBind(string $name, string $value): ControllerArgumentsMaker
    {
        if ($this->parameters->hasParameter($name) === false) {
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
        $new = clone $this;
        $new->arguments->put($name, $value);

        return $new;
    }

    /**
     * @throws ControllerArgumentsRequiredException
     */
    public function arguments(): ControllerArgumentsInterface
    {
        $this->assertRequired();

        return $this->arguments;
    }

    private function assertRequired(): void
    {
        $failed = [];
        /**
         * @var string $name
         */
        foreach ($this->required as $name) {
            if ($this->arguments->has($name) === false) {
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
