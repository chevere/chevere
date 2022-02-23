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
use Psr\Container\ContainerExceptionInterface;

/**
 * Exception thrown representing a generic exception in a container.
 */
final class ContainerException extends Exception implements ContainerExceptionInterface
{
}
