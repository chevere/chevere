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

namespace Chevere\Parameter\Traits;

use Chevere\Parameter\Interfaces\ParametersAccessInterface;
use Chevere\Parameter\Interfaces\ParametersInterface;
use InvalidArgumentException;
use OutOfBoundsException;
use function Chevere\Message\message;

trait ParameterAssertArrayTypeTrait
{
    private ParametersInterface $parameters;

    private function assertArrayType(ParametersAccessInterface $parameter): void
    {
        $parametersCount = $this->parameters->count();
        $providedCount = $parameter->parameters()->count();
        if ($parametersCount === 0 && $providedCount !== 0) {
            throw new InvalidArgumentException(
                (string) message(
                    'Expecting no parameters, `%provided%` provided',
                    provided: strval($providedCount)
                )
            );
        }
        foreach ($this->parameters as $name => $item) {
            try {
                $tryParameter = $parameter->parameters()->get($name);
            } catch (OutOfBoundsException) {
                throw new OutOfBoundsException(
                    (string) message(
                        'Parameter `%name%` not found',
                        name: $name
                    )
                );
            }

            try {
                $item->assertCompatible($tryParameter);
            } catch (\TypeError) {
                throw new InvalidArgumentException(
                    (string) message(
                        'Parameter `%name%` of type `%type%` is not compatible with type `%provided%`',
                        name: $name,
                        type: $item::class,
                        provided: $tryParameter::class,
                    )
                );
            }
        }
    }
}
