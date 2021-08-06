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

namespace Chevere\Exceptions\Var;

use Chevere\Exceptions\Core\LogicException;

/**
 * Exception thrown when a `$var` can't be stored.
 */
final class VarStorableException extends LogicException
{
}
