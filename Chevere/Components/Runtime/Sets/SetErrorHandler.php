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

use Chevere\Components\Runtime\Interfaces\Sets\SetErrorHandlerInterface;

/**
 * Sets and restores the error handler using `set_error_handler` and `restore_error_handler`
 */
final class SetErrorHandler extends SetAbstractHandler implements SetErrorHandlerInterface
{
    public function getSetHandler(): callable
    {
        return 'set_error_handler';
    }

    public function getRestoreHandler(): callable
    {
        return 'restore_error_handler';
    }
}
