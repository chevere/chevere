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

namespace Chevere\Components\Var;

use DeepCopy\DeepCopy;
use DeepCopy\Exception\CloneException;

/**
 * Deep copies the given value. Same as `DeepCopy\deep_copy`
 * but it adds a breadcrumbs for debugging.
 */
function deepCopy(mixed $value, bool $useCloneMethod = false)
{
    try {
        return (new DeepCopy($useCloneMethod))
            ->copy($value);
    } catch (CloneException $e) {
        if (is_object($value)) {
            new ObjectClonable($value);
        }

        throw $e;
    }
}
