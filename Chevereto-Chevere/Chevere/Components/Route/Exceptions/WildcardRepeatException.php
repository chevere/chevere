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

namespace Chevere\Components\Route\Exceptions;

use Exception;

/**
 * Exception thrown when a wildcard is repeated like in a WildcardCollectionInterface or in a PathUriWildcardsInterface.
 */
final class WildcardRepeatException extends Exception
{
}
