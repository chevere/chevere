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

namespace Chevere\Exceptions\VarSupport;

use Chevere\Exceptions\Core\LogicException;

/**
 * Exception thrown when failing to provide a clonable object.
 */
final class ObjectClonableException extends LogicException
{
}
