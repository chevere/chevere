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

use Chevere\Components\Controller\Exceptions\ControllerArgumentCountException;
use Chevere\Components\Controller\Exceptions\ControllerArgumentKeyNotExistsException;
use Chevere\Components\Controller\Exceptions\ControllerArgumentRegexException;
use Chevere\Components\Controller\Interfaces\ControllerArgumentsInterface;
use Chevere\Components\Controller\Interfaces\ControllerParameterInterface;
use Chevere\Components\Controller\Interfaces\ControllerParametersInterface;
use Chevere\Components\Message\Message;
use Ds\Map;
use LogicException;
use Throwable;

final class ControllerArgumentsMaker
{
    private ControllerParametersInterface $parameters;

    private Map $map;

    private ControllerArgumentsInterface $arguments;

    /**
     * @param array $array [<string>key => <string>value,]
     * @throws ControllerArgumentCountException
     * @throws ControllerArgumentKeyNotExistsException
     * @throws ControllerArgumentRegexException
     * @throws LogicException if $array doesn't meet the expected types (key=>value)
     */
    public function __construct(ControllerParametersInterface $parameters, array $array)
    {
        $this->parameters = $parameters;
        $this->map = new Map($array);
        $this->assertCount();
        $this->assertMatchingKeys();
        $this->arguments = new ControllerArguments($array);
        $this->assertValues();
    }

    public function arguments(): ControllerArgumentsInterface
    {
        return $this->arguments;
    }

    private function assertCount(): void
    {
        $expected = $this->parameters->map()->count();
        $count = $this->map->count();
        if ($expected !== $count) {
            throw new ControllerArgumentCountException(
                (new Message('Expecting %expected% argument(s), %count% argument(s) passed'))
                    ->code('%expected%', (string) $expected)
                    ->code('%count%', (string) $count)
                    ->toString()
            );
        }
    }

    private function assertMatchingKeys(): void
    {
        $diff = $this->parameters->map()->diff($this->map);
        if ($diff->/** @scrutinizer ignore-call */ isEmpty() === false) {
            throw new ControllerArgumentKeyNotExistsException(
                (new Message('Missing argument key(s): %keysMissing%'))
                    ->implodeTag('%keysMissing%', 'code', $diff->keys()->toArray())
                    ->toString()
            );
        }
    }

    private function assertValues(): void
    {
        $failures = [];
        /** @var ControllerParameterInterface $parameter */
        foreach ($this->parameters->map() as $name => $parameter) {
            if (preg_match($parameter->regex()->toString(), $this->map->get($name)) !== 1) {
                $failures[] = $name . ':' . $parameter->regex()->toString();
            }
        }
        if ($failures !== []) {
            throw new ControllerArgumentRegexException(
                (new Message("Arguments doesn't match regex: %arguments%"))
                    ->implodeTag('%arguments%', 'code', $failures)
                    ->toString()
            );
        }
    }
}
