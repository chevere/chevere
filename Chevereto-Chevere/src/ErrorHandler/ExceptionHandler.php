<?php

declare(strict_types=1);

/*
 * This file is part of Chevere.
 *
 * (c) Rodolfo Berrios <rodolfo@chevereto.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Chevere\ErrorHandler;

use Throwable;
use ErrorException;
use Chevere\Path;
use Chevere\Utility\Str;

// FIXME: Code font (inline) must be smaller

/**
 * Handles the error exception throwable.
 */
final class ExceptionHandler
{
    /** @var Throwable */
    private $exception;

    /** @var string */
    private $className;

    /** @var int */
    private $code;

    /** @var string */
    private $errorType;

    /** @var string */
    private $type;

    /** @var string */
    private $loggerLevel;

    /** @var string */
    private $message;

    /** @var string */
    private $file;

    /** @var int */
    private $line;

    /** @var Throwable $exception */
    public function __construct(Throwable $exception)
    {
        $this->exception = $exception;
        $this->className = get_class($exception);
        if (Str::startsWith('Chevere\\', $this->className)) {
            $this->className = Str::replaceFirst('Chevere\\', null, $this->className);
        }
        if ($exception instanceof ErrorException) {
            /* @scrutinizer ignore-call */
            $phpCode = $exception->getSeverity();
            $this->code = $phpCode;
            $this->errorType = $phpCode;
        } else {
            $phpCode = E_ERROR;
            $this->code = $exception->getCode();
            $this->errorType = $phpCode;
        }
        $this->type = ErrorHandler::ERROR_TABLE[$phpCode];
        $this->loggerLevel = ErrorHandler::PHP_LOG_LEVEL[$phpCode] ?? 'error';
        $this->message = $exception->getMessage();
        $this->file = Path::normalize($exception->getFile());
        $this->line = (int) $exception->getLine();
    }

    public function exception(): Throwable
    {
        return $this->exception;
    }

    public function className(): string
    {
        return $this->className;
    }

    public function code(): int
    {
        return $this->code;
    }

    public function errorType(): string
    {
        return $this->errorType;
    }

    public function type(): string
    {
        return $this->type;
    }

    public function loggerLevel(): string
    {
        return $this->loggerLevel;
    }

    public function message(): string
    {
        return $this->message;
    }

    public function file(): string
    {
        return $this->file;
    }

    public function line(): int
    {
        return $this->line;
    }
}
