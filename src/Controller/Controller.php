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
use Chevere\Parameter\Interfaces\StringParameterInterface;
use Chevere\Throwable\Exceptions\InvalidArgumentException;
use function Chevere\Action\getParameters;
use function Chevere\Message\message;

abstract class Controller extends Action implements ControllerInterface
{
    protected static function assertStatic(): void
    {
        $invalid = [];
        foreach (getParameters(static::class) as $name => $parameter) {
            if (! ($parameter instanceof StringParameterInterface)) {
                $invalid[] = $name;
            }
        }
        if ($invalid === []) {
            return;
        }

        throw new InvalidArgumentException(
            message('Parameter %parameters% must be of type %type% for controller %className%.')
                ->withCode('%parameters%', implode(', ', $invalid))
                ->withStrong('%type%', 'string')
                ->withStrong('%className%', static::class)
        );
    }
}
