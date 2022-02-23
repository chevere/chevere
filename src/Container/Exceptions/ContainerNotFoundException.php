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

namespace Chevere\Container\Exceptions;

use Exception;
use Psr\Container\NotFoundExceptionInterface;

/**
 * Exception thrown when no entry was found in the container.
 */
final class ContainerNotFoundException extends Exception implements NotFoundExceptionInterface
{
}
