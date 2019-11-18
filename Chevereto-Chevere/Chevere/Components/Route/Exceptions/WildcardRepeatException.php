<?php

/*
 * This file is part of Chevere.
 *
 * (c) Rodolfo Berrios <rodolfo@chevereto.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Chevere\Components\Route\Exceptions;

use Exception;

/**
 * Exception thrown when a wildcard is repeated like in a WildcardCollectionContract or in a PathUriWildcardsContract.
 */
final class WildcardRepeatException extends Exception
{
}
