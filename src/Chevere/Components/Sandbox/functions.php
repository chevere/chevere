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

namespace Chevere\Components\Sandbox;

use Chevere\Components\ThrowableHandler\ThrowableHandler;
use function Chevere\Components\VarDump\varDumpHtml;
use Chevere\Components\VarDump\VarDumpInstance;
use function Chevere\Components\Writer\streamFor;
use Chevere\Components\Writer\StreamWriter;
use Chevere\Components\Writer\Writers;
use Chevere\Components\Writer\WritersInstance;

/**
 * @codeCoverageIgnore
 */
function sandbox(): void
{
    new WritersInstance(
        (new Writers())
            ->with(
                new StreamWriter(
                    streamFor('php://output', 'w')
                )
            )
    );
    set_error_handler(ThrowableHandler::ERRORS_AS_EXCEPTIONS);
    if (PHP_SAPI === 'cli') {
        set_exception_handler(ThrowableHandler::CONSOLE_HANDLER);
    } else {
        new VarDumpInstance(varDumpHtml());
        set_exception_handler(ThrowableHandler::HTML_HANDLER);
    }
    register_shutdown_function(ThrowableHandler::FATAL_ERROR_HANDLER);
}
