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

namespace Chevere\VariableSupport;

use Chevere\Throwable\Exceptions\LogicException;
use DeepCopy\DeepCopy;
use DeepCopy\Exception\CloneException;
use function Chevere\Message\message;

/**
 * Deep copies the given value. Same as `DeepCopy\deep_copy`
 * but it adds a breadcrumbs to the exception message.
 */
function deepCopy(mixed $value, bool $useCloneMethod = false): mixed
{
    try {
        return (new DeepCopy($useCloneMethod))->copy($value);
    } catch (CloneException $e) {
        if (is_object($value)) {
            $object = new ObjectVariable($value);
            $object->assertClonable();
        }
        // @codeCoverageIgnoreStart
        throw new LogicException(
            message: message($e->getMessage())
        );
        // @codeCoverageIgnoreEnd
    }
}
