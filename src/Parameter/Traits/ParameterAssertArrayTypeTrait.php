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
use Chevere\Throwable\Exceptions\InvalidArgumentException;
use Chevere\Throwable\Exceptions\OutOfBoundsException;
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
                message('Expecting no parameters, %provided% provided')
                    ->withCode('%provided%', strval($providedCount))
            );
        }
        foreach ($this->parameters as $name => $item) {
            try {
                $tryParameter = $parameter->parameters()->get($name);
            } catch (OutOfBoundsException) {
                throw new OutOfBoundsException(
                    message('Parameter %name% not found')
                        ->withCode('%name%', $name)
                );
            }

            try {
                $item->assertCompatible($tryParameter);
            } catch (\TypeError) {
                throw new InvalidArgumentException(
                    message('Parameter %name% of type %type% is not compatible with type %provided%')
                        ->withCode('%name%', $name)
                        ->withCode('%type%', $item::class)
                        ->withCode('%provided%', $tryParameter::class)
                );
            }
        }
    }
}
