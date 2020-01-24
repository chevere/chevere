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

namespace Chevere\Components\ExceptionHandler\Interfaces;

use Psr\Log\LogLevel;

interface ExceptionInterface
{
    const DEFAULT_ERROR_TYPE = E_ERROR;

    /** @var string[] Readable PHP error mapping */
    const ERROR_TYPES = [
        E_ERROR => 'Fatal error',
        E_WARNING => 'Warning',
        E_PARSE => 'Parse error',
        E_NOTICE => 'Notice',
        E_CORE_ERROR => 'Core error',
        E_CORE_WARNING => 'Core warning',
        E_COMPILE_ERROR => 'Compile error',
        E_COMPILE_WARNING => 'Compile warning',
        E_USER_ERROR => 'Fatal error',
        E_USER_WARNING => 'Warning',
        E_USER_NOTICE => 'Notice',
        E_STRICT => 'Strict standars',
        E_RECOVERABLE_ERROR => 'Recoverable error',
        E_DEPRECATED => 'Deprecated',
        E_USER_DEPRECATED => 'Deprecated',
    ];

    /** @var string[] PHP error code LogLevel table. Stripped from Monolog\ErrorHandler::defaultErrorLevelMap */
    const ERROR_LEVELS = [
        E_ERROR => LogLevel::CRITICAL,
        E_WARNING => LogLevel::WARNING,
        E_PARSE => LogLevel::ALERT,
        E_NOTICE => LogLevel::NOTICE,
        E_CORE_ERROR => LogLevel::CRITICAL,
        E_CORE_WARNING => LogLevel::WARNING,
        E_COMPILE_ERROR => LogLevel::ALERT,
        E_COMPILE_WARNING => LogLevel::WARNING,
        E_USER_ERROR => LogLevel::ERROR,
        E_USER_WARNING => LogLevel::WARNING,
        E_USER_NOTICE => LogLevel::NOTICE,
        E_STRICT => LogLevel::NOTICE,
        E_RECOVERABLE_ERROR => LogLevel::ERROR,
        E_DEPRECATED => LogLevel::NOTICE,
        E_USER_DEPRECATED => LogLevel::NOTICE,
    ];

    /**
     * Provides access to the exception class name.
     */
    public function className(): string;

    /**
     * Provides access to the exception code.
     */
    public function code(): int;

    /**
     * Provides access to the exception severity.
     */
    public function severity(): int;

    /**
     * Provides access to the exception logger level.
     */
    public function loggerLevel(): string;

    /**
     * Provides access to the exception type.
     */
    public function type(): string;

    /**
     * Provides access to the exception message.
     */
    public function message(): string;

    /**
     * Provides access to the exception file.
     */
    public function file(): string;

    /**
     * Provides access to the exception line.
     */
    public function line(): int;

    /**
     * Provides access to the exception trace.
     */
    public function trace(): array;
}
