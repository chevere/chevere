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

namespace Chevere\Controller;

use Chevere\Action\Action;
use Chevere\Controller\Interfaces\ControllerInterface;
use function Chevere\Message\message;
use Chevere\Parameter\Interfaces\StringParameterInterface;
use Chevere\Throwable\Exceptions\InvalidArgumentException;

abstract class Controller extends Action implements ControllerInterface
{
    protected function assertRunParameters(): void
    {
        $invalid = [];
        foreach ($this->parameters()->getIterator() as $name => $parameter) {
            if (!($parameter instanceof StringParameterInterface)) {
                $invalid[] = $name;
            }
        }
        if ($invalid !== []) {
            throw new InvalidArgumentException(
                message('Parameter %parameters% must be of type %type% for controller %className%.')
                    ->withCode('%parameters%', implode(', ', $invalid))
                    ->withStrong('%type%', 'string')
                    ->withStrong('%className%', static::class)
            );
        }
    }
}
