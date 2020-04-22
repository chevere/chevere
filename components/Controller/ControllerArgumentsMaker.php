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

use Chevere\Components\Controller\Exceptions\ControllerArgumentRegexException;
use Chevere\Components\Controller\Exceptions\ControllerArgumentRequiredException;
use Chevere\Components\Controller\Interfaces\ControllerArgumentsInterface;
use Chevere\Components\Controller\Interfaces\ControllerParameterInterface;
use Chevere\Components\Controller\Interfaces\ControllerParametersInterface;
use Chevere\Components\Message\Message;
use Ds\Map;
use Ds\Set;
use InvalidArgumentException;
use LogicException;
use OutOfBoundsException;

final class ControllerArgumentsMaker
{
    private ControllerParametersInterface $parameters;

    private ControllerArgumentsInterface $arguments;

    private Set $required;

    /**
     * @param array $array [<string>name => <string>value,]
     * @throws ControllerArgumentRegexException
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
            throw new ControllerArgumentRegexException(
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

    public function assertRequired(): void
    {
        $failed = [];
        /**
         * @var string $name
         */
        foreach ($this->required as $name) {
            if ($this->arguments->has($name)) {
                $failed[] = $name;
            }
        }
        if ($failed !== []) {
            throw new ControllerArgumentRequiredException(
                (new Message('Missing required argument(s): %message%'))
                    ->implodeTag('%message%', 'code', $failed)
                    ->toString()
            );
        }
    }

    public function arguments(): ControllerArgumentsInterface
    {
        return $this->arguments;
    }
}
