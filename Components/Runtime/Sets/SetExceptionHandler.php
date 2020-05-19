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

namespace Chevere\Components\Runtime\Sets;

use Chevere\Components\Runtime\Interfaces\Sets\SetExceptionHandlerInterface;
use Chevere\Components\Runtime\Traits\SetTrait;

/**
 * Sets and restores the exception handler using `set_exception_handler` and `restore_exception_handler`
 */
final class SetExceptionHandler extends SetAbstractHandler implements SetExceptionHandlerInterface
{
    use SetTrait;

    public function getSetHandler(): callable
    {
        return 'set_exception_handler';
    }

    public function getRestoreHandler(): callable
    {
        return 'restore_exception_handler';
    }
}
